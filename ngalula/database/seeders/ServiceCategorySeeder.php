<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Massage',
                'description' => 'Relaxing and therapeutic massage services including Swedish, deep tissue, hot stone, and aromatherapy massages.',
                'icon' => 'fa-hands',
                'sort_order' => 1,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Facial',
                'description' => 'Rejuvenating facial treatments including deep cleansing, anti-aging, hydrating, and specialty facials.',
                'icon' => 'fa-spa',
                'sort_order' => 2,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Hair Treatment',
                'description' => 'Professional hair care services including cuts, coloring, styling, and specialized hair treatments.',
                'icon' => 'fa-cut',
                'sort_order' => 3,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Nails',
                'description' => 'Complete nail care services including manicures, pedicures, gel nails, and nail art.',
                'icon' => 'fa-hand-sparkles',
                'sort_order' => 4,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Sauna',
                'description' => 'Traditional and infrared sauna sessions for detoxification and relaxation.',
                'icon' => 'fa-fire',
                'sort_order' => 5,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Waxing',
                'description' => 'Professional hair removal services using various waxing techniques for all body areas.',
                'icon' => 'fa-feather',
                'sort_order' => 6,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Body Therapy',
                'description' => 'Comprehensive body treatments including wraps, scrubs, and therapeutic body therapies.',
                'icon' => 'fa-person',
                'sort_order' => 7,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Wellness Therapy',
                'description' => 'Holistic wellness services including acupuncture, reflexology, and alternative therapies.',
                'icon' => 'fa-yin-yang',
                'sort_order' => 8,
                'is_featured' => false,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::create($category);
        }

        $this->command->info('Service categories seeded successfully!');
    }
}
