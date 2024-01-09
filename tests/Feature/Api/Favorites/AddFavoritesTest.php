<?php

namespace Tests\Feature\Api\Favorites;

use Tests\TestCase;
use App\Models\{User, Article};

class AddFavoritesTest extends TestCase 
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->user = $user;
    }

    public function testAddArticleToFavorites(): void 
    {
        $article = Article::factory()->create();

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/articles/{$article->slug}/favorite");

        $response->assertOk()
            ->assertJsonPath("article.favorited", true)
            ->assertJsonPath("article.favoritesCount",1);

        $this->assertTrue($this->user->favorites->contains($article));

        $repeatedResponse = $this->actingAs($this->user, 'api')
            ->postJson("/api/articles/{$article->slug}/favorite");

        $repeatedResponse->assertOk()
            ->assertJsonPath("article.favoritesCount", 1);

        $this->assertDatabaseCount("article_favorite", 1);

    }

    public function testAddNonExistentArticleToFavorites(): void 
    {
        $this->actingAs($this->user, 'api')
            ->postJson("/api/articles/non-existent/favorite")->assertNotFound();
    }

    public function testAddArticleToFavoritesWithoutAuth(): void 
    {
        $article = Article::factory()->create();

        $this->postJson("/api/articles/{$article->slug}/favorite")
            ->assertUnauthorized();
    }
}

