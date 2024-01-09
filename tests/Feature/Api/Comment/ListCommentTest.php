<?php

namespace Tests\Feature\Api\Comment;

use App\Models\{Article, Comment, User};
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ListCommentTest extends TestCase 
{
    public function testListArticleCommentsWithoutAuth(): void 
    {
        $article = Article::factory()
            ->has(Comment::factory()->count(5), 'comments')->create();

        $comment = $article->comments->first();
        $author = $comment->author;

        $response = $this->getJson("/api/articles/".$article->slug."/comments");

        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) => 
                $json->has('comments', 5, fn (AssertableJson $item) => 
                    $item->where('id', $comment->getKey())
                        ->whereAll([
                            'createdAt' => $comment->created_at?->toISOString(),
                            'updatedAt' => $comment->updated_at?->toISOString(),
                            'body' => $comment->body,
                        ])
                        ->has('author', fn (AssertableJson $subItem) =>
                            $subItem->missing('following')
                                ->whereAll([
                                    'username' => $author->username,
                                    'bio' => $author->bio,
                                    'image' => $author->image,
                                ])
                        )
                )
            )  
        ;

    }

    public function testListArticleCommentsFollowedAuthor(): void 
    {
        $comment = Comment::factory()->create();
        $author = $comment->author;

        $follower = User::factory()->hasAttached($author, [], 'authors')->create();

        $article = $comment->article;

        $response = $this->actingAs($follower, 'api')
            ->getJson("/api/articles/".$article->slug."/comments");

        $response->assertOk()
            ->assertJsonPath('comments.0.author.following', true);
    }

    public function testListArticleCommentsUnfollowedAuthor(): void 
    {
        $user = User::factory()->create();

        $article = Article::factory()->has(Comment::factory(), 'comments')->create();

        $response = $this->actingAs($user, 'api')
            ->getJson("/api/articles/".$article->slug."/comments");

        $response->assertOk()
            ->assertJsonPath("comments.0.author.following", false);
    }

    public function testListEmptyArticleComments(): void 
    {
        $article = Article::factory()->create();

        $response = $this->getJson("/api/articles/".$article->slug."/comments");

        $response->assertOk()
            ->assertExactJson(['comments' => []]);
    }

    public function testListCommentsOfNonExistentArticle(): void 
    {
        $this->getJson("/api/articles/non-existent/comments")->assertNotFound();
    }
}


