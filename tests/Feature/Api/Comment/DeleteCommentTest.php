<?php

namespace Tests\Feature\Api\Comment;

use App\Models\{Article, Comment, User};
use Tests\TestCase;

class DeleteCommentTest extends TestCase 
{
    private Comment $comment;
    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();

        $comment = Comment::factory()->create();

        $this->comment = $comment;
        $this->article = $comment->article;
    }

    public function testDeleteArticleComment(): void 
    {
        $this->actingAs($this->comment->author, 'api')
            ->deleteJson("/api/articles/".$this->article->slug."/comments/".$this->comment->getKey())->assertNoContent()
            ;

        
        $this->assertModelMissing($this->comment);  
    }

    public function testDeleteCommentOfNonExistentArticle(): void 
    {
        $this->assertNotSame($nonExistentSlug = 'non-existent', $this->article->slug);

        $this->actingAs($this->comment->author, 'api')
            ->deleteJson("/api/articles/".$nonExistentSlug."/comments/".$this->comment->getKey())->assertNotFound();

        $this->assertModelExists($this->comment);
    }

    public function testDeleteNonExistenArticleComment($nonExistentId = "Non-existen-id"): void 
    {
        $this->assertNotEquals($nonExistentId, $this->comment->getKey());

        $this->actingAs($this->comment->author, 'api')
            ->deleteJson("/api/articles/".$this->article->slug."/comments/".$nonExistentId)->assertNotFound();

        $this->assertModelExists($this->comment);
    }

    public function testDeleteForeignArticleComment(): void {
        $user = User::factory()->create();

        $this->actingAs($user, 'api')
            ->deleteJson("/api/articles/".$this->article->slug."/comments/".$this->comment->getKey())->assertForbidden();

        $this->assertModelExists($this->comment);
    }

    public function testDeleteCommentOfForeignArticle(): void 
    {
        $article = Article::factory()->create();

        $this->actingAs($this->comment->author, 'api')
            ->deleteJson("/api/articles/".$article->slug."/comments/".$this->comment->getKey())->assertNotFound();
        
        $this->assertModelExists($this->comment);
    }

    public function testDeleteCommentWithoutAuth(): void 
    {
        $this->deleteJson("/api/articles/".$this->article->slug."/comments/".$this->comment->getKey())->assertUnauthorized();

        $this->assertModelExists($this->comment);
    }

    public function nonExistentIdProvider(): array 
    {
        return [
            'int key' => [123],
            'string key' => ['non-existent'],
        ];
    }


}

