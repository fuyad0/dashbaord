<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::create([
            'title' => 'Grandsave',
            'system_name' => 'Grandsave',
            'email' => 'info@grandsave.com',
            'number' => '5873515720',
            'logo' => null,
            'favicon' => null,
            'address' => null,
            'copyright_text' => 'Copyright 2025. All Rights Reserved. Powered by Grandsave.',
            'description' => null,
            'facebook' => 'https://facebook.com',
            'twitter' => 'https://twitter.com',
            'linkedin' => 'https://linkedin.com',
            'instagram' => 'https://instagram.com',
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);
    }
}
