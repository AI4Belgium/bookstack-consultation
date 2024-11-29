<?php

namespace BookStack\Http\Middleware;

use BookStack\Access\EmailConfirmationService;
use BookStack\Users\Models\User;
use Closure;
use Illuminate\Support\Facades\Log;

/**
 * Check that the user's email address is set
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
        if (auth()->check() && empty($user->email)) {
            Log::debug("no email found for user: " . $user->name, ["user" => $user, "request" => $request->path()]);
            return redirect()->to('/my-account/auth/set-email');
        }

        return $next($request);
    }
}
