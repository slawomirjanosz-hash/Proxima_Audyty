<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class OnlineUsersInfo extends Component
{
    public function render()
    {
        $threshold = now()->subMinutes(10)->timestamp;
        $onlineUsers = Cache::get('online_users_set', []);
        $onlineCount = count(array_filter($onlineUsers, static fn (int $ts): bool => $ts >= $threshold));
        // always at least 1 when the current viewer is logged in
        if (Auth::check() && $onlineCount === 0) {
            $onlineCount = 1;
        }

        $user = Auth::user();

        return view('components.online-users-info', [
            'onlineCount' => $onlineCount,
            'user' => $user,
        ]);
    }
}
