<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mengatur header cache untuk aset Swagger UI (/docs/asset/*).
 *
 * Aset sukses di-cache lama karena URL-nya sudah memakai hash isi file
 * (?v=md5), sedangkan respons error (404/5xx) tidak boleh disimpan sama
 * sekali — dulu 404 sempat ikut membawa header cache satu tahun sehingga
 * browser menyimpan kegagalan tersebut.
 */
class SwaggerAssetCacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Aset dilayani berbeda per encoding (gzip/br), jadi cache harus memisahkannya.
        $response->setVary('Accept-Encoding');

        if ($response->getStatusCode() >= 400) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->remove('Expires');
        } else {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            $response->headers->remove('Pragma');
            $response->headers->remove('Expires');
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');
        }

        return $response;
    }
}
