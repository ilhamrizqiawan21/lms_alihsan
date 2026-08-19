<?php

namespace App\Providers;

use App\Models\KelasMapel;
use App\Policies\KelasMapelPolicy;
use App\Policies\WaliKelasPolicy;
use App\Http\Controllers\Guru\NilaiController;
use App\Http\Controllers\Guru\NilaiRekapController;
use App\Http\Controllers\Guru\SikapController;
use App\Http\Controllers\Guru\SikapRekapController;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // The existing route names are kept stable while the recap endpoints are migrated to Inertia.
        $this->app->bind(NilaiController::class, fn ($app) => $app->make(NilaiRekapController::class));
        $this->app->bind(SikapController::class, fn ($app) => $app->make(SikapRekapController::class));
    }

    public function boot(): void
    {
        Gate::define('mengajar', [KelasMapelPolicy::class, 'mengajar']);
        Gate::define('kelola-wali-kelas', [WaliKelasPolicy::class, 'kelola']);
        Gate::define('lihat-laporan-wali-kelas', [WaliKelasPolicy::class, 'lihatLaporan']);
    }
}
