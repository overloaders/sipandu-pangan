<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'client_id' => $this->client_id,
            'user_id' => $this->user_id,
            'description' => $this->description,
            'allowed_ips' => $this->allowed_ips,
            'rate_limit_per_minute' => $this->rate_limit_per_minute,
            'rate_limit_per_day' => $this->rate_limit_per_day,
            'is_active' => $this->is_active,
            'last_access_at' => $this->last_access_at?->toISOString(),
            'scopes' => $this->scopes,
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
