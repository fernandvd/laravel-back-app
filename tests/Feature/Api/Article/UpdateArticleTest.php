<?php

namespace Tests\Feature\Api\Article;

use App\Models\{Article, User};
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateArticleTest extends TestCase
{

    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();

        $article = Article::factory()->create();

        $this->article = $article;
    }

    public function testUpdateArticle(): void 
    {
        $author = $this->article->author;

        $this->assertNotEquals($title='Updated title', $this->article->title);
        $this->assertNotEquals($fakeSlug='overwrite-slug', $this->article->slug);
        $this->assertNotEquals($description='New description', $this->article->description);
        $this->assertNotEquals($body='Updated article body', $this->article->body);

        $this->actingAs($author, 'api')
            ->putJson("/api/articles/{$this->article->slug}", [
                'article' => ['description' => $description]
            ])
            ->assertOk();
        $this->actingAs($author, 'api')
            ->putJson("/api/articles/{$this->article->slug}", ['article' => ["body" => $body]]);
        $response = $this->actingAs($author, 'api')
            ->putJson("/api/articles/{$this->article->slug}", [
                'article' => [
                    'title' => $title,
                    'slug' => $fakeSlug, // must be overwritten with title slug
                ],
            ]);

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('article', fn (AssertableJson $item) => 
                    $item->whereType('updatedAt', 'string')
                        ->whereAll([
                            'slug' => $fakeSlug,
                            'title' => $title,
                            'description' => $description,
                            'body' => $body,
                            'tagList' => [],
                            'createdAt' => $this->article->created_at?->toISOString(),
                            'favorited' => false,
                            'favoritesCount' => 0,
                        ])
                        ->has('author', fn (AssertableJson $subItem) => 
                            $subItem->whereAll([
                                'username' => $author->username,
                                'bio' => $author->bio,
                                'image' => $author->image,
                                'following' => false,
                            ])
                        )
                )
                            );
    }

    public function testUpdateForeignArticle(): void 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->putJson("/api/articles/{$this->article->slug}", [
                'article' => [
                    'body' => fake()->text(),
                ],
            ]);
        
        $response->assertForbidden();
    }

    public function testUpdateArticleValidationUnique(): void 
    {
        $anotherArticle = Article::factory()->create();

        $response = $this->actingAs($this->article->author, 'api')
            ->putJson("api/articles/".$this->article->slug, [
                'article' => [
                    'title' => $anotherArticle->title,
                ],
            ]);

        $response->assertUnprocessable()->assertInvalid(('slug'));
    }

    public function testSelfUpdateArticleValidationUnique(): void 
    {
        $response = $this->actingAs($this->article->author, 'api')
            ->putJson("/api/articles/".$this->article->slug, [
                'article' => [
                    'title' => $this->article->title,
                    'slug' => $this->article->slug,
                ],
            ]);
        
        $response->assertOk()->assertJsonPath('article.slug', $this->article->slug);
    }

    public function testUpdateNonExistentArticle(): void 
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')
        ->putJson("/api/articles/non-existent", [
            'article' => [
                'body' => fake()->text(),
            ],
        ]);

        $response->assertNotFound();
    }

    public function testUpdateArticleWithoutAuth(): void 
    {
        $response = $this->putJson("/api/articles/".$this->article->slug, [
            'article' => [
                'body' => fake()->text(),
            ],
        ]);

        $response->assertUnauthorized();
    }

}