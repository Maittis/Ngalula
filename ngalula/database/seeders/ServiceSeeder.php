<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SubService;
use App\Models\ServiceAddOn;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ServiceCategory::all()->keyBy('name');

        // Massage Services
        $massageServices = [
            [
                'name' => 'Swedish Massage',
                'description' => 'Classic full-body massage using long, flowing strokes to promote relaxation and improve circulation.',
                'detailed_description' => 'Our Swedish massage is a gentle, full-body treatment that\'s perfect for stress relief and relaxation. Using light to medium pressure, our therapists use long, gliding strokes, kneading, and circular movements to help relax and energize you.',
                'duration_minutes' => 60,
                'base_price' => 89.99,
                'is_featured' => true,
                'sort_order' => 1,
                'requirements' => ['Minimum age: 18', 'Avoid heavy meals 2 hours before'],
                'benefits' => ['Stress relief', 'Improved circulation', 'Muscle relaxation', 'Better sleep'],
                'what_to_expect' => ['Private room with soothing music', 'Professional draping', 'Customizable pressure', 'Aromatherapy options'],
                'tags' => ['relaxation', 'stress-relief', 'circulation', 'classic'],
            ],
            [
                'name' => 'Deep Tissue Massage',
                'description' => 'Intensive massage targeting deep muscle layers and connective tissues to relieve chronic tension.',
                'detailed_description' => 'Deep tissue massage uses firm pressure and slow strokes to reach deeper layers of muscle and fascia. It\'s especially helpful for chronic aches and pains, and contracted areas such as stiff neck and upper back, low back pain, leg muscle tightness, and sore shoulders.',
                'duration_minutes' => 75,
                'base_price' => 119.99,
                'is_featured' => true,
                'sort_order' => 2,
                'requirements' => ['Minimum age: 18', 'Not recommended for first-time massage clients'],
                'benefits' => ['Chronic pain relief', 'Breaks up scar tissue', 'Improves mobility', 'Reduces inflammation'],
                'what_to_expect' => ['Targeted pressure on problem areas', 'Communication throughout session', 'Potential soreness afterward', 'Hydration recommendations'],
                'tags' => ['therapeutic', 'pain-relief', 'deep-pressure', 'sports'],
            ],
            [
                'name' => 'Hot Stone Massage',
                'description' => 'Therapeutic massage using smooth, heated stones to relax muscles and improve circulation.',
                'detailed_description' => 'Hot stone massage is a specialty massage where the therapist uses smooth, heated stones as an extension of their own hands, or by placing them on the body. The heat can be both deeply relaxing and help warm up tight muscles so the therapist can work more deeply, more quickly.',
                'duration_minutes' => 90,
                'base_price' => 149.99,
                'is_featured' => false,
                'sort_order' => 3,
                'requirements' => ['Minimum age: 18', 'Not recommended for pregnancy', 'Avoid if sensitive to heat'],
                'benefits' => ['Deep relaxation', 'Pain relief', 'Improved circulation', 'Stress reduction'],
                'what_to_expect' => ['Warm stones placed on body', 'Therapist uses stones for massage', 'Soothing heat therapy', 'Enhanced relaxation'],
                'tags' => ['heat-therapy', 'relaxation', 'pain-relief', 'luxury'],
            ],
        ];

        // Facial Services
        $facialServices = [
            [
                'name' => 'Classic Facial',
                'description' => 'Rejuvenating facial treatment including cleansing, exfoliation, extraction, and hydration.',
                'detailed_description' => 'Our classic facial is a comprehensive treatment that leaves your skin feeling refreshed and rejuvenated. Includes deep cleansing, gentle exfoliation, extractions if needed, customized mask, and moisturizing treatment tailored to your skin type.',
                'duration_minutes' => 60,
                'base_price' => 79.99,
                'is_featured' => true,
                'sort_order' => 1,
                'requirements' => ['Arrive makeup-free if possible', 'Avoid sun exposure 24 hours before'],
                'benefits' => ['Deep cleansing', 'Improved skin texture', 'Hydration', 'Relaxation'],
                'what_to_expect' => ['Skin analysis', 'Steam treatment', 'Extractions if needed', 'Custom mask application'],
                'tags' => ['cleansing', 'hydration', 'basic-care', 'relaxation'],
            ],
            [
                'name' => 'Anti-Aging Facial',
                'description' => 'Advanced facial treatment targeting fine lines, wrinkles, and signs of aging.',
                'detailed_description' => 'This advanced anti-aging facial uses cutting-edge techniques and products to combat signs of aging. Includes deep cleansing, enzyme exfoliation, peptide-rich serums, collagen-boosting treatments, and specialized massage techniques.',
                'duration_minutes' => 75,
                'base_price' => 129.99,
                'is_featured' => true,
                'sort_order' => 2,
                'requirements' => ['Consultation required', 'Patch test for new clients'],
                'benefits' => ['Reduces fine lines', 'Improves elasticity', 'Boosts collagen', 'Brightens complexion'],
                'what_to_expect' => ['Advanced product application', 'LED therapy', 'Specialized massage', 'Home care recommendations'],
                'tags' => ['anti-aging', 'collagen', 'wrinkles', 'advanced'],
            ],
        ];

        // Hair Treatment Services
        $hairServices = [
            [
                'name' => 'Hair Cut & Style',
                'description' => 'Professional haircut and styling session personalized to your preferences.',
                'detailed_description' => 'Our expert stylists provide personalized cuts and styling based on your face shape, hair type, and lifestyle. Includes consultation, precision cut, wash, and professional styling with premium products.',
                'duration_minutes' => 45,
                'base_price' => 59.99,
                'is_featured' => false,
                'sort_order' => 1,
                'requirements' => ['Consultation before cut', 'Clean hair preferred'],
                'benefits' => ['Fresh look', 'Healthy hair maintenance', 'Personalized style', 'Confidence boost'],
                'what_to_expect' => ['Style consultation', 'Precision cutting', 'Professional styling', 'Product recommendations'],
                'tags' => ['cut', 'style', 'maintenance', 'personalized'],
            ],
            [
                'name' => 'Deep Conditioning Treatment',
                'description' => 'Intensive hair treatment to restore moisture, shine, and vitality to damaged hair.',
                'detailed_description' => 'Transform dry, damaged hair with our intensive deep conditioning treatment. We use premium products to penetrate deep into the hair shaft, restoring moisture, repairing damage, and leaving your hair soft, shiny, and manageable.',
                'duration_minutes' => 30,
                'base_price' => 39.99,
                'is_featured' => false,
                'sort_order' => 2,
                'requirements' => ['Clean hair needed', 'No chemical treatments 48 hours before'],
                'benefits' => ['Deep hydration', 'Damage repair', 'Improved shine', 'Better manageability'],
                'what_to_expect' => ['Hair analysis', 'Treatment application', 'Heat activation', 'Blow dry finish'],
                'tags' => ['treatment', 'hydration', 'repair', 'restoration'],
            ],
        ];

        // Nail Services
        $nailServices = [
            [
                'name' => 'Classic Manicure',
                'description' => 'Traditional manicure with nail shaping, cuticle care, and polish application.',
                'detailed_description' => 'Our classic manicure includes nail shaping, cuticle treatment, hand massage, and your choice of polish. We use high-quality products to ensure your nails look beautiful and stay polished longer.',
                'duration_minutes' => 45,
                'base_price' => 29.99,
                'is_featured' => false,
                'sort_order' => 1,
                'requirements' => ['Natural nails only', 'No artificial nails'],
                'benefits' => ['Neat appearance', 'Healthy cuticles', 'Smooth nails', 'Relaxing hand massage'],
                'what_to_expect' => ['Nail shaping', 'Cuticle care', 'Hand massage', 'Polish application'],
                'tags' => ['manicure', 'basic-care', 'polish', 'relaxation'],
            ],
            [
                'name' => 'Gel Pedicure',
                'description' => 'Luxurious pedicure with long-lasting gel polish for perfect toes.',
                'detailed_description' => 'Treat your feet with our luxurious gel pedicure. Includes foot soak, exfoliation, callus treatment, nail shaping, cuticle care, massage, and durable gel polish application that lasts for weeks.',
                'duration_minutes' => 60,
                'base_price' => 49.99,
                'is_featured' => true,
                'sort_order' => 2,
                'requirements' => ['Allow extra time for gel curing', 'Avoid water exposure for 24 hours'],
                'benefits' => ['Long-lasting polish', 'Smooth feet', 'Healthy nails', 'Extended wear time'],
                'what_to_expect' => ['Luxurious foot soak', 'Callus treatment', 'Gel polish application', 'UV curing'],
                'tags' => ['pedicure', 'gel', 'long-lasting', 'luxury'],
            ],
        ];

        // Sauna Services
        $saunaServices = [
            [
                'name' => 'Traditional Sauna Session',
                'description' => 'Classic dry heat sauna experience for detoxification and relaxation.',
                'detailed_description' => 'Experience the traditional benefits of our dry heat sauna. The high temperature helps detoxify your body, improve circulation, and promote deep relaxation. Includes access to shower facilities and relaxation area.',
                'duration_minutes' => 30,
                'base_price' => 24.99,
                'is_featured' => false,
                'sort_order' => 1,
                'requirements' => ['Hydration recommended', 'Shower before use', 'Time limit applies'],
                'benefits' => ['Detoxification', 'Improved circulation', 'Stress relief', 'Muscle relaxation'],
                'what_to_expect' => ['Dry heat environment', 'Temperature control', 'Shower access', 'Relaxation area'],
                'tags' => ['detox', 'heat-therapy', 'relaxation', 'traditional'],
            ],
            [
                'name' => 'Infrared Sauna Therapy',
                'description' => 'Advanced infrared sauna with lower temperature and deeper tissue penetration.',
                'detailed_description' => 'Our infrared sauna uses advanced technology to provide therapeutic heat at lower temperatures, allowing for deeper tissue penetration and more effective detoxification. Perfect for those who find traditional saunas too intense.',
                'duration_minutes' => 45,
                'base_price' => 39.99,
                'is_featured' => true,
                'sort_order' => 2,
                'requirements' => ['Hydration essential', 'Medical consultation if pregnant', 'Start with shorter sessions'],
                'benefits' => ['Deeper detoxification', 'Pain relief', 'Lower temperature', 'Improved skin'],
                'what_to_expect' => ['Gentle infrared heat', 'Chromotherapy lighting', 'Sound system', 'Temperature control'],
                'tags' => ['infrared', 'therapy', 'advanced', 'pain-relief'],
            ],
        ];

        // Waxing Services
        $waxingServices = [
            [
                'name' => 'Brazilian Wax',
                'description' => 'Professional Brazilian wax for complete hair removal in the bikini area.',
                'detailed_description' => 'Our professional Brazilian wax provides complete hair removal in the bikini area using high-quality wax and techniques to minimize discomfort. Our experienced estheticians ensure your comfort and privacy throughout the process.',
                'duration_minutes' => 30,
                'base_price' => 59.99,
                'is_featured' => false,
                'sort_order' => 1,
                'requirements' => ['Hair length: 1/4 inch minimum', 'No sun exposure 24 hours before', 'No lotions on day of service'],
                'benefits' => ['Long-lasting results', 'Smooth skin', 'Confidence', 'Hygiene'],
                'what_to_expect' => ['Private treatment room', 'Professional technique', 'Aftercare advice', 'Soothing products'],
                'tags' => ['brazilian', 'bikini', 'hair-removal', 'professional'],
            ],
            [
                'name' => 'Full Leg Wax',
                'description' => 'Complete leg waxing service for smooth, hair-free legs.',
                'detailed_description' => 'Achieve perfectly smooth legs with our full leg waxing service. We use premium wax and professional techniques to remove hair from the entire leg area, leaving your skin smooth and hair-free for weeks.',
                'duration_minutes' => 45,
                'base_price' => 69.99,
                'is_featured' => false,
                'sort_order' => 2,
                'requirements' => ['Hair length: 1/4 inch minimum', 'No sun exposure before service', 'Exfoliate 24 hours before'],
                'benefits' => ['Smooth legs', 'Long-lasting results', 'No shaving irritation', 'Time-saving'],
                'what_to_expect' => ['Thorough hair removal', 'Soothing post-wax treatment', 'Aftercare instructions', 'Professional environment'],
                'tags' => ['legs', 'full-leg', 'smooth', 'long-lasting'],
            ],
        ];

        // Body Therapy Services
        $bodyServices = [
            [
                'name' => 'Detox Body Wrap',
                'description' => 'Full-body detoxifying wrap to cleanse and rejuvenate your skin.',
                'detailed_description' => 'Our detox body wrap uses natural ingredients to draw out impurities and toxins from your body while nourishing your skin. Includes dry brushing, application of detoxifying mud or algae, cocoon wrapping, and moisturizing finish.',
                'duration_minutes' => 90,
                'base_price' => 139.99,
                'is_featured' => true,
                'sort_order' => 1,
                'requirements' => ['Hydration important', 'Avoid eating 2 hours before', 'Shower before treatment'],
                'benefits' => ['Detoxification', 'Skin tightening', 'Hydration', 'Relaxation'],
                'what_to_expect' => ['Dry brushing exfoliation', 'Wrap application', 'Relaxation time', 'Moisturizing finish'],
                'tags' => ['detox', 'wrap', 'cleansing', 'rejuvenation'],
            ],
            [
                'name' => 'Salt Scrub Treatment',
                'description' => 'Exfoliating full-body salt scrub to reveal smooth, glowing skin.',
                'detailed_description' => 'Reveal smooth, radiant skin with our luxurious salt scrub treatment. We use mineral-rich sea salts blended with essential oils to exfoliate dead skin cells, improve circulation, and leave your skin incredibly soft and glowing.',
                'duration_minutes' => 60,
                'base_price' => 89.99,
                'is_featured' => false,
                'sort_order' => 2,
                'requirements' => ['Avoid sun exposure 24 hours after', 'No cuts or abrasions', 'Hydration recommended'],
                'benefits' => ['Smoother skin', 'Improved circulation', 'Cellulite reduction', 'Stress relief'],
                'what_to_expect' => ['Gentle exfoliation', 'Aromatherapy scents', 'Shower rinse', 'Moisturizing application'],
                'tags' => ['exfoliation', 'salt-scrub', 'smooth-skin', 'glowing'],
            ],
        ];

        // Wellness Therapy Services
        $wellnessServices = [
            [
                'name' => 'Reflexology Session',
                'description' => 'Therapeutic foot massage targeting reflex points to promote overall wellness.',
                'detailed_description' => 'Reflexology is a therapeutic method of relieving pain by stimulating predefined pressure points on the feet and hands. This controlled pressure alleviates the source of the discomfort and promotes overall wellness and relaxation.',
                'duration_minutes' => 60,
                'base_price' => 79.99,
                'is_featured' => true,
                'sort_order' => 1,
                'requirements' => ['Remove shoes and socks', 'Communicate pressure preferences', 'Hydration recommended'],
                'benefits' => ['Stress reduction', 'Improved circulation', 'Pain relief', 'Overall wellness'],
                'what_to_expect' => ['Foot assessment', 'Pressure point stimulation', 'Relaxing environment', 'Aftercare advice'],
                'tags' => ['reflexology', 'feet', 'pressure-points', 'wellness'],
            ],
            [
                'name' => 'Acupuncture Treatment',
                'description' => 'Traditional acupuncture therapy for balance and healing.',
                'detailed_description' => 'Our licensed acupuncturist provides traditional acupuncture treatments to restore balance and promote natural healing. This ancient Chinese medicine technique uses fine needles to stimulate specific points on the body.',
                'duration_minutes' => 75,
                'base_price' => 119.99,
                'is_featured' => false,
                'sort_order' => 2,
                'requirements' => ['Initial consultation required', 'Medical history disclosure', 'Loose, comfortable clothing'],
                'benefits' => ['Pain relief', 'Stress reduction', 'Improved energy', 'Balance restoration'],
                'what_to_expect' => ['Health consultation', 'Needle insertion', 'Relaxation period', 'Treatment discussion'],
                'tags' => ['acupuncture', 'traditional-medicine', 'healing', 'balance'],
            ],
        ];

        // Create services for each category
        $this->createServicesForCategory($categories['Massage'], $massageServices);
        $this->createServicesForCategory($categories['Facial'], $facialServices);
        $this->createServicesForCategory($categories['Hair Treatment'], $hairServices);
        $this->createServicesForCategory($categories['Nails'], $nailServices);
        $this->createServicesForCategory($categories['Sauna'], $saunaServices);
        $this->createServicesForCategory($categories['Waxing'], $waxingServices);
        $this->createServicesForCategory($categories['Body Therapy'], $bodyServices);
        $this->createServicesForCategory($categories['Wellness Therapy'], $wellnessServices);

        $this->command->info('Services seeded successfully!');
    }

    private function createServicesForCategory($category, $services)
    {
        foreach ($services as $serviceData) {
            $serviceData['category_id'] = $category->id;
            $service = Service::create($serviceData);
            
            // Create some example sub-services and add-ons for featured services
            if ($service->is_featured) {
                $this->createExampleSubServices($service);
                $this->createExampleAddOns($service);
            }
        }
    }

    private function createExampleSubServices($service)
    {
        $subServices = [
            [
                'name' => 'Extended Session',
                'description' => 'Add 30 minutes to your treatment for enhanced results',
                'duration_minutes' => 30,
                'price_adjustment' => 30.00,
                'price_type' => 'fixed',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Couples Treatment',
                'description' => 'Enjoy this treatment with a partner in our couples suite',
                'duration_minutes' => 0,
                'price_adjustment' => 50.00,
                'price_type' => 'fixed',
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($subServices as $subServiceData) {
            $service->subServices()->create($subServiceData);
        }
    }

    private function createExampleAddOns($service)
    {
        $addOns = [
            [
                'name' => 'Aromatherapy',
                'description' => 'Enhance your treatment with essential oils',
                'price' => 15.00,
                'duration_addition_minutes' => 0,
                'is_required' => false,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Hot Stones',
                'description' => 'Add heated stones for deeper muscle relaxation',
                'price' => 25.00,
                'duration_addition_minutes' => 15,
                'is_required' => false,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Scalp Treatment',
                'description' => 'Relaxing scalp massage with nourishing oils',
                'price' => 20.00,
                'duration_addition_minutes' => 10,
                'is_required' => false,
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($addOns as $addOnData) {
            $service->addOns()->create($addOnData);
        }
    }
}
