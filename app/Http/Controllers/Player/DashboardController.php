<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $logs = ActivityLog::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('player.dashboard', compact('logs'));
    }
}
