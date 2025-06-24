<?php

namespace Database\Seeders;

use App\Models\Community;
use Database\Seeders\Traits\UgandaLocationData;
use Illuminate\Database\Seeder;

class CommunitySeeder extends Seeder
{
    use UgandaLocationData;

    public function run(): void
    {
        $regions = $this->getUgandaRegionsWithDistricts();
        
        // Create communities in different regions
        $communities = [
            [
                'name' => 'Green Valley Farmers Group',
                'leader_name' => 'John Omondi',
                'district' => 'Wakiso',
                'member_count' => rand(15, 40),
            ],
            [
                'name' => 'Sustainable Farming Initiative',
                'leader_name' => 'Sarah Nalwoga',
                'district' => 'Mukono',
                'member_count' => rand(15, 40),
            ],
            [
                'name' => 'Eco-Agriculture Cooperative',
                'leader_name' => 'Robert Ssebunya',
                'district' => 'Luweero',
                'member_count' => rand(15, 40),
            ],
            // Add more regions
            [
                'name' => 'Western Highlands Farmers Association',
                'leader_name' => 'Grace Mbabazi',
                'district' => 'Kabale',
                'member_count' => rand(15, 40),
            ],
            [
                'name' => 'Northern Agroforestry Network',
                'leader_name' => 'Ocen Patrick',
                'district' => 'Gulu',
                'member_count' => rand(15, 40),
            ],
            [
                'name' => 'Eastern Tree Growers Cooperative',
                'leader_name' => 'Namukasa Prossy',
                'district' => 'Mbale',
                'member_count' => rand(15, 40),
            ],
        ];

        foreach ($communities as $community) {
            $district = $community['district'];
            $subCounties = $this->getUgandanSubCounties($district);
            $subCounty = $subCounties[array_rand($subCounties)];
            $parish = $subCounty . ' ' . $this->getRandomSuffix();
            $village = $parish . ' ' . $this->getRandomSuffix();

            Community::firstOrCreate(
                ['name' => $community['name']],
                [
                    'name' => $community['name'],
                    'leader_name' => $community['leader_name'],
                    'contact' => $this->getRandomUgandanPhoneNumber(),
                    'district' => $district,
                    'sub_county' => $subCounty,
                    'parish' => $parish,
                    'village' => $village,
                    'latitude' => $this->generateUgandanLatitude($district),
                    'longitude' => $this->generateUgandanLongitude($district),
                    'member_count' => $community['member_count'],
                ]
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
