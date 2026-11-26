<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
        // Force HTTPS — nginx terminates SSL and forwards plain HTTP to PHP-FPM,
        // so Laravel thinks the request is HTTP. Without this, route() generates
        // http:// URLs which causes Mixed Content errors in the browser.
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // AdminLTE 3 ships Bootstrap 4 — render pagination with the Bootstrap 4 view
        // (the default Tailwind view renders unstyled/huge here since Tailwind CSS is not loaded)
        Paginator::useBootstrapFour();

        $this->configureRateLimiting();
        $this->configureSlowQueryMonitoring();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Rate limit for API endpoints (60 requests per minute)
        RateLimiter::for('api', function (\Illuminate\Http\Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limit for login attempts (5 attempts per minute)
        RateLimiter::for('login', function (\Illuminate\Http\Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Rate limit for registration (3 attempts per minute)
        RateLimiter::for('register', function (\Illuminate\Http\Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        // Rate limit for password reset (3 attempts per minute)
        RateLimiter::for('password-reset', function (\Illuminate\Http\Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });
    }

    /**
     * Slow query monitoring: log query yang melebihi ambang durasi dan
     * request dengan jumlah query berlebihan (indikasi N+1) ke channel
     * "slow-queries" (storage/logs/slow-queries-*.log). Aktif hanya jika
     * SLOW_QUERY_LOG=true — overhead listener minimal (hitung + bandingkan).
     */
    protected function configureSlowQueryMonitoring(): void
    {
        if (!config('database.slow_query.enabled')) {
            return;
        }

        $thresholdMs = (int) config('database.slow_query.threshold_ms', 300);
        $maxQueries = (int) config('database.slow_query.max_queries_per_request', 100);

        // State per request: jumlah query & total durasi
        $state = new class {
            public int $count = 0;
            public float $totalMs = 0;
        };

        DB::listen(function ($query) use ($thresholdMs, $state) {
            $state->count++;
            $state->totalMs += $query->time;

            if ($query->time >= $thresholdMs) {
                Log::channel('slow-queries')->warning('Slow query', [
                    'time_ms' => round($query->time, 1),
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'request' => $this->requestContext(),
                ]);
            }
        });

        // Setelah request selesai, tandai request dengan jumlah query berlebihan (N+1)
        app()->terminating(function () use ($maxQueries, $state) {
            if ($state->count >= $maxQueries) {
                Log::channel('slow-queries')->warning('High query count for request', [
                    'queries' => $state->count,
                    'total_db_ms' => round($state->totalMs, 1),
                    'request' => $this->requestContext(),
                ]);
            }
        });
    }

    /**
     * Konteks request untuk log (aman dipanggil dari console — request null).
     */
    protected function requestContext(): array
    {
        $request = request();
        return [
            'method' => $request?->method(),
            'url' => $request?->fullUrl(),
            'user_id' => auth()->id(),
            'ip' => $request?->ip(),
        ];
    }
}
