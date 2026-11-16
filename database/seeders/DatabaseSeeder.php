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

        $email = config('demo.admin_email');
        $password = config('demo.admin_password');

        // Never create a privileged account with predictable default credentials.
        if (blank($email) || blank($password)) {
            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => config('demo.admin_name', 'Demo Administrator'),
                'email_verified_at' => now(),
                'password' => $password,
                'is_admin' => true,
            ],
        );
    }
}
