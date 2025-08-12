<?php

namespace App\Providers;

use App\Services\StorageService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register custom Blade directive for file URLs
        Blade::directive('fileUrl', function ($expression) {
            return "<?php echo \App\Services\StorageService::url($expression); ?>";
        });

        // Register global helper function for file URLs
        if (!function_exists('storage_url')) {
            function storage_url($path) {
                return StorageService::url($path);
            }
        }
    }
}
