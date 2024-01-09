<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{


    public static $wrap = 'article';

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'slug' => $this->resource->slug,
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'body' => $this->resource->body,
            'tagList' => new TagsCollection($this->resource->tags),
            'createdAt' => $this->resource->created_at,
            'updatedAt' => $this->resource->updated_at,
            'favorited' => $this->when($user !== null, fn() => $this->resource->favoredBy($user)),
            'favoritesCount' => $this->resource->favoredUsers->count(),
            'author' => new ProfileResource($this->resource->author),
        ];
    }
}
