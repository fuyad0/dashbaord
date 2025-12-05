<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanOption;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Sample Packages
        $packages = [
            [
                'title' => 'Freebie',
                'description' => 'description',
                'price' => 0.00,
                'type' => 'Free',
                'duration' => 30,
                'stripe_product_id' => 'prod_TH3zXWC8lZ4p8l',
                'stripe_price_id' => 'price_1SKVrB6627bgwGRusMKlm0W0',
                'interval' => 'month',
                'status' => 'Active',
                'options' => [
                    [
                        'name' => 'Description One',
                        'type' => 'Yes',
                    ],
                    [
                        'name' => 'Description Two',
                        'type' => 'Yes',
                    ],
                    [
                        'name' => 'Description Three',
                        'type' => 'No',
                    ],
                    [
                        'name' => 'Description Four',
                        'type' => 'No',
                    ],
                    [
                        'name' => 'Description Five',
                        'type' => 'No',
                    ],
                    [
                        'name' => 'Description Six',
                        'type' => 'No',
                    ],
                ],
            ],
            [
                'title' => 'Professional',
                'description' => 'description',
                'price' => 6.90,
                'type' => 'Month',
                'duration' => 30,
                'stripe_product_id' => 'prod_TH3zlJ3mfsOMpQ',
                'stripe_price_id' => 'price_1SKVrh6627bgwGRuezUTqz06',
                'interval' => 'month',
                'status' => 'Active',
                'options' => [
                    [
                        'name' => 'Description One',
                        'type' => 'Yes',
                    ],
                    [
                        'name' => 'Description Two',
                        'type' => 'Yes',
                    ],
                    [
                        'name' => 'Description Three',
                        'type' => 'Yes',
                    ],
                    [
                        'name' => 'Description Four',
                        'type' => 'No',
                    ],
                    [
                        'name' => 'Description Five',
                        'type' => 'No',
                    ],
                    [
                        'name' => 'Description Six',
                        'type' => 'No',
                    ],
                ],
            ],
            [
                'title' => 'Enterprise',
                'description' => 'description',
                'price' => 69.00,
                'type' => 'Yearly',
                'duration' => 30,
                'stripe_product_id' => 'prod_TH42hdNin1E3WD',
                'stripe_price_id' => 'price_1SKVuo6627bgwGRukKVwQUnw',
                'interval' => 'year',
                'status' => 'Active',
                'options' => [
                    [
                        'name' => 'Description One',
                        'type' => 'Yes',
                    ],
                    [
                        'name' => 'Description Two',
                        'type' => 'Yes',
                    ],
                    [
                        'name' => 'Description Three',
                        'type' => 'Yes',
                    ],
                    [
                        'name' => 'Description Four',
                        'type' => 'Yes',
                    ],
                    [
                        'name' => 'Description Five',
                        'type' => 'Yes',
                    ],
                    [
                        'name' => 'Description Six',
                        'type' => 'Yes',
                    ],
                ],
            ],
        ];

        // Insert into database
        foreach ($packages as $packageData) {
            $package = Plan::create([
                'title' => $packageData['title'],
                'description' => $packageData['description'],
                'price' => $packageData['price'],
                'type' => $packageData['type'],
                'duration' => $packageData['duration'],
                'stripe_product_id' => $packageData['stripe_product_id'],
                'stripe_price_id' => $packageData['stripe_price_id'],
                'interval' => $packageData['interval'],
                'status' => $packageData['status'],
            ]);

            // Add Package Options
            foreach ($packageData['options'] as $option) {
                PlanOption::create([
                    'plan_id' => $package->id,
                    'name' => $option['name'],
                    'type' => $option['type'],
                ]);
            }
        }

    }
}
