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
        // Run platform seeder first to ensure platforms exist
        $this->call(PlatformSeeder::class);

        // Run role and permission seeder
        $this->call(RolePermissionSeeder::class);

        // Run admin seeder
        $this->call(AdminSeeder::class);

        // Create test user with User role
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $testUser->assignRole('User');
    }
}