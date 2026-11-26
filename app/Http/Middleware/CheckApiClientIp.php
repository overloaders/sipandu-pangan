<?php

namespace App\Http\Middleware;

use App\Models\ApiClient;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiClientIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if request has API client credentials
        $clientId = $request->header('X-Client-ID');
        
        if ($clientId) {
            $client = ApiClient::where('client_id', $clientId)
                ->where('is_active', true)
                ->first();
            
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'API client tidak valid atau tidak aktif',
                    'error' => 'invalid_client',
                ], 401);
            }
            
            // Check IP whitelist
            if ($client->allowed_ips) {
                $allowedIps = json_decode($client->allowed_ips, true);
                if (!in_array($request->ip(), $allowedIps)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'IP address tidak diizinkan untuk client ini',
                        'error' => 'ip_not_allowed',
                    ], 403);
                }
            }
            
            // Check rate limit
            $client->update(['last_access_at' => now()]);
            
            // Store client in request for later use
            $request->attributes->set('api_client', $client);
        }
        
        return $next($request);
    }
}
