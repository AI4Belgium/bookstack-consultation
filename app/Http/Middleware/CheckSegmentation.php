<?php

namespace BookStack\Http\Middleware;

use BookStack\Entities\Queries\BookQueries;
use BookStack\Users\Models\User;
use Closure;
use Illuminate\Support\Facades\Log;

/**
 * Check that the user's email address is set
 */
class CheckSegmentation
{
    public function __construct(protected BookQueries $bookQueries)
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
        // Log::debug('CheckSegmentation isDownload: ', [session()->get('saml2_is_download')]);
        $veryWrongValue = 'very-wrong-value_2346$@##@$@#$@#$@#&&**(())';
        /** @var User $user */
        $user = auth()->user();
        if (auth()->check() && empty($user->email)) {
            // Log::debug("no email found for user: " . $user->name, ["user" => $user, "request" => $request->path()]);
            return redirect()->to('/segmentation');
        }
        $keys = ['is_org', 'join_as', 'first_name', 'last_name', 'city', 'region', 'become_member', 'language'];
        foreach ($keys as $key) {
            $val = setting()->getForCurrentUser($key, $veryWrongValue);
            // Log::debug($key, ['val'=> $val, 'isEmpty' => $val === null || $val === '']);
            session()->keep(['saml2_is_download']);
            if ($val === null || $val === '' || $val === $veryWrongValue) {
                return redirect()->to('/segmentation');
            }
        }

        // Check if the user is trying to download a book and redirect them to the book download page and remove the session value!!!
        $isDownload = session()->pull('saml2_is_download');

        if ($isDownload) {
            $lang = $user->getLocale()->appLocale();
            $books = $this->bookQueries->visibleForList()
                ->whereRelation('tags', 'name', '=', 'lang')
                ->whereRelation('tags', 'value', '=', $lang)
                ->take(1)
                ->get()->toArray();
            // Log::debug('CheckSegmentation isDownload: ', ['books' => $books]);
            if (isset($books) && count($books) > 0) { // @phpstan-ignore-line
                return redirect()->to('/books/' . $books[0]['slug'] . '/export/pdf');
            }
        }

        return $next($request);
    }
}
