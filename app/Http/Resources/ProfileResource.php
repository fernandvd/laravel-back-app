<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ProfileResource extends BaseUserResource
{

    public static $wrap = 'profile';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return array_merge(parent::toArray($request), [
            'following' => $this->when($user !== null, fn() => $user->following($this->resource))
        ]);
    }
}
