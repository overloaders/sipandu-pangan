<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasActivityLog;

    protected $fillable = [
        'name', 'email', 'password', 'nip', 'phone', 'foto',
        'unit_id', 'is_active', 'two_factor_enabled',
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
        ];
    }

    // Relationships
    public function unit() { return $this->belongsTo(Unit::class); }
    
    public function roles() { return $this->belongsToMany(Role::class); }

    /**
     * Cache hasil pengecekan role per request untuk menghindari query berulang
     * (hasRole dipanggil banyak kali per halaman: sidebar, UnitScope, controller, dll).
     */
    protected array $roleCheckCache = [];

    /**
     * Check if user has any of the given roles
     */
    public function hasRole($slugs): bool
    {
        if (is_string($slugs)) $slugs = [$slugs];
        sort($slugs);
        $key = implode('|', $slugs);
        if (!array_key_exists($key, $this->roleCheckCache)) {
            $this->roleCheckCache[$key] = $this->roles()->whereIn('slug', $slugs)->exists();
        }
        return $this->roleCheckCache[$key];
    }

    /**
     * Check if user has ALL of the given roles
     */
    public function hasAllRoles(array $slugs): bool
    {
        foreach ($slugs as $slug) {
            if (!$this->hasRole($slug)) return false;
        }
        return true;
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    /**
     * Check if user can manage data (create/edit/delete)
     */
    public function canManage(): bool
    {
        return $this->hasRole(['super-admin', 'admin']);
    }

    /**
     * Check if user can input data
     */
    public function canInput(): bool
    {
        return $this->hasRole(['super-admin', 'admin', 'supervisor', 'operator']);
    }

    /**
     * Check if user can view data
     */
    public function canView(): bool
    {
        return $this->hasRole(['super-admin', 'admin', 'supervisor', 'operator', 'keuangan']);
    }

    /**
     * Check if user can access financial module
     */
    public function canAccessFinance(): bool
    {
        return $this->hasRole(['super-admin', 'admin', 'keuangan']);
    }

    /**
     * Check if user can manage master data
     */
    public function canManageMaster(): bool
    {
        return $this->hasRole(['super-admin', 'admin']);
    }

    /**
     * Check if user can delete data
     */
    public function canDelete(): bool
    {
        return $this->hasRole(['super-admin', 'admin']);
    }

    /**
     * Get role label for display
     */
    public function getRoleLabel(): string
    {
        $role = $this->roles->first();
        return $role ? $role->name : 'User';
    }
}
