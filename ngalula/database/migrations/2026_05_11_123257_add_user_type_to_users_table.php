<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', ['customer', 'therapist', 'receptionist', 'admin', 'super_admin'])->default('customer')->after('email');
            $table->string('phone')->nullable()->after('user_type');
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
            $table->string('otp_code')->nullable()->after('phone_verified_at');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            $table->boolean('is_active')->default(true)->after('otp_expires_at');
            $table->string('google_id')->nullable()->after('is_active');
            $table->string('facebook_id')->nullable()->after('google_id');
            $table->string('apple_id')->nullable()->after('facebook_id');
            $table->string('biometric_token')->nullable()->after('apple_id');
            $table->boolean('two_factor_enabled')->default(false)->after('biometric_token');
            $table->string('two_factor_secret')->nullable()->after('two_factor_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'user_type',
                'phone',
                'phone_verified_at',
                'otp_code',
                'otp_expires_at',
                'is_active',
                'google_id',
                'facebook_id',
                'apple_id',
                'biometric_token',
                'two_factor_enabled',
                'two_factor_secret'
            ]);
        });
    }
};
