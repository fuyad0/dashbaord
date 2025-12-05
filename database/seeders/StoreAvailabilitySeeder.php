<?php

namespace Database\Seeders;

use App\Models\Availability;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreAvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Availability::create([
            'stores_id' => '1',
            'day' => 'Saturday',
            'time_start' => '08:00:01',
            'time_end' => '05:00:00',
        ]);
    }
}
