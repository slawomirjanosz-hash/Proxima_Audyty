<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        if (Auth::check()) {
            $userId = Auth::id();
            $threshold = now()->subMinutes(10)->timestamp;
            $nowTs = now()->timestamp;

            $onlineUsers = Cache::get('online_users_set', []);
            $onlineUsers[$userId] = $nowTs;
            // prune stale entries every request
            $onlineUsers = array_filter($onlineUsers, static fn (int $ts): bool => $ts >= $threshold);

            Cache::put('online_users_set', $onlineUsers, now()->addMinutes(15));
        }

        return $response;
    }
}
