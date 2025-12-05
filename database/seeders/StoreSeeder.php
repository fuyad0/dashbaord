<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Store::create([
            'users_id' => '1',
            'type' => 'Restaurants',
            'name' => 'My Restaurants',
            'slug' => 'my-restaurants',
            'slogan' => 'Restaurants',
            'logo' => null,
            'banner' => null,
            'email' => 'info@grandsave.com',
            'phone' => '5873515720',
            'whatsapp' => '5873515720',
            'address' => null,
            'website' => 'https://example.com',
            'details' => null,
            'facebook' => 'https://facebook.com',
            'twitter' => 'https://twitter.com',
            'youtube' => 'https://linkedin.com',
            'tiktok' => 'https://linkedin.com',
            'longitude' => '23.776474204846373',
            'latitude' => '90.40365223824473',
            'reservation' => 1,
            'status' => 'Active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
