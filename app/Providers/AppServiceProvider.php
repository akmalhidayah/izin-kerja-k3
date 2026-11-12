<?php

namespace App\Providers;

use App\Models\Notification;
use App\Models\StepApproval;
use Illuminate\Support\Facades\Blade; // <-- Tambahkan ini
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
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

        // Angka badge sidebar admin: Perlu Tindakan vendor/karyawan dan SIK yang belum lengkap tanda tangannya.
        // Disimpan 60 detik supaya pindah halaman tidak menambah query; bila cache/DB gagal, sidebar tetap tampil.
        View::composer('layouts.admin', function ($view) {
            $user = auth()->user();
            if (! $user || ! $user->isAdmin()) {
                $view->with('sidebarBadges', []);

                return;
            }

            $compute = fn () => [
                'vendor' => Notification::query()->ofVendor()->perluTindakan()->count(),
                'pgo' => Notification::query()->ofPgo()->perluTindakan()->count(),
                'sik' => StepApproval::query()
                    ->where('step', 'sik')
                    ->where('status', 'disetujui')
                    ->where(fn ($q) => $q->whereNull('signature_senior_manager')->orWhereNull('signature_manager'))
                    ->count(),
            ];

            try {
                $badges = Cache::remember('admin.sidebar_badges', 60, $compute);
            } catch (\Throwable $e) {
                try {
                    $badges = $compute();
                } catch (\Throwable $e) {
                    $badges = [];
                }
            }

            $view->with('sidebarBadges', $badges);
        });

        // ⬇️ Tambahkan ini: Share Carbon ke seluruh view Blade
        Blade::directive('carbon', function ($expression) {
            return "<?php echo \\Carbon\\Carbon::parse($expression); ?>";
        });
    }
}
