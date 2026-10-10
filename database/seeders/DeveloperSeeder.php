<?php

namespace Database\Seeders;

use App\Models\Developer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DeveloperSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('DEVELOPER_ADMIN_EMAIL');
        $password = env('DEVELOPER_ADMIN_PASSWORD');

        if (blank($email) || blank($password)) {
            throw new RuntimeException(
                'DEVELOPER_ADMIN_EMAIL and DEVELOPER_ADMIN_PASSWORD must be set before running DeveloperSeeder.'
            );
        }

        Developer::firstOrCreate(
            ['email' => $email],
            [
                'name' => env('DEVELOPER_ADMIN_NAME', 'CCOD Developer'),
                'password' => Hash::make($password),
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}
