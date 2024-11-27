<?php

namespace BookStack\Http\Middleware;

use BookStack\Access\EmailConfirmationService;
use BookStack\Users\Models\User;
use Closure;
use Illuminate\Support\Facades\Log;

/**
 * Check that the user's email address is confirmed.
 *
 * As of v21.08 this is technically not required but kept as a prevention
 * to log out any users that may be logged in but in an "awaiting confirmation" state.
 * We'll keep this for a while until it'd be very unlikely for a user to be upgrading from
 * a pre-v21.08 version.
 *
 * Ideally we'd simply invalidate all existing sessions upon update but that has
 * proven to be a lot more difficult than expected.
 */
class CheckEmail
{
    public function __construct()
    {
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
        /** @var User $user */
        $user = auth()->user();
        Log::info("no email found for user: " . $user->name, ["user" => $user, "request" => $request->path()]);
        if (auth()->check() && empty($user->email)) {
            return redirect()->to('/my-account/auth/set-email');
        }

        return $next($request);
    }
}
