<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ApiClient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'client_id', 'client_secret_hash', 'user_id', 'description',
        'allowed_ips', 'rate_limit_per_minute', 'rate_limit_per_day',
        'is_active', 'last_access_at', 'scopes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_access_at' => 'datetime',
    ];

    protected $hidden = ['client_secret_hash'];

    public function user() { return $this->belongsTo(User::class); }
    public function tokenLogs() { return $this->hasMany(ApiTokenLog::class); }
    public function securityEvents() { return $this->hasMany(SecurityEvent::class); }

    public static function generateCredentials(): array
    {
        $clientId = Str::random(40);
        $clientSecret = Str::random(64);
        return [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'client_secret_hash' => hash('sha256', $clientSecret),
        ];
    }
}
