<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CacheStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:stats {--clear : Clear all application cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display cache statistics and optionally clear cache';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('clear')) {
            CacheService::clearAll();
            $this->info('✅ All application cache cleared!');
            return Command::SUCCESS;
        }

        $this->info('📊 Cache Statistics');
        $this->newLine();

        $driver = config('cache.default');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Cache Driver', $driver],
                ['Session Driver', config('session.driver')],
            ]
        );

        if ($driver === 'redis') {
            try {
                $stats = CacheService::getStats();
                
                $this->newLine();
                $this->info('🔴 Redis Info');
                $this->table(
                    ['Metric', 'Value'],
                    collect($stats)->map(fn($v, $k) => [$k, $v])->values()->toArray()
                );
            } catch (\Exception $e) {
                $this->error('❌ Could not connect to Redis: ' . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info('💡 Tips:');
        $this->line('  • Use "php artisan cache:stats --clear" to clear all cache');
        $this->line('  • Use "php artisan cache:clear" to clear Laravel cache');
        $this->line('  • Use "php artisan config:clear" to clear config cache');

        return Command::SUCCESS;
    }
}
