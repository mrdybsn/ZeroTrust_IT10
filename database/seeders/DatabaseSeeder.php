<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'fullname' => 'System Administrator',
            'username' => 'admin',
            'email' => 'admin@zerotrust.local',
            'password' => 'Admin@1234',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $players = [
            ['Mardy Besana', 'mardy'],
            ['John Caminoy', 'john'],
            ['Hezelie Diwa', 'hezelie'],
            ['Franzine Eclar', 'franzine'],
            ['Gycel Ucag', 'gycel'],
        ];

        foreach ($players as [$fullname, $username]) {
            User::create([
                'fullname' => $fullname,
                'username' => $username,
                'email' => "{$username}@zerotrust.local",
                'password' => 'Player@123',
                'role' => 'player',
                'status' => 'active',
            ]);
        }

        ActivityLog::create([
            'user_id' => $admin->id,
            'activity' => 'Admin account initialized (Laravel seeder)',
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);

        User::where('username', 'admin')->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => TwoFactorService::generateSecret(),
        ]);
    }
}
