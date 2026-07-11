<?php

namespace App\Providers;

use App\Models\KelasMapel;
use App\Policies\KelasMapelPolicy;
use App\Policies\WaliKelasPolicy;
use Illuminate\Support\Facades\Gate;
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
        // Daftarkan policy
        Gate::define('mengajar', [KelasMapelPolicy::class, 'mengajar']);
        Gate::define('kelola-wali-kelas', [WaliKelasPolicy::class, 'kelola']);
        Gate::define('lihat-laporan-wali-kelas', [WaliKelasPolicy::class, 'lihatLaporan']);
    }
}
