<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'Green Thumb Nurseries',
                'company_registration' => 'REG12345678',
                'vendor_type' => 'Seedling Supplier',
                'is_active' => true,
                'contact_person' => 'John Doe',
                'contact_position' => 'Sales Manager',
                'email' => 'info@greenthumb.com',
                'phone' => '+256700000001',
                'address' => '123 Garden Road, Kampala',
                'city' => 'Kampala',
                'country' => 'Uganda',
                'payment_terms' => 'Net 30',
                'currency' => 'UGX',
            ],
            [
                'name' => 'Agro Supplies Ltd',
                'company_registration' => 'REG87654321',
                'vendor_type' => 'Equipment Supplier',
                'is_active' => true,
                'contact_person' => 'Jane Smith',
                'contact_position' => 'Director',
                'email' => 'sales@agrosupplies.com',
                'phone' => '+256700000002',
                'address' => '456 Farm Lane, Entebbe',
                'city' => 'Entebbe',
                'country' => 'Uganda',
                'payment_terms' => 'Net 15',
                'currency' => 'UGX',
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::firstOrCreate(
                ['email' => $vendor['email']],
                $vendor
            );
        }
    }
}
