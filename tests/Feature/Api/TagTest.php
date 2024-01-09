<?php

namespace Tests\Feature\Api;

use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;
use Tests\TestCase;

class TagTest extends TestCase 
{
    public function testReturnsTagsList(): void 
    {
        $tags = Tag::factory()->count(5)->create();

        $response = $this->getJson("/api/tags");

        $response->assertOk()
            ->assertExactJson([
                'tags' => $tags->pluck('name'),
            ]);
    }

    public function testReturnsEmptyTagsList(): void 
    {
        $response = $this->getJson("/api/tags");

        $response->assertOk()
            ->assertExactJson(['tags' => []]);
    }

    public function testTagResource(): void 
    {
        $tag = Tag::factory()->create();

        $resource = new TagResource($tag);

        $request = $this->mock(Request::class);

        $tagResource = $resource->toArray($request);

        $this->assertSame(['name' => $tag->name], $tagResource);
    }

}