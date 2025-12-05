<?php

namespace Database\Seeders;

use App\Models\Offer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreOfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Offer::create([
            'stores_id' => '1',
            'offer_type' => '2 In 1',
            'offer_des' => 'My Restaurants',
            'status' => 'Active',
        ]);
    }
}
