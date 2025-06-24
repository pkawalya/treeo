<?php

namespace Database\Seeders;

use App\Models\Farmer;
use App\Models\User;
use App\Models\Community;
use Database\Seeders\Traits\UgandaLocationData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FarmerSeeder extends Seeder
{
    use UgandaLocationData;

    public function run(): void
    {
        // Ensure we have at least one user for farmers
        $user = User::firstOrCreate(
            ['email' => 'farmer1@treeo.com'],
            [
                'name' => 'Farmer One',
                'username' => 'farmer1',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Common Ugandan first names and last names
        $firstNames = ['David', 'Grace', 'Joseph', 'Sarah', 'Robert', 'Mary', 'John', 'Alice', 'Peter', 'Esther'];
        $lastNames = ['Kato', 'Nalubega', 'Ssemakula', 'Nakato', 'Ochieng', 'Auma', 'Okello', 'Nakimuli', 'Onyango', 'Nabukenya'];
        $regions = $this->getUgandaRegionsWithDistricts();

        // Get all communities
        $communities = Community::all();
        
        // Generate farmers from different regions
        $farmers = [];
        
        // Create farmers from each region
        foreach ($regions as $region => $districts) {
            // Take up to 2 districts per region
            $selectedDistricts = array_rand(array_flip($districts), min(2, count($districts)));
            if (!is_array($selectedDistricts)) {
                $selectedDistricts = [$selectedDistricts];
            }
            
            foreach ($selectedDistricts as $district) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $email = strtolower($firstName . '.' . $lastName . rand(1, 100) . '@example.com');
                
                $subCounties = $this->getUgandanSubCounties($district);
                $subCounty = $subCounties[array_rand($subCounties)];
                
                // Get coordinates based on district (approximate center points)
                $coordinates = $this->getUgandaDistrictCoordinates($district);
                
                // Add some randomness to the coordinates to spread out farmers in the same district
                $latitude = $coordinates['lat'] + (mt_rand(-500, 500) / 10000);
                $longitude = $coordinates['lng'] + (mt_rand(-500, 500) / 10000);
                
                // Ensure coordinates are within Uganda's boundaries
                $latitude = max( -1.4823, min(4.2341, $latitude )); // Uganda's lat range
                $longitude = max( 29.5734, min(35.0000, $longitude )); // Uganda's lng range
                $parish = $subCounty . ' ' . $this->getRandomSuffix();
                $village = $parish . ' ' . $this->getRandomSuffix();
                
                $farmers[] = [
                    'user_id' => (rand(0, 3) === 0) ? $user->id : null, // 25% chance to get a user account
                    'name' => "$firstName $lastName",
                    'phone' => $this->getRandomUgandanPhoneNumber(),
                    'email' => $email,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'district' => $district,
                    'sub_county' => $subCounty,
                    'parish' => $parish,
                    'village' => $village,
                    'latitude' => $this->generateUgandanLatitude($district),
                    'longitude' => $this->generateUgandanLongitude($district),
                    'status' => rand(0, 10) > 1 ? 'active' : 'inactive', // 90% active
                ];
            }
        }

        // Add some additional farmers from major districts
        $majorDistricts = ['Kampala', 'Wakiso', 'Mukono', 'Jinja', 'Mbarara', 'Gulu', 'Arua', 'Mbale', 'Lira', 'Masaka'];
        foreach ($majorDistricts as $district) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $email = strtolower($firstName . '.' . $lastName . rand(1, 100) . '@example.com');
            
            $subCounties = $this->getUgandanSubCounties($district);
            $subCounty = $subCounties[array_rand($subCounties)];
            $parish = $subCounty . ' ' . $this->getRandomSuffix();
            $village = $parish . ' ' . $this->getRandomSuffix();
            
            // Get a random community for this farmer
            $community = $communities->random();
            
            $farmers[] = [
                'user_id' => (rand(0, 3) === 0) ? $user->id : null,
                'community_id' => $community->id,
                'name' => "$firstName $lastName",
                'phone' => $this->getRandomUgandanPhoneNumber(),
                'email' => $email,
                'district' => $district,
                'sub_county' => $subCounty,
                'parish' => $parish,
                'village' => $village,
                'latitude' => $this->generateUgandanLatitude($district),
                'longitude' => $this->generateUgandanLongitude($district),
                'status' => rand(0, 10) > 1 ? 'active' : 'inactive',
            ];
        }

        // Create the farmers
        foreach ($farmers as $farmer) {
            Farmer::firstOrCreate(
                ['email' => $farmer['email']],
                $farmer
            );
        }
    }

    private function generateUgandanLatitude(string $district): float
    {
        // Approximate latitude ranges for Uganda
        $districtLatitudes = [
            'Kampala' => [0.3, 0.4],
            'Wakiso' => [0.2, 0.4],
            'Mukono' => [0.1, 0.3],
            'Luweero' => [0.6, 0.9],
            'Gulu' => [2.7, 3.0],
            'Mbale' => [1.0, 1.2],
            'Kabale' => [-1.3, -1.0],
            'Mbarara' => [-0.7, -0.5],
            'Arua' => [2.9, 3.2],
            'Lira' => [2.2, 2.4],
        ];

        $range = $districtLatitudes[$district] ?? [0.0, 2.0];
        return $range[0] + (mt_rand() / mt_getrandmax()) * ($range[1] - $range[0]);
    }

    private function generateUgandanLongitude(string $district): float
    {
        // Approximate longitude ranges for Uganda
        $districtLongitudes = [
            'Kampala' => [32.5, 32.7],
            'Wakiso' => [32.4, 32.6],
            'Mukono' => [32.7, 33.0],
            'Luweero' => [32.3, 32.6],
            'Gulu' => [32.2, 32.5],
            'Mbale' => [34.1, 34.3],
            'Kabale' => [29.9, 30.1],
            'Mbarara' => [30.5, 30.8],
            'Arua' => [30.9, 31.2],
            'Lira' => [32.8, 33.1],
        ];

        $range = $districtLongitudes[$district] ?? [29.0, 35.0];
        return $range[0] + (mt_rand() / mt_getrandmax()) * ($range[1] - $range[0]);
    }
}
