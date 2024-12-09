<?php

namespace BookStack\Http\Middleware;

use BookStack\Translation\LocaleManager;
use Illuminate\Support\Facades\Log;
use Closure;

class Localization
{
    public function __construct(
        protected LocaleManager $localeManager
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $lang = $request->query('lang');
        if (!empty($lang)) {
            $userLocale = $this->localeManager->getLocalDefinition($lang);
            $user = user();
            setting()->putUser($user, 'language', $lang);
        }
        if (!isset($userLocale)) {
            $userLocale = $this->localeManager->getForUser(user());
        }
        // Log::debug('middleware-localization',
        //     ['locale' => $userLocale->appLocale(), $request->url(), 'user' => user(),'userSetting' => setting()->getUser(user(), 'language')]
        // );
        // Share details of the user's locale for use in views
        view()->share('locale', $userLocale);

        // Set locale for system components
        app()->setLocale($userLocale->appLocale());

        return $next($request);
    }
}
