<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'inactive_users' => User::where('status', 'inactive')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'players' => User::where('role', 'player')->count(),
            'logs_today' => ActivityLog::whereDate('created_at', today())->count(),
            'failed_logins' => ActivityLog::where('activity', 'like', 'Failed login%')
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
            'total_logs' => ActivityLog::count(),
        ];

        $recentLogs = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $activityByDay = ActivityLog::select(
            DB::raw('DATE(created_at) as day'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $loginsByRole = User::select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->get();

        $activityByType = ActivityLog::select('activity', DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('activity')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $maxActivity = $activityByDay->max('total') ?: 1;

        return view('admin.dashboard', compact(
            'stats',
            'recentLogs',
            'activityByDay',
            'loginsByRole',
            'activityByType',
            'maxActivity'
        ));
    }
}
