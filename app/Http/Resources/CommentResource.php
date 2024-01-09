<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{

    public static $wrap = 'comment';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'createdAt' => $this->resource->created_at,
            'updatedAt' => $this->resource->updated_at,
            'body' => $this->resource->body,
            'author' => new ProfileResource($this->resource->author),
        ];
    }
}
