<?php

namespace BookStack\Access;

use BookStack\Exceptions\JsonDebugException;
use BookStack\Exceptions\SamlException;
use BookStack\Exceptions\StoppedAuthenticationException;
use BookStack\Exceptions\UserRegistrationException;
use BookStack\Users\Models\User;
use Exception;
use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Constants;
use OneLogin\Saml2\Error;
use OneLogin\Saml2\IdPMetadataParser;
use OneLogin\Saml2\ValidationError;

/**
 * Class Saml2Service
 * Handles any app-specific SAML tasks.
 */
class Saml2Service
{
    protected array $config;

    public function __construct(
        protected RegistrationService $registrationService,
        protected LoginService $loginService,
        protected GroupSyncService $groupSyncService
    ) {
        $this->config = config('saml2');
    }

    /**
     * Initiate a login flow.
     *
     * @throws Error
     */
    public function login(): array
    {
        $toolKit = $this->getToolkit();
        $returnRoute = url('/saml2/acs');

        return [
            'url' => $toolKit->login($returnRoute, [], false, false, true),
            'id'  => $toolKit->getLastRequestID(),
        ];
    }

    /**
     * Initiate a logout flow.
     * Returns the SAML2 request ID, and the URL to redirect the user to.
     *
     * @throws Error
     * @returns array{url: string, id: ?string}
     */
    public function logout(User $user): array
    {
        $toolKit = $this->getToolkit();
        $sessionIndex = session()->get('saml2_session_index');
        $returnUrl = url($this->loginService->logout());

        try {
            $url = $toolKit->logout(
                $returnUrl,
                [],
                $user->email,
                $sessionIndex,
                true,
                Constants::NAMEID_EMAIL_ADDRESS
            );
            $id = $toolKit->getLastRequestID();
        } catch (Error $error) {
            if ($error->getCode() !== Error::SAML_SINGLE_LOGOUT_NOT_SUPPORTED) {
                throw $error;
            }

            $url = $returnUrl;
            $id = null;
        }

        return ['url' => $url, 'id' => $id];
    }

    /**
     * Process the ACS response from the idp and return the
     * matching, or new if registration active, user matched to the idp.
     * Returns null if not authenticated.
     *
     * @throws Error
     * @throws SamlException
     * @throws ValidationError
     * @throws JsonDebugException
     * @throws UserRegistrationException
     */
    public function processAcsResponse(?string $requestId, string $samlResponse): ?User
    {
        // The SAML2 toolkit expects the response to be within the $_POST superglobal
        // so we need to manually put it back there at this point.
        $_POST['SAMLResponse'] = $samlResponse;
        $toolkit = $this->getToolkit();
        $toolkit->processResponse($requestId);
        $errors = $toolkit->getErrors();

        if (!empty($errors)) {
            $reason = $toolkit->getLastErrorReason();
            $message = 'Invalid ACS Response; Errors: ' . implode(', ', $errors);
            $message .= $reason ? "; Reason: {$reason}" : '';
            throw new Error($message);
        }

        if (!$toolkit->isAuthenticated()) {
            return null;
        }

        $attrs = $toolkit->getAttributes();
        $id = $toolkit->getNameId();
        session()->put('saml2_session_index', $toolkit->getSessionIndex());

        return $this->processLoginCallback($id, $attrs);
    }

    /**
     * Process a response for the single logout service.
     *
     * @throws Error
     */
    public function processSlsResponse(?string $requestId): string
    {
        $toolkit = $this->getToolkit();

        // The $retrieveParametersFromServer in the call below will mean the library will take the query
        // parameters, used for the response signing, from the raw $_SERVER['QUERY_STRING']
        // value so that the exact encoding format is matched when checking the signature.
        // This is primarily due to ADFS encoding query params with lowercase percent encoding while
        // PHP (And most other sensible providers) standardise on uppercase.
        /** @var ?string $samlRedirect */
        $samlRedirect = $toolkit->processSLO(true, $requestId, true, null, true);
        $errors = $toolkit->getErrors();

        if (!empty($errors)) {
            throw new Error(
                'Invalid SLS Response: ' . implode(', ', $errors)
            );
        }

        $defaultBookStackRedirect = $this->loginService->logout();

        return $samlRedirect ?? $defaultBookStackRedirect;
    }

    /**
     * Get the metadata for this service provider.
     *
     * @throws Error
     */
    public function metadata(): string
    {
        $toolKit = $this->getToolkit(true);
        $settings = $toolKit->getSettings();
        $metadata = $settings->getSPMetadata();
        $errors = $settings->validateMetadata($metadata);

        if (!empty($errors)) {
            throw new Error(
                'Invalid SP metadata: ' . implode(', ', $errors),
                Error::METADATA_SP_INVALID
            );
        }

        return $metadata;
    }

    /**
     * Load the underlying Onelogin SAML2 toolkit.
     *
     * @throws Error
     * @throws Exception
     */
    protected function getToolkit(bool $spOnly = false): Auth
    {
        $settings = $this->config['onelogin'];
        $overrides = $this->config['onelogin_overrides'] ?? [];

        if ($overrides && is_string($overrides)) {
            $overrides = json_decode($overrides, true);
        }

        $metaDataSettings = [];
        if (!$spOnly && $this->config['autoload_from_metadata']) {
            $metaDataSettings = IdPMetadataParser::parseRemoteXML($settings['idp']['entityId']);
        }

        $spSettings = $this->loadOneloginServiceProviderDetails();
        $settings = array_replace_recursive($settings, $spSettings, $metaDataSettings, $overrides);

        return new Auth($settings, $spOnly);
    }

