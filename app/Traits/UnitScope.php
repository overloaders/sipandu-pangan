<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Apply unit-based scoping to Eloquent queries.
 *
 * Usage in controller:
 *   $query = SomeModel::query();
 *   $this->applyUnitScope($query);
 *
 * Super-admin sees all data; other roles see only their unit.
 */
trait UnitScope
{
    /**
     * Scope a query to the current user's unit.
     * Super-admin bypasses this filter (sees all units).
     */
    protected function applyUnitScope(Builder $query, ?Request $request = null): Builder
    {
        $user = auth()->user();

        // Super-admin sees everything
        if ($user && $user->hasRole('super-admin')) {
            return $query;
        }

        // If request has explicit unit_id filter and user has access, use it
        if ($request && $request->filled('unit_id')) {
            // Non-super-admin can only filter their own unit
            if ($user->unit_id && $request->unit_id != $user->unit_id) {
                // Ignore unauthorized unit filter — scope to user's unit
                return $query->where('unit_id', $user->unit_id);
            }
            return $query->where('unit_id', $request->unit_id);
        }

        // Default: scope to user's unit
        if ($user && $user->unit_id) {
            return $query->where('unit_id', $user->unit_id);
        }

        // User without unit — show nothing (safety net)
        return $query->whereRaw('1 = 0');
    }

    /**
     * Check if current user can access a specific unit.
     */
    protected function canAccessUnit(int $unitId): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        if ($user->hasRole('super-admin')) return true;
        return $user->unit_id == $unitId;
    }

    /**
     * IDs of units the current user may access (null = all units, super-admin).
     */
    protected function allowedUnitIds(): ?array
    {
        $user = auth()->user();
        if (!$user) return [];
        if ($user->hasRole('super-admin')) return null;
        return $user->unit_id ? [$user->unit_id] : [];
    }

    /**
     * Query builder for the units the current user may use in forms.
     * Super-admin sees all units; other roles see only their own unit.
     */
    protected function allowedUnitsQuery(): Builder
    {
        $ids = $this->allowedUnitIds();
        $query = \App\Models\Unit::query()->orderBy('nama_unit');
        if ($ids !== null) {
            $query->whereIn('id', $ids);
        }
        return $query;
    }

    /**
     * Ensure the model's unit is accessible; abort 403 otherwise.
     */
    protected function authorizeUnitModel($model): void
    {
        if (!$model || !$this->canAccessUnit((int) $model->unit_id)) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }
    }

    /**
     * Force unit_id to the current user's unit unless they are super-admin.
     * Non-super-admin users may only create data for their own unit.
     */
    protected function enforceUnitId(array $data): array
    {
        $user = auth()->user();
        if ($user && !$user->hasRole('super-admin') && $user->unit_id) {
            $data['unit_id'] = $user->unit_id;
        }
        return $data;
    }

    /**
     * Resolve the effective unit_id for a create/update request:
     * non-super-admin users are always pinned to their own unit.
     */
    protected function resolveUnitId($requested): ?int
    {
        $user = auth()->user();
        if ($user && !$user->hasRole('super-admin')) {
            return $user->unit_id;
        }
        return $requested;
    }
}
