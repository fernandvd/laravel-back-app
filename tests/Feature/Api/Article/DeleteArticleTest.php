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

    public function testDeleteArticleWithUserAdmin()
    {
        $article = Article::factory()->create();
        $this->artisan('db:seed', ['--class' => 'RolAndPermissionSeeder']);
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $userAdmin = User::factory()->rolAdmin()->create();

        $this->actingAs($userAdmin, 'api')
            ->deleteJson("/api/articles/{$article->slug}")
            ->assertNoContent();

        $this->assertModelMissing($article);

    }

    public function testDeleteArticleWithUserWithPermissionForDelete() 
    {
        $article = Article::factory()->create();
        
        $this->artisan('db:seed', ['--class' => 'RolAndPermissionSeeder']);
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $userEditor = User::factory()->rolEditor()->create();

        $this->actingAs($userEditor, 'api')
            ->deleteJson("/api/articles/{$article->slug}")
            ->assertNoContent();

        $this->assertModelMissing($article);
    }

    public function testDeleteArticleWithUserWithoutPermission() 
    {
        $article = Article::factory()->create();
        
        $this->artisan('db:seed', ['--class' => 'RolAndPermissionSeeder']);
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $userClient = User::factory()->rolClient()->create();

        $this->actingAs($userClient, 'api')
            ->deleteJson("/api/articles/{$article->slug}")
            ->assertForbidden();

        $this->assertModelExists($article);
    }
}
