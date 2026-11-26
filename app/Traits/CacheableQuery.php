<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

/**
 * Reusable trait for API performance optimization.
 * Provides caching, search, and consistent pagination.
 */
trait CacheableQuery
{
    /**
     * Execute a cached, searchable, paginated query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $cachePrefix Cache key prefix (e.g., 'wbp_list')
     * @param int $cacheMinutes Cache duration in minutes
     * @param array $searchable Fields to search on
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function cachedPaginate($query, string $cachePrefix, int $cacheMinutes = 5, array $searchable = [], Request $request = null)
    {
        $request = $request ?? request();

        // Apply search filter
        if ($request->filled('search') && !empty($searchable)) {
            $search = $request->search;
            $query->where(function ($q) use ($searchable, $search) {
                foreach ($searchable as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply unit filter
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        // Apply date range filter
        if ($request->filled('from_date')) {
            $dateField = $this->getDateField();
            $query->where($dateField, '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $dateField = $this->getDateField();
            $query->where($dateField, '<=', $request->to_date);
        }

        // Build cache key from all request parameters
        $params = $request->only(['search', 'status', 'unit_id', 'from_date', 'to_date', 'page', 'per_page']);
        ksort($params);
        $cacheKey = $cachePrefix . '.' . md5(json_encode($params));

        // Cache the paginated result
        $perPage = min($request->get('per_page', 15), 100); // Max 100 per page

        return Cache::remember($cacheKey, $cacheMinutes * 60, function () use ($query, $perPage) {
            return $query->orderByDesc('created_at')->paginate($perPage);
        });
    }

    /**
     * Clear cache for a specific prefix.
     */
    protected function clearCache(string $prefix): void
    {
        // In production, use Cache Tags for more precise invalidation
        // For now, we rely on TTL-based expiration
    }

    /**
     * Get the date field name for date range filtering.
     * Override this in controllers that use different date fields.
     */
    protected function getDateField(): string
    {
        return 'created_at';
    }
}
