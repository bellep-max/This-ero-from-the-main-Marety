<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-07-20
 * Time: 17:56.
 */

namespace App\Http\Middleware;

use App\Constants\DefaultConstants;
use App\Models\Ban;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Closure;

class CheckRole
{
    /**
     * Check if user have role access, also check if the site is offline of not.
     *
     * @return mixed|void
     */
    public function handle($request, Closure $next, $permission)
    {
        if (!Group::getValue($permission)) {
            if (config('settings.site_offline')) {
                abort(503, config('settings.offline_reason'));
            } else {
                abort(403);
            }
        }

        if (auth()->user()->banned) {
            $banned = Ban::findOrFail(auth()->id());

            if (Carbon::now()->timestamp >= Carbon::parse($banned->end_at)->timestamp) {
                auth()->user()->update([
                    'banned' => DefaultConstants::FALSE,
                ]);
                auth()->user()->ban()->delete();
            } else {
                abort('403', __('auth.banned', ['banned_reason' => $banned->reason, 'banned_time' => Carbon::parse($banned->end_at)->format('H:i F j Y')]));
            }
        }

        return $next($request);
    }
}
