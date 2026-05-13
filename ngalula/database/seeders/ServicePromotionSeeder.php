<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ServicePromotion;
use App\Models\Service;

class ServicePromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some featured services to create promotions for
        $swedishMassage = Service::where('name', 'Swedish Massage')->first();
        $classicFacial = Service::where('name', 'Classic Facial')->first();
        $detoxWrap = Service::where('name', 'Detox Body Wrap')->first();

        if ($swedishMassage) {
            ServicePromotion::create([
                'service_id' => $swedishMassage->id,
                'name' => 'Summer Relaxation Special',
                'description' => 'Beat the summer heat with our refreshing Swedish massage at a special discount!',
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'starts_at' => now()->subDays(10),
                'ends_at' => now()->addDays(20),
                'is_active' => true,
                'promo_code' => 'SUMMER20',
                'usage_limit' => 100,
                'usage_count' => 15,
                'conditions' => [
                    'Valid for new clients only',
                    'Cannot be combined with other offers',
                    'Monday-Thursday appointments only'
                ],
            ]);
        }

        if ($classicFacial) {
            ServicePromotion::create([
                'service_id' => $classicFacial->id,
                'name' => 'Glow Up Package',
                'description' => 'Get radiant skin for summer with our special facial package discount!',
                'discount_type' => 'fixed_amount',
                'discount_value' => 15.00,
                'starts_at' => now()->subDays(5),
                'ends_at' => now()->addDays(25),
                'is_active' => true,
                'usage_limit' => null, // Unlimited usage
                'usage_count' => 8,
                'conditions' => [
                    'Valid for all clients',
                    'Includes complimentary product sample',
                    'Booking required 24 hours in advance'
                ],
            ]);
        }

        if ($detoxWrap) {
            ServicePromotion::create([
                'service_id' => $detoxWrap->id,
                'name' => 'Weekend Wellness Retreat',
                'description' => 'Escape the weekend stress with our luxurious detox body wrap at a special price!',
                'discount_type' => 'percentage',
                'discount_value' => 25.00,
                'starts_at' => now()->addDays(1),
                'ends_at' => now()->addDays(30),
                'is_active' => true,
                'usage_limit' => 50,
                'usage_count' => 0,
                'conditions' => [
                    'Weekend appointments only (Sat-Sun)',
                    'First-time clients get extra 10% off',
                    'Includes complimentary herbal tea'
                ],
            ]);
        }

        $this->command->info('Service promotions seeded successfully!');
    }
}
