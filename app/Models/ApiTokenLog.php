<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiTokenLog extends Model
{
    protected $fillable = [
        'api_client_id', 'user_id', 'personal_access_token_id', 'endpoint',
        'method', 'ip_address', 'user_agent', 'response_code',
        'response_time_ms', 'request_params', 'request_id',
    ];

    protected $casts = ['response_time_ms' => 'integer'];

    public function apiClient() { return $this->belongsTo(ApiClient::class); }
    public function user() { return $this->belongsTo(User::class); }
}
