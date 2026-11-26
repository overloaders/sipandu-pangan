<?php

namespace App\Http\Middleware;

use App\Models\ApiTokenLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $response = $next($request);
        $duration = round((microtime(true) - $startTime) * 1000);

        try {
            ApiTokenLog::create([
                'api_client_id' => $request->user()?->currentAccessToken()?->name,
                'user_id' => $request->user()?->id,
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent(), 0, 500),
                'response_code' => $response->getStatusCode(),
                'response_time_ms' => $duration,
                'request_id' => $request->header('X-Request-Id'),
            ]);
        } catch (\Exception $e) {
            \Log::warning('API log failed: ' . $e->getMessage());
        }

        return $response;
    }
}
