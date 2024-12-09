<?php

namespace BookStack\Http\Middleware;

use BookStack\Access\EmailConfirmationService;
use BookStack\Users\Models\User;
use Illuminate\Support\Facades\Log;
use Closure;

/**
 * Check that the user's email address is confirmed.
 *
 */
class CheckProfile
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
        $UNKNOWN = 'unknown';
        /** @var User $user */
        $user = auth()->user();
        // If the user is an admin, they can access the system regardless.
        if ($user->hasSystemRole('admin')) {
            return $next($request);
        }
        $data = setting()->getUser($user, 'isOrganisation', $UNKNOWN);
        if ($data == $UNKNOWN) {
            Log::debug('CheckProfile', ["user" => $user, "request" => $request->path(), "data" => $data]);
            Log::debug("user roles", ["roles"=> $user->roles()->get()]);
            return redirect()->to('/my-account/');
        }

        return $next($request);
    }
}
