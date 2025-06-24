<?php

namespace Database\Seeders;

use App\Models\Seedling;
use Illuminate\Database\Seeder;

class SeedlingSeeder extends Seeder
{
    public function run(): void
    {
        $seedlings = [
            [
                'name' => 'Mvule (African Teak)',
                'type' => 'Timber',
                'description' => 'Mvule is a valuable timber tree native to Africa, known for its durability and resistance to termites.',
                'growth_stages' => json_encode([
                    ['name' => 'Germination', 'duration_days' => 14],
                    ['name' => 'Seedling', 'duration_days' => 60],
                    ['name' => 'Sapling', 'duration_months' => 12],
                    ['name' => 'Mature', 'duration_years' => 10]
                ]),
                'image_url' => 'seedlings/mvule.jpg'
            ],
            [
                'name' => 'Mango',
                'type' => 'Fruit',
                'description' => 'Mango trees produce delicious fruits and provide good shade.',
                'growth_stages' => json_encode([
                    ['name' => 'Germination', 'duration_days' => 21],
                    ['name' => 'Seedling', 'duration_days' => 90],
                    ['name' => 'Sapling', 'duration_months' => 18],
                    ['name' => 'Fruiting', 'duration_years' => 3]
                ]),
                'image_url' => 'seedlings/mango.jpg'
            ],
            [
                'name' => 'Eucalyptus',
                'type' => 'Timber',
                'description' => 'Fast-growing tree used for timber, firewood, and essential oils.',
                'growth_stages' => json_encode([
                    ['name' => 'Germination', 'duration_days' => 10],
                    ['name' => 'Seedling', 'duration_days' => 45],
                    ['name' => 'Sapling', 'duration_months' => 6],
                    ['name' => 'Mature', 'duration_years' => 3]
                ]),
                'image_url' => 'seedlings/eucalyptus.jpg'
            ]
        ];

        foreach ($seedlings as $seedling) {
            Seedling::firstOrCreate(
                ['name' => $seedling['name']],
                $seedling
            );
        }
    }
}
