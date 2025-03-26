<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot()
    {
        Paginator::useBootstrap();
        
        // Force HTTPS in production
        if(config('app.env') === 'production' || config('force_https')) {
            URL::forceScheme('https');
        }
        
        // Add error handling for pagination views
        View::share('paginationError', function ($e) {
            Log::error('Pagination error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        });
    }
}