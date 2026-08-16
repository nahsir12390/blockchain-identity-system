<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => env('DEMO_ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => env('DEMO_ADMIN_NAME', 'System Admin'),
                'email_verified_at' => now(),
                'password' => env('DEMO_ADMIN_PASSWORD', 'password'),
                'is_admin' => true,
            ],
        );
    }
}
