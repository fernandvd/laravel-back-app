<?php

namespace Tests\Feature\Api\Article;

use App\Models\{User, Tag, Article};
use Illuminate\Support\Arr;
use Illuminate\Testing\AssertableJsonString;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ListArticleTest extends TestCase 
{
    protected function setUp(): void 
    {
        parent::setUp();

        Article::factory()->count(30)->create();
    }

    public function testListArticles(): void 
    {
        $response = $this->getJson('/api/articles');

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => 
                $json->where('articlesCount', 20)
                    ->count('articles', 20)
                    ->has('articles', fn (AssertableJson $items) => 
                        $items->each(fn (AssertableJson $item) => 
                            $item->missing('favorited')
                                ->whereAllType([
                                    'slug' => 'string',
                                    'title' => 'string',
                                    'description' => 'string',
                                    'body' => 'string',
                                    'createdAt' => 'string',
                                    'updatedAt' => 'string',
                                ])
                                ->whereAll([
                                    'tagList' => [],
                                    'favoritesCount' => 0,
                                ])
                                ->has('author', fn (AssertableJson $subItem) => 
                                    $subItem->missing('following')
                                        ->whereAllType([
                                            'username' => 'string',
                                            'bio' => 'string|null',
                                            'image' => 'string|null',
                                        ])
                                )
                        )
                    )
        );
    }

    public function testListArticlesByTag(): void 
    {
        Article::factory()->has(Tag::factory()->count(3))->count(20)->create();

        $tag = Tag::factory()->has(Article::factory()->count(10), 'articles')->create();

        $response = $this->getJson("/api/articles?tag={$tag->name}");

        $response->assertOk()
            ->assertJsonPath('articlesCount', 10)
            ->assertJsonCount(10, 'articles');

        foreach ($response['articles'] as $article) {
            $this->assertContains(
                $tag->name, Arr::get($article, 'tagList'),
                "Article must have tag {$tag->name}"
            );
        }
    }

    public function testListArticlesByAuthor(): void 
    {
        $author = User::factory()->has(Article::factory()->count(5), 'articles')->create();

        $response = $this->getJson("/api/articles?author={$author->username}");

        $response->assertOk()
            ->assertJsonPath('articlesCount', 5)
            ->assertJsonCount(5, 'articles');

        foreach($response['articles'] as $article) {
            $this->assertSame(
                $author->username,
                Arr::get($article, 'author.username'),
                "Author must be {$author->username}"
            );
        }

    }

    public function testListArticlesByFavored(): void 
    {
        $user = User::factory()->has(Article::factory()->count(15), 'favorites')->create();

        $response = $this->getJson("/api/articles?favorited={$user->username}");

        $response->assertOk()
            ->assertJsonPath('articlesCount', 15)
            ->assertJsonCount(15, 'articles');

        // verify favored
        foreach ($response['articles'] as $article) {
            $this->assertSame(
                1, Arr::get($article, 'favoritesCount'),
                "Article must be favored by {$user->username}."
            );
        }
    }

    public function testArticlesFeedLimit(): void 
    {
        $response = $this->getJson('/api/articles?limit=25');

        $response->assertOk()
            ->assertJsonPath('articlesCount', 25)
            ->assertJsonCount(25, 'articles');
    }

    public function testArticleFeedOffset(): void 
    {
        $response = $this->getJson('/api/articles?offset=20');

        $response->assertOk()
            ->assertJsonPath('articlesCount', 10)
            ->assertJsonCount(10, 'articles');
    }


}
