<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (class_exists('OwenIt\Auditing\Auditable')) {
            // It exists, do nothing
        } else {
            // Manually require the files
            $auditPath = base_path('vendor/owen-it/laravel-auditing/src/');

            if (file_exists($auditPath . 'Auditable.php')) {
                require_once $auditPath . 'Auditable.php';
            }

            if (file_exists($auditPath . 'AuditingServiceProvider.php')) {
                require_once $auditPath . 'AuditingServiceProvider.php';
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.custom');
    }
}
