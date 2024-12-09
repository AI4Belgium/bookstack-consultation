<?php

namespace BookStack\Users\Controllers;

use BookStack\Access\SocialDriverManager;
use BookStack\Http\Controller;
use BookStack\Permissions\PermissionApplicator;
use BookStack\Settings\UserNotificationPreferences;
use BookStack\Settings\UserShortcutMap;
use BookStack\Uploads\ImageRepo;
use BookStack\Users\UserRepo;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class UserAccountController extends Controller
{
    public function __construct(
        protected UserRepo $userRepo,
    ) {
        $this->middleware(function (Request $request, Closure $next) {
            $this->preventGuestAccess();
            return $next($request);
        });
    }

    /**
     * Redirect the root my-account path to the main/first category.
     * Required as a controller method, instead of the Route::redirect helper,
     * to ensure the URL is generated correctly.
     */
    public function redirect()
    {
        return redirect('/my-account/profile');
    }

    /**
     * Show the profile form interface.
     */
    public function showProfile()
    {
        $this->setPageTitle(trans('preferences.profile'));

        return view('users.account.profile', [
            'model' => user(),
            'category' => 'profile',
        ]);
    }

    public function showSegmentationForm()
    {
        $this->setPageTitle(trans('preferences.profile'));

        return view('users.account.profile-segmentation', [
            'model' => user(),
            'category' => 'profile',
        ]);
    }

    public function showMyAccountSegmentationForm()
    {
        $this->setPageTitle(trans('preferences.profile'));

        return view('users.account.segmentation', [
            'model' => user(),
            'category' => 'profile',
        ]);
    }

    public function updateSegmentationProfile(Request $request)
    {
        $this->preventAccessInDemoMode();

        session()->keep(['saml2_is_download']);

        $data = $request->all();
        Log::debug('data', $data);

        $user = user();
        $rules = [
            'join_as'                           => ['string', 'in:' . implode(',', array_keys(trans('segmentation.join_as_values')))],
            'first_name'                        => ['string', 'min:2', 'max:100'],
            'last_name'                         => ['string', 'min:2', 'max:100'],
            'email'                             => ['string', 'min:2', 'email', 'unique:users,email,' . $user->id],
            'language'                          => ['string', 'max:15', 'alpha_dash'],
            'country'                           => ['string', 'in:' . implode(',', array_keys(trans('segmentation.country_values')))],
            'region'                            => ['string', 'in:' . implode(',', array_keys(trans('segmentation.region_values')))],
            'city'                              => ['string', 'min:2', 'max:100'],
            'is_expert'                         => ['boolean'],
            'user_profile'                      => ['string', 'required_if:is_org,0' ,'in:' . implode(',', array_keys(trans('segmentation.profile_values')))],
            'is_org'                            => ['boolean', 'required'],
            'become_member'                     => ['boolean'],
            'job_role'                          => ['string', 'required_if_accepted:is_org', 'min:2', 'max:100'],
            'org_name'                          => ['string', 'required_if_accepted:is_org', 'min:2', 'max:200'],
            'founded'                           => ['integer', 'required_if_accepted:is_org', 'min:300', 'max:' . date('Y')],
            'vat'                               => ['string', 'required_if_accepted:is_org', 'regex:/^BE0[1-9][0-9]{7}$|^0[1-9][0-9]{7}$/'],
            'org_profile'                       => ['string', 'required_if_accepted:is_org', 'in:' . implode(',', array_keys(trans('segmentation.organisation.profile_values')))],
            'nb_employees'                      => ['string', 'required_if_accepted:is_org', 'in:' . implode(',', array_keys(trans('segmentation.organisation.number_of_employees_values')))],
            'my_org'                            => ['string', 'required_if_accepted:is_org', 'in:' . implode(',', array_keys(trans('segmentation.my_organisation_values')))],
            'expertise_domain'                  => ['array', 'required_if_accepted:is_org,is_expert'],
            'expertise_domain.*'                => ['in:' . implode(',', array_keys(trans('segmentation.expertise_domain_values')))],
            'non_technical_expertise_domains'   => ['array', 'required_if_accepted:is_org,is_expert'],
            'non_technical_expertise_domains.*' => ['in:' . implode(',', array_keys(trans('segmentation.non_technical_expertise_domains_values')))],
            'application_sectors'               => ['array', 'required_if_accepted:is_org,is_expert'],
            'application_sectors.*'             => ['in:' . implode(',', array_keys(trans('segmentation.application_sectors_values')))],
            'application_fields'                => ['array', 'required_if_accepted:is_org,is_expert'],
            'application_fields.*'              => ['in:' . implode(',', array_keys(trans('segmentation.application_fields_values')))],
            'expertise_status'                  => ['string','required_if_accepted:is_org,is_expert', 'in:' . implode(',', array_keys(trans('segmentation.expertise_status_values')))],
        ];
        // Log::debug('rules', $rules);
        $validated = $this->validate($request, $rules);

        if (!empty($validated['email']) && $validated['email'] !== $user->email) {
            $user->email = $validated['email'];
            $user->save();
        }
        if (!empty($validated['first_name'] && !empty($validated['last_name']))) {
            $user->name = $validated['first_name'] . ' ' . $validated['last_name'];
            $user->save();
        }
        if (!array_key_exists('become_member', $validated)) {
            $validated['become_member'] = 0;
        }
        if (!array_key_exists('is_expert', $validated) && $validated['is_org'] == '0') {
            $validated['is_expert'] = 0;
        }

        foreach ($validated as $k => $v) {
            if ($k === 'email') {
                continue;
            }
            if (is_array($v)) {
                $v = json_encode($v);
            }
            if ($k === 'is_org' && $v === '1') {
                setting()->removeForCurrentUser(key: 'is_expert');
            }
            setting()->putForCurrentUser($k, $v);
        }

        // Log::debug( 'updateSegmentationProfile validated: ', context: $validated);
        // Log::debug('updateSegmentationProfile isDownload: ', [session()->get('saml2_is_download')]);
        return redirect()->intended();
    }

    public function updateLanguage(Request $request, string $language): mixed
    {
        $request->merge(['language' => $language]);
        Log::debug('language request', context: $request->all());
        $user = user();
        $validated = $this->validate($request, [
            'language' => ['string', 'max:15', 'alpha_dash'],
        ]);

        Log::debug('language update', context: $validated);

        $this->userRepo->update($user, $validated, false);

        return Redirect::back();
    }

    /**
     * Handle the submission of the user profile form.
     */
    public function updateProfile(Request $request, ImageRepo $imageRepo)
    {
        $this->preventAccessInDemoMode();

        $user = user();
        $validated = $this->validate($request, [
            'name'             => ['min:2', 'max:100'],
            'email'            => ['min:2', 'email', 'unique:users,email,' . $user->id],
            'language'         => ['string', 'max:15', 'alpha_dash'],
            'profile_image'    => array_merge(['nullable'], $this->getImageValidationRules()),
        ]);

        $this->userRepo->update($user, $validated, userCan('users-manage'));

        // Save profile image if in request
        if ($request->hasFile('profile_image')) {
            $imageUpload = $request->file('profile_image');
            $imageRepo->destroyImage($user->avatar);
            $image = $imageRepo->saveNew($imageUpload, 'user', $user->id);
            $user->image_id = $image->id;
            $user->save();
        }

        // Delete the profile image if reset option is in request
        if ($request->has('profile_image_reset')) {
            $imageRepo->destroyImage($user->avatar);
            $user->image_id = 0;
            $user->save();
        }

        return redirect('/my-account/profile');
    }

    /**
     * Show the user-specific interface shortcuts.
     */
    public function showShortcuts()
    {
        $shortcuts = UserShortcutMap::fromUserPreferences();
        $enabled = setting()->getForCurrentUser('ui-shortcuts-enabled', false);

        $this->setPageTitle(trans('preferences.shortcuts_interface'));

        return view('users.account.shortcuts', [
            'category' => 'shortcuts',
            'shortcuts' => $shortcuts,
            'enabled' => $enabled,
        ]);
    }

    /**
     * Update the user-specific interface shortcuts.
     */
    public function updateShortcuts(Request $request)
    {
        $enabled = $request->get('enabled') === 'true';
        $providedShortcuts = $request->get('shortcut', []);
        $shortcuts = new UserShortcutMap($providedShortcuts);

        setting()->putForCurrentUser('ui-shortcuts', $shortcuts->toJson());
        setting()->putForCurrentUser('ui-shortcuts-enabled', $enabled);

        $this->showSuccessNotification(trans('preferences.shortcuts_update_success'));

        return redirect('/my-account/shortcuts');
    }

    /**
     * Show the notification preferences for the current user.
     */
    public function showNotifications(PermissionApplicator $permissions)
    {
        $this->checkPermission('receive-notifications');

        $preferences = (new UserNotificationPreferences(user()));

        $query = user()->watches()->getQuery();
        $query = $permissions->restrictEntityRelationQuery($query, 'watches', 'watchable_id', 'watchable_type');
        $query = $permissions->filterDeletedFromEntityRelationQuery($query, 'watches', 'watchable_id', 'watchable_type');
        $watches = $query->with('watchable')->paginate(20);

        $this->setPageTitle(trans('preferences.notifications'));
        return view('users.account.notifications', [
            'category' => 'notifications',
            'preferences' => $preferences,
            'watches' => $watches,
        ]);
    }

    /**
     * Update the notification preferences for the current user.
     */
    public function updateNotifications(Request $request)
    {
        $this->preventAccessInDemoMode();
        $this->checkPermission('receive-notifications');
        $data = $this->validate($request, [
           'preferences' => ['required', 'array'],
           'preferences.*' => ['required', 'string'],
        ]);

        $preferences = (new UserNotificationPreferences(user()));
        $preferences->updateFromSettingsArray($data['preferences']);
        $this->showSuccessNotification(trans('preferences.notifications_update_success'));

        return redirect('/my-account/notifications');
    }

    /**
     * Show the view for the "Access & Security" account options.
     */
    public function showAuth(SocialDriverManager $socialDriverManager)
    {
        $mfaMethods = user()->mfaValues()->get()->groupBy('method');

        $this->setPageTitle(trans('preferences.auth'));

        return view('users.account.auth', [
            'category' => 'auth',
            'mfaMethods' => $mfaMethods,
            'authMethod' => config('auth.method'),
            'activeSocialDrivers' => $socialDriverManager->getActive(),
        ]);
    }

    /**
     * Handle the submission for the auth change password form.
     */
    public function updatePassword(Request $request)
    {
        $this->preventAccessInDemoMode();

        if (config('auth.method') !== 'standard') {
            $this->showPermissionError();
        }

        $validated = $this->validate($request, [
            'password'         => ['required_with:password_confirm', Password::default()],
            'password-confirm' => ['same:password', 'required_with:password'],
        ]);

        $this->userRepo->update(user(), $validated, false);

        $this->showSuccessNotification(trans('preferences.auth_change_password_success'));

        return redirect('/my-account/auth');
    }

    /**
     * Handle the submission updating the user email.
     */
    public function updateEmail(Request $request)
    {
        Log::info('updateEmail');
        $this->preventAccessInDemoMode();

        $user = user();

        $validated = $this->validate($request, [
            'email'         => ['min:2', 'email', 'unique:users,email,' . $user->id],
            'email-confirm' => ['same:email', 'required_with:email'],
        ]);

        Log::info('updateEmail validated: ', context: $validated);

        $this->userRepo->update($user, $validated, manageUsersAllowed: empty($user->email));

        $this->showSuccessNotification(trans('preferences.auth_change_password_success'));

        return redirect('/');
    }

    /**
     * Show the user self-delete page.
     */
    public function delete()
    {
        $this->setPageTitle(trans('preferences.delete_my_account'));

        return view('users.account.delete', [
            'category' => 'profile',
        ]);
    }

    /**
     * Remove the current user from the system.
     */
    public function destroy(Request $request)
    {
        $this->preventAccessInDemoMode();

        $requestNewOwnerId = intval($request->get('new_owner_id')) ?: null;
        $newOwnerId = userCan('users-manage') ? $requestNewOwnerId : null;

        $this->userRepo->destroy(user(), $newOwnerId);

        return redirect('/');
    }
}
