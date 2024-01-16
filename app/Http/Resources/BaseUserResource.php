<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'username' => $this->resource->username,
            'bio' => $this->resource->bio,
            'image' => $this->resource->image,
            'roles' => $this->resource->getRoleNames()->pluck('name'),
        ];
    }
}
