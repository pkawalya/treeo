<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Seedlings',
            'Fertilizers',
            'Tools',
            'Equipment',
            'Protective Gear',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category],
                [
                    'slug' => Str::slug($category),
                    'description' => 'Sample description for ' . $category,
                    'is_active' => true,
                ]
            );
        }
    }
}
