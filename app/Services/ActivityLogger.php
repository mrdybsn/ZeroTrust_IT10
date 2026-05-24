<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log(?int $userId, string $activity): void
    {
        ActivityLog::create([
            'user_id' => $userId,
            'activity' => $activity,
            'ip_address' => Request::ip(),
            'created_at' => now(),
        ]);
    }
}
