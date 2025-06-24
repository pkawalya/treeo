<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\Distribution;
use App\Models\Farmer;
use App\Models\Seedling;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\Traits\UgandaLocationData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistributionSeeder extends Seeder
{
    use UgandaLocationData;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all seedlings, farmers, communities, and distributors
        $seedlings = Seedling::all();
        $farmers = Farmer::all();
        $communities = Community::all();
        $distributors = User::where('is_admin', true)->get();

        if ($seedlings->isEmpty() || $farmers->isEmpty() || $communities->isEmpty() || $distributors->isEmpty()) {
            $this->command->warn('Skipping DistributionSeeder: Insufficient data (seedlings, farmers, communities, or admin users missing)');
            return;
        }

        // Get all districts in Uganda
        $ugandaDistricts = $this->getUgandaRegionsWithDistricts();
        $allDistricts = [];
        foreach ($ugandaDistricts as $region => $districts) {
            foreach ($districts as $district) {
                $allDistricts[$district] = ['region' => $region];
            }
        }

        // Common seedling types and their preferred regions
        $seedlingPreferences = [
            'Mvule (African Teak)' => ['Northern', 'Eastern'],
            'Mango' => ['Central', 'Eastern', 'Western'],
            'Eucalyptus' => ['Central', 'Western'],
        ];

        // Create distributions for farmers (individual distributions)
        foreach ($farmers as $farmer) {
            $district = $farmer->district;
            $region = $allDistricts[$district]['region'] ?? 'Central';
            
            // Choose appropriate seedling based on region
            $suitableSeedlings = $seedlings->filter(function($seedling) use ($seedlingPreferences, $region) {
                return in_array($region, $seedlingPreferences[$seedling->name] ?? [$region]);
            });
            
            if ($suitableSeedlings->isEmpty()) {
                $suitableSeedlings = $seedlings;
            }
            
            $seedling = $suitableSeedlings->random();
            
            // Create 1-3 distributions per farmer
            $distributionCount = rand(1, 3);
            for ($i = 0; $i < $distributionCount; $i++) {
                $distributionDate = Carbon::now()
                    ->subMonths(rand(0, 12)) // Up to 1 year old
                    ->subDays(rand(0, 30)); // Random day in the month
                
                Distribution::create([
                    'recipient_type' => 'farmer',
                    'recipient_id' => $farmer->id,
                    'seedling_id' => $seedling->id,
                    'quantity' => $this->getRandomQuantity('farmer'),
                    'distribution_date' => $distributionDate,
                    'distributor_id' => $distributors->random()->id,
                    'notes' => $this->generateDistributionNotes('farmer', $farmer, $seedling, $district, $region),
                    'status' => $this->getRandomStatus($distributionDate),
                ]);
            }
        }

        // Create distributions for communities (bulk distributions)
        foreach ($communities as $community) {
            $district = $community->district;
            $region = $allDistricts[$district]['region'] ?? 'Central';
            
            // Choose appropriate seedling based on region
            $suitableSeedlings = $seedlings->filter(function($seedling) use ($seedlingPreferences, $region) {
                return in_array($region, $seedlingPreferences[$seedling->name] ?? [$region]);
            });
            
            if ($suitableSeedlings->isEmpty()) {
                $suitableSeedlings = $seedlings;
            }
            
            $seedling = $suitableSeedlings->random();
            
            // Create 1-2 distributions per community
            $distributionCount = rand(1, 2);
            for ($i = 0; $i < $distributionCount; $i++) {
                $distributionDate = Carbon::now()
                    ->subMonths(rand(0, 24)) // Up to 2 years old
                    ->subDays(rand(0, 30)); // Random day in the month
                
                Distribution::create([
                    'recipient_type' => 'community',
                    'recipient_id' => $community->id,
                    'seedling_id' => $seedling->id,
                    'quantity' => $this->getRandomQuantity('community'),
                    'distribution_date' => $distributionDate,
                    'distributor_id' => $distributors->random()->id,
                    'notes' => $this->generateDistributionNotes('community', $community, $seedling, $district, $region),
                    'status' => $this->getRandomStatus($distributionDate),
                ]);
            }
        }
    }
    
    private function getRandomQuantity(string $type): int
    {
        return $type === 'community' 
            ? rand(500, 5000) // Larger quantities for communities
            : rand(10, 200);  // Smaller quantities for individual farmers
    }
    
    private function generateDistributionNotes(string $type, $recipient, $seedling, string $district, string $region): string
    {
        $recipientType = $type === 'community' ? 'community' : 'farmer';
        $recipientName = $recipient->name;
        $seedlingName = $seedling->name;
        
        $notes = [
            "Distribution of $seedlingName to $recipientType $recipientName in $district ($region region)",
            "$seedlingName seedlings provided to $recipientType $recipientName in $district",
            "$recipientType support: $seedlingName distribution in $district",
            "$seedlingName allocation to $recipientType $recipientName, $district district",
            "$seedlingName seedlings for $recipientType $recipientName in $district ($region)",
        ];
        
        // Add some random notes for variety
        $additionalNotes = [
            ' Part of regional reforestation program.',
            ' Includes training on proper planting techniques.',
            ' Follow-up visit scheduled.',
            ' Part of climate change mitigation efforts.',
            ' Distribution coordinated with local authorities.',
            ' Recipient committed to proper care and maintenance.',
        ];
        
        $note = $notes[array_rand($notes)];
        if (rand(0, 1)) { // 50% chance to add additional note
            $note .= $additionalNotes[array_rand($additionalNotes)];
        }
        
        return $note;
    }
    
    private function getRandomStatus(Carbon $distributionDate): string
    {
        $daysSinceDistribution = $distributionDate->diffInDays(now());
        
        if ($daysSinceDistribution < 7) {
            // Recent distributions might still be pending
            return rand(0, 1) ? 'completed' : 'pending';
        } elseif ($daysSinceDistribution < 30) {
            // Older distributions are more likely to be completed
            return rand(0, 10) > 1 ? 'completed' : 'cancelled';
        } else {
            // Very old distributions are almost certainly completed
            return 'completed';
        }
    }
}
