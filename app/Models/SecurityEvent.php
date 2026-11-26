<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityEvent extends Model
{
    protected $fillable = [
        'event_type', 'user_id', 'api_client_id', 'ip_address',
        'user_agent', 'details', 'severity', 'is_resolved', 'resolution_notes',
    ];

    protected $casts = ['is_resolved' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }
    public function apiClient() { return $this->belongsTo(ApiClient::class); }
}