    /**
     * Load dynamic service provider options required by the onelogin toolkit.
     */
    protected function loadOneloginServiceProviderDetails(): array
    {
        $spDetails = [
            'entityId'                 => url('/saml2/metadata'),
            'assertionConsumerService' => [
                'url' => url('/saml2/acs'),
            ],
            'singleLogoutService' => [
                'url' => url('/saml2/sls'),
            ],
        ];

        return [
            'baseurl' => url('/saml2'),
            'sp'      => $spDetails,
        ];
    }

    /**
     * Check if groups should be synced.
     */
    protected function shouldSyncGroups(): bool
    {
        return $this->config['user_to_groups'] !== false;
    }

    /**
     * Calculate the display name.
     */
    protected function getUserDisplayName(array $samlAttributes, string $defaultValue): string
    {
        $displayNameAttr = $this->config['display_name_attributes'];

        $displayName = [];
        foreach ($displayNameAttr as $dnAttr) {
            $dnComponent = $this->getSamlResponseAttribute($samlAttributes, $dnAttr, null);
            if ($dnComponent !== null) {
                $displayName[] = $dnComponent;
            }
        }

        if (count($displayName) == 0) {
            $displayName = $defaultValue;
        } else {
            $displayName = implode(' ', $displayName);
        }

        return $displayName;
    }

    /**
     * Get the value to use as the external id saved in BookStack
     * used to link the user to an existing BookStack DB user.
     */
    protected function getExternalId(array $samlAttributes, string $defaultValue)
    {
        $userNameAttr = $this->config['external_id_attribute'];
        if ($userNameAttr === null) {
            return $defaultValue;
        }

        return $this->getSamlResponseAttribute($samlAttributes, $userNameAttr, $defaultValue);
    }

    /**
     * Extract the details of a user from a SAML response.
     *
     * @return array{external_id: string, name: string, email: string, saml_id: string}
     */
    protected function getUserDetails(string $samlID, $samlAttributes): array
    {
        $emailAttr = $this->config['email_attribute'];
        $externalId = $this->getExternalId($samlAttributes, $samlID);

        $defaultEmail = filter_var($samlID, FILTER_VALIDATE_EMAIL) ? $samlID : null;
        $email = $this->getSamlResponseAttribute($samlAttributes, $emailAttr, $defaultEmail);

        return [
            'external_id' => $externalId,
            'name'        => $this->getUserDisplayName($samlAttributes, $externalId),
            'email'       => $email,
            'saml_id'     => $samlID,
            'first_name'  => $this->getSamlResponseAttribute($samlAttributes, 'givenName', ''),
            'last_name'   => $this->getSamlResponseAttribute($samlAttributes, 'surname', ''),
        ];
    }

    /**
     * Get the groups a user is a part of from the SAML response.
     */
    public function getUserGroups(array $samlAttributes): array
    {
        $groupsAttr = $this->config['group_attribute'];
        $userGroups = $samlAttributes[$groupsAttr] ?? null;

        if (!is_array($userGroups)) {
            $userGroups = [];
        }

        return $userGroups;
    }

    /**
     *  For an array of strings, return a default for an empty array,
     *  a string for an array with one element and the full array for
     *  more than one element.
     */
    protected function simplifyValue(array $data, $defaultValue)
    {
        switch (count($data)) {
            case 0:
                $data = $defaultValue;
                break;
            case 1:
                $data = $data[0];
                break;
        }

        return $data;
    }

    /**
     * Get a property from an SAML response.
     * Handles properties potentially being an array.
     */
    protected function getSamlResponseAttribute(array $samlAttributes, string $propertyKey, $defaultValue)
    {
        if (isset($samlAttributes[$propertyKey])) {
            return $this->simplifyValue($samlAttributes[$propertyKey], $defaultValue);
        }

        return $defaultValue;
    }

    /**
     * Process the SAML response for a user. Login the user when
     * they exist, optionally registering them automatically.
     *
     * @throws SamlException
     * @throws JsonDebugException
     * @throws UserRegistrationException
     * @throws StoppedAuthenticationException
     */
    public function processLoginCallback(string $samlID, array $samlAttributes): User
    {
        $userDetails = $this->getUserDetails($samlID, $samlAttributes);
        $isLoggedIn = auth()->check();

        if ($this->shouldSyncGroups()) {
            $userDetails['groups'] = $this->getUserGroups($samlAttributes);
        }

        if ($this->config['dump_user_details']) {
            throw new JsonDebugException([
                'id_from_idp'         => $samlID,
                'attrs_from_idp'      => $samlAttributes,
                'attrs_after_parsing' => $userDetails,
            ]);
        }

        // if ($userDetails['email'] === null) {
        //     throw new SamlException(trans('errors.saml_no_email_address'));
        // }

        if ($isLoggedIn) {
            throw new SamlException(trans('errors.saml_already_logged_in'), '/login');
        }

        $email = $userDetails['email'];

        $user = $this->registrationService->findOrRegister(
            $userDetails['name'],
            isset($email) ? $email : '',
            $userDetails['external_id'],
            !empty($email) && preg_match('/@.*\./', $email)
        );

        if ($this->shouldSyncGroups()) {
            $this->groupSyncService->syncUserWithFoundGroups($user, $userDetails['groups'], $this->config['remove_from_groups']);
        }

        $this->loginService->login($user, 'saml2');

        session()->keep(['saml2_is_download']);

        return $user;
    }
}
