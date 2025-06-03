<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade; // <-- Tambahkan ini
use Carbon\Carbon;

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
        // Daftarkan komponen Blade untuk <x-admin-layout>
        Blade::component('layouts.admin', 'admin-layout');
         Schema::defaultStringLength(191);

        // ⬇️ Tambahkan ini: Share Carbon ke seluruh view Blade
        Blade::directive('carbon', function ($expression) {
            return "<?php echo \\Carbon\\Carbon::parse($expression); ?>";
        });
    }
}
