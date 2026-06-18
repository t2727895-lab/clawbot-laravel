<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = [
            [
                'name' => 'LinkedIn',
                'slug' => 'linkedin',
                'description' => 'Professional networking platform',
                'is_active' => true,
            ],
            // [
            //     'name' => 'Facebook',
            //     'slug' => 'facebook',
            //     'description' => 'Social media platform',
            //     'is_active' => true,
            // ],
            // [
            //     'name' => 'Twitter',
            //     'slug' => 'twitter',
            //     'description' => 'Social media platform for quick updates',
            //     'is_active' => true,
            // ],
        ];

        foreach ($platforms as $platform) {
            Platform::firstOrCreate(
                ['slug' => $platform['slug']],
                $platform
            );
        }
    }
}
