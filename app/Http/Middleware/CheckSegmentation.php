<?php

namespace BookStack\Http\Middleware;

use BookStack\Users\Models\User;
use Closure;
use Illuminate\Support\Facades\Log;

/**
 * Check that the user's email address is set
 */
class CheckSegmentation
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
        $veryWrongValue = 'very-wrong-value_2346$@##@$@#$@#$@#&&**(())';
        /** @var User $user */
        $user = auth()->user();
        if (auth()->check() && empty($user->email)) {
            Log::debug("no email found for user: " . $user->name, ["user" => $user, "request" => $request->path()]);
            return redirect()->to('/segmentation');
        }
        $keys = ['is_org', 'join_as', 'first_name', 'last_name', 'city', 'region', 'become_member', 'language'];
        foreach ($keys as $key) {
            $val = setting()->getForCurrentUser($key, $veryWrongValue);
            // Log::debug($key, ['val'=> $val, 'isEmpty' => $val === null || $val === '']);
            if ($val === null || $val === '' || $val === $veryWrongValue) return redirect()->to('/segmentation');
        }

        return $next($request);
    }
}
