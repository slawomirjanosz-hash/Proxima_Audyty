<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OnlineUsersInfo extends Component
{
    public function render()
    {
        // Count unique user_id in sessions table where user_id is not null and last_activity is recent (last 10 minutes)
        $onlineCount = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes(10)->timestamp)
            ->distinct('user_id')
            ->count('user_id');

        $user = Auth::user();

        return view('components.online-users-info', [
            'onlineCount' => $onlineCount,
            'user' => $user,
        ]);
    }
}
