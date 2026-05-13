<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create or update admin user
        User::updateOrCreate(
            ['email' => 'admin@ngalula.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@ngalula.com',
                'password' => Hash::make('admin123'),
                'user_type' => 'admin',
                'phone' => '1234567890',
                'email_verified_at' => now(),
            ]
        );
        
        $this->command->info('Admin user created successfully!');
    }
}
