<?php

namespace Tests\Feature\Api\Article;

use App\Models\{Article, User};
use Tests\TestCase;

class DeleteArticleTest extends TestCase 
{
    private Article $article;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $article = Article::factory()->create();

        $user = User::factory()->create();

        $this->article = $article;
        $this->user = $user;
    }

    public function testDeleteArticle(): void 
    {
        $this->actingAs($this->article->author, 'api')
            ->deleteJson("/api/articles/{$this->article->slug}")
            ->assertNoContent();

        $this->assertModelMissing(($this->article));
    }

    public function testDeleteForeignArticle(): void 
    {
        $this->actingAs($this->user, 'api')
            ->deleteJson("/api/articles/{$this->article->slug}")
            ->assertForbidden();

        $this->assertModelExists($this->article);
    }

    public function testDeleteNonExistentArticle(): void {
        $this->actingAs($this->user, 'api')
            ->deleteJson("/api/articles/non-existent")
            ->assertNotFound();
    }

    public function testDeleteArticleWithoutAuth(): void 
    {
        $this->deleteJson("/api/articles/{$this->article->slug}")
            ->assertUnauthorized();
        
        $this->assertModelExists($this->article);
    }
}
