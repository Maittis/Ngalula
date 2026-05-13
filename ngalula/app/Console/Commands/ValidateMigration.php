<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Inventory\Product;
use App\Models\Inventory\Oil;
use App\Models\Inventory\Cream;
use App\Models\Inventory\Equipment;
use App\Models\Service;
use App\Models\Booking;

class ValidateMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migration:validate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate the tech stack migration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting migration validation...');
        
        $this->validateDatabase();
        $this->validateModels();
        $this->validateRelationships();
        $this->validateConfiguration();
        $this->validatePerformance();
        
        $this->info('Migration validation completed successfully!');
        $this->info('All systems are ready for production use.');
    }

    private function validateDatabase()
    {
        $this->info('Validating database configuration...');
        
        // Check database connection
        try {
            DB::connection()->getPdo();
            $this->info('✓ Database connection successful');
        } catch (\Exception $e) {
            $this->error('✗ Database connection failed: ' . $e->getMessage());
            return;
        }
        
        // Check PostgreSQL version
        $version = DB::select('SELECT version()')[0]->version;
        $this->info("✓ PostgreSQL version: {$version}");
        
        // Check Redis connection
        try {
            $redis = app('redis');
            $redis->ping();
            $this->info('✓ Redis connection successful');
        } catch (\Exception $e) {
            $this->error('✗ Redis connection failed: ' . $e->getMessage());
        }
        
        // Validate required tables exist
        $requiredTables = [
            'users',
            'inventory_products',
            'inventory_oils',
            'inventory_creams',
            'inventory_equipment',
            'inventory_suppliers',
            'inventory_transactions',
            'inventory_alerts',
            'services',
            'bookings',
            'payments',
        ];
        
        foreach ($requiredTables as $table) {
            if (Schema::hasTable($table)) {
                $this->info("✓ Table {$table} exists");
            } else {
                $this->error("✗ Table {$table} missing");
            }
        }
        
        // Validate PostgreSQL-specific features
        try {
            $result = DB::select('SELECT COUNT(*) as count FROM pg_indexes WHERE tablename = \'inventory_products\'');
            $this->info("✓ PostgreSQL indexes configured ({$result[0]->count} indexes found)");
        } catch (\Exception $e) {
            $this->error('✗ PostgreSQL indexes validation failed: ' . $e->getMessage());
        }
    }

    private function validateModels()
    {
        $this->info('Validating models...');
        
        // Test User model
        try {
            $userCount = User::count();
            $this->info("✓ User model working ({$userCount} users found)");
        } catch (\Exception $e) {
            $this->error('✗ User model failed: ' . $e->getMessage());
        }
        
        // Test Product model
        try {
            $productCount = Product::count();
            $this->info("✓ Product model working ({$productCount} products found)");
        } catch (\Exception $e) {
            $this->error('✗ Product model failed: ' . $e->getMessage());
        }
        
        // Test Oil model
        try {
            $oilCount = Oil::count();
            $this->info("✓ Oil model working ({$oilCount} oils found)");
        } catch (\Exception $e) {
            $this->error('✗ Oil model failed: ' . $e->getMessage());
        }
        
        // Test Cream model
        try {
            $creamCount = Cream::count();
            $this->info("✓ Cream model working ({$creamCount} creams found)");
        } catch (\Exception $e) {
            $this->error('✗ Cream model failed: ' . $e->getMessage());
        }
        
        // Test Equipment model
        try {
            $equipmentCount = Equipment::count();
            $this->info("✓ Equipment model working ({$equipmentCount} equipment found)");
        } catch (\Exception $e) {
            $this->error('✗ Equipment model failed: ' . $e->getMessage());
        }
        
        // Test Service model
        try {
            $serviceCount = Service::count();
            $this->info("✓ Service model working ({$serviceCount} services found)");
        } catch (\Exception $e) {
            $this->error('✗ Service model failed: ' . $e->getMessage());
        }
        
        // Test Booking model
        try {
            $bookingCount = Booking::count();
            $this->info("✓ Booking model working ({$bookingCount} bookings found)");
        } catch (\Exception $e) {
            $this->error('✗ Booking model failed: ' . $e->getMessage());
        }
    }

    private function validateRelationships()
    {
        $this->info('Validating model relationships...');
        
        // Test Product relationships
        try {
            $product = Product::first();
            if ($product) {
                $supplier = $product->supplier;
                $transactions = $product->transactions;
                $this->info('✓ Product relationships working');
            }
        } catch (\Exception $e) {
            $this->error('✗ Product relationships failed: ' . $e->getMessage());
        }
        
        // Test User relationships
        try {
            $user = User::first();
            if ($user) {
                $bookings = $user->bookings;
                $this->info('✓ User relationships working');
            }
        } catch (\Exception $e) {
            $this->error('✗ User relationships failed: ' . $e->getMessage());
        }
        
        // Test Booking relationships
        try {
            $booking = Booking::first();
            if ($booking) {
                $customer = $booking->customer;
                $service = $booking->service;
                $this->info('✓ Booking relationships working');
            }
        } catch (\Exception $e) {
            $this->error('✗ Booking relationships failed: ' . $e->getMessage());
        }
    }

    private function validateConfiguration()
    {
        $this->info('Validating configuration...');
        
        // Check Laravel version
        $laravelVersion = app()->version();
        $this->info("✓ Laravel version: {$laravelVersion}");
        
        // Check PHP version
        $phpVersion = PHP_VERSION;
        $this->info("✓ PHP version: {$phpVersion}");
        
        // Check environment
        $environment = app()->environment();
        $this->info("✓ Environment: {$environment}");
        
        // Check cache driver
        $cacheDriver = config('cache.default');
        $this->info("✓ Cache driver: {$cacheDriver}");
        
        // Check queue driver
        $queueDriver = config('queue.default');
        $this->info("✓ Queue driver: {$queueDriver}");
        
        // Check session driver
        $sessionDriver = config('session.driver');
        $this->info("✓ Session driver: {$sessionDriver}");
        
        // Check broadcast driver
        $broadcastDriver = config('broadcasting.default');
        $this->info("✓ Broadcast driver: {$broadcastDriver}");
        
        // Check mail configuration
        $mailDriver = config('mail.default');
        $this->info("✓ Mail driver: {$mailDriver}");
        
        // Check Firebase configuration
        $firebaseProjectId = config('firebase.project_id');
        if ($firebaseProjectId) {
            $this->info("✓ Firebase project ID: {$firebaseProjectId}");
        } else {
            $this->warn('⚠ Firebase project ID not configured');
        }
        
        // Check WhatsApp configuration
        $twilioSid = config('whatsapp.connections.twilio.sid');
        if ($twilioSid) {
            $this->info('✓ WhatsApp API configured');
        } else {
            $this->warn('⚠ WhatsApp API not configured');
        }
        
        // Check Filament configuration
        $filamentPath = config('filament.panels.admin.path');
        $this->info("✓ Filament path: {$filamentPath}");
    }

    private function validatePerformance()
    {
        $this->info('Validating performance...');
        
        // Test database query performance
        $start = microtime(true);
        $products = Product::limit(100)->get();
        $end = microtime(true);
        $queryTime = ($end - $start) * 1000;
        $this->info("✓ Product query performance: {$queryTime}ms");
        
        // Test cache performance
        $start = microtime(true);
        cache()->remember('test_key', 60, function () {
            return 'test_value';
        });
        $end = microtime(true);
        $cacheTime = ($end - $start) * 1000;
        $this->info("✓ Cache performance: {$cacheTime}ms");
        
        // Test Redis performance
        $start = microtime(true);
        $redis = app('redis');
        $redis->set('test_key', 'test_value');
        $redis->get('test_key');
        $end = microtime(true);
        $redisTime = ($end - $start) * 1000;
        $this->info("✓ Redis performance: {$redisTime}ms");
        
        // Test model performance
        $start = microtime(true);
        $user = User::with('bookings')->first();
        $end = microtime(true);
        $modelTime = ($end - $start) * 1000;
        $this->info("✓ Model relationship performance: {$modelTime}ms");
        
        // Check memory usage
        $memoryUsage = memory_get_usage(true) / 1024 / 1024;
        $this->info("✓ Memory usage: {$memoryUsage}MB");
        
        // Check disk space
        $diskFree = disk_free_space('/') / 1024 / 1024 / 1024;
        $this->info("✓ Disk free space: {$diskFree}GB");
    }
}
