<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SimpleBookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Create simple bookings table
            DB::statement('
                CREATE TABLE IF NOT EXISTS simple_bookings (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    booking_id VARCHAR(50) UNIQUE,
                    service_name VARCHAR(255),
                    therapist_name VARCHAR(255),
                    customer_name VARCHAR(255),
                    customer_email VARCHAR(255),
                    customer_phone VARCHAR(50),
                    booking_date DATE,
                    booking_time TIME,
                    payment_method VARCHAR(50),
                    total_amount DECIMAL(8,2),
                    status VARCHAR(50) DEFAULT "confirmed",
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ');
            
            // Create admin notifications table
            DB::statement('
                CREATE TABLE IF NOT EXISTS admin_notifications (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    type VARCHAR(100),
                    message TEXT,
                    details JSON,
                    read_status BOOLEAN DEFAULT FALSE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ');
            
            // Create customer notifications table
            DB::statement('
                CREATE TABLE IF NOT EXISTS customer_notifications (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    customer_email VARCHAR(255),
                    type VARCHAR(100),
                    message TEXT,
                    details JSON,
                    read_status BOOLEAN DEFAULT FALSE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ');
            
            Log::info('All booking tables created successfully');
            
            // Insert sample data for testing
            $this->insertSampleData();
            
        } catch (\Exception $e) {
            Log::error('Error creating booking tables: ' . $e->getMessage());
        }
    }
    
    private function insertSampleData()
    {
        try {
            // Insert sample therapists
            DB::table('simple_therapists')->insert([
                ['id' => 1, 'name' => 'Sarah Johnson', 'specialty' => 'Massage Therapy', 'rating' => 5.0, 'experience_years' => 10],
                ['id' => 2, 'name' => 'Michael Chen', 'specialty' => 'Wellness Expert', 'rating' => 4.8, 'experience_years' => 8],
                ['id' => 3, 'name' => 'Emily Davis', 'specialty' => 'Facial Specialist', 'rating' => 5.0, 'experience_years' => 12],
            ]);
            
            // Insert sample services
            DB::table('simple_services')->insert([
                ['id' => 1, 'name' => 'Swedish Massage', 'price' => 120, 'duration' => 60, 'category' => 'Massage'],
                ['id' => 2, 'name' => 'Deep Tissue Massage', 'price' => 150, 'duration' => 75, 'category' => 'Massage'],
                ['id' => 3, 'name' => 'Aromatherapy Massage', 'price' => 130, 'duration' => 60, 'category' => 'Massage'],
                ['id' => 4, 'name' => 'Rejuvenating Facial', 'price' => 100, 'duration' => 60, 'category' => 'Facial'],
                ['id' => 5, 'name' => 'Hot Stone Therapy', 'price' => 140, 'duration' => 90, 'category' => 'Massage'],
                ['id' => 6, 'name' => 'Sports Massage', 'price' => 160, 'duration' => 75, 'category' => 'Massage'],
            ]);
            
            Log::info('Sample data inserted successfully');
            
        } catch (\Exception $e) {
            Log::error('Error inserting sample data: ' . $e->getMessage());
        }
    }
}
