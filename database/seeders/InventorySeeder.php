<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Seedling;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        // Get the first category, vendor, and seedling
        $category = Category::first();
        $vendor = Vendor::first();
        $seedling = Seedling::first();

        if (!$category || !$vendor || !$seedling) {
            $this->call([
                CategorySeeder::class,
                VendorSeeder::class,
                SeedlingSeeder::class,
            ]);
            $category = Category::first();
            $vendor = Vendor::first();
            $seedling = Seedling::first();
        }

        $inventoryItems = [
            [
                'seedling_id' => $seedling->id,
                'name' => 'Mango Seedlings',
                'sku' => 'MNG-001',
                'description' => 'High-quality mango seedlings, ready for planting',
                'quantity' => 1000,
                'unit_of_measure' => 'pcs',
                'unit_cost' => 5000, // in UGX
                'reorder_level' => 100,
                'location' => 'Nursery A',
                'category_id' => $category->id,
                'supplier_id' => $vendor->id,
                'batch_number' => 'BATCH-' . date('Ymd') . '-001',
                'procurement_date' => now()->subDays(30),
                'expiry_date' => now()->addMonths(6),
                'status' => 'in_stock',
                'last_stocked_at' => now(),
            ],
            [
                'seedling_id' => $seedling->id,
                'name' => 'Organic Fertilizer',
                'sku' => 'FERT-001',
                'description' => 'Organic fertilizer for young trees',
                'quantity' => 500,
                'unit_of_measure' => 'kg',
                'unit_cost' => 10000, // in UGX
                'reorder_level' => 50,
                'location' => 'Store Room 1',
                'category_id' => $category->id,
                'supplier_id' => $vendor->id,
                'batch_number' => 'BATCH-' . date('Ymd') . '-002',
                'procurement_date' => now()->subDays(15),
                'expiry_date' => now()->addYear(),
                'status' => 'in_stock',
                'last_stocked_at' => now(),
            ],
            [
                'seedling_id' => $seedling->id,
                'name' => 'Pruning Shears',
                'sku' => 'TOOL-001',
                'description' => 'Professional pruning shears for tree maintenance',
                'quantity' => 50,
                'unit_of_measure' => 'pcs',
                'unit_cost' => 25000, // in UGX
                'reorder_level' => 10,
                'location' => 'Tool Shed',
                'category_id' => $category->id,
                'supplier_id' => $vendor->id,
                'batch_number' => 'BATCH-' . date('Ymd') . '-003',
                'procurement_date' => now()->subDays(7),
                'expiry_date' => null, // Tools don't expire
                'status' => 'in_stock',
                'last_stocked_at' => now(),
            ],
        ];

        foreach ($inventoryItems as $item) {
            Inventory::firstOrCreate(
                ['sku' => $item['sku']],
                $item
            );
        }
    }
}
