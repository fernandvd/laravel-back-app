<?php

namespace Tests\Feature\Api\Favorites;


use Tests\TestCase;
use App\Models\{Article, User};

class RemoveFavoritesTest extends TestCase 
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->user = $user;
    }

    public function testRemoveArticleFromFavorites(): void {
        $article = Article::factory()
            ->hasAttached($this->user, [], 'favoredUsers')->create();

        $response = $this->actingAs($this->user, "api")
            ->deleteJson("/api/articles/{$article->slug}/favorite");
        $response->assertOk()
            ->assertJsonPath("article.favorited", false)
            ->assertJsonPath("article.favoritesCount", 0);

        $this->assertTrue($this->user->favorites->doesntContain($article));

        $this->actingAs($this->user, "api")
            ->deleteJson("/api/articles/{$article->slug}/favorite")->assertOk();
    }

    public function testRemoveNonExistentArticleFromFavorites(): void 
    {
        $this->actingAs($this->user, "api")
            ->deleteJson("/api/articles/non-existent/favorite")
            ->assertNotFound();

    }

    public function testRemoveArticleFromFavoritesWithoutAuth(): void 
    {
        $article = Article::factory()->create();

        $this->deleteJson("/api/articles/{$article->slug}/favorite")->assertUnauthorized();
    }

}
