<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearAllCaches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all caches in the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing all caches...');
        
        Artisan::call('cache:clear');
        $this->info('Application cache cleared');
        
        Artisan::call('config:clear');
        $this->info('Configuration cache cleared');
        
        Artisan::call('route:clear');
        $this->info('Route cache cleared');
        
        Artisan::call('view:clear');
        $this->info('View cache cleared');
        
        // Clear compiled views
        Artisan::call('optimize:clear');
        $this->info('Optimized class cleared');
        
        $this->info('All caches have been cleared successfully!');
        
        return Command::SUCCESS;
    }
}