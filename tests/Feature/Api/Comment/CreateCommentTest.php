<?php

namespace Tests\Feature\Api\Comment;

use App\Models\{User, Article};
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\AssertableJsonString;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CreateCommentTest extends TestCase 
{
    use WithFaker;

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

    public function testCreateCommentForArticle(): void 
    {
        $message = $this->faker->sentence();

        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/articles/{$this->article->slug}/comments", [
                'comment' => [
                    "body" => $message,
                ],
            ]);

        $response->assertCreated()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('comment', fn (AssertableJson $comment) => 
                    $comment->where('body', $message)
                        ->whereAllType([
                            'id' => 'integer',
                            'createdAt' => 'string',
                            'updatedAt' => 'string',
                        ])
                        ->has('author', fn (AssertableJson $author) =>
                            $author->whereAll([
                                'username' => $this->user->username,
                                'bio' => $this->user->bio,
                                'image' => $this->user->image,
                                'following' => false,
                            ])
                        )
                )
                            );
    }

    public function testCreateCommentValidation(array $data = [])
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/articles/".$this->article->slug."/comments", $data);

        $response->assertUnprocessable()->assertInvalid("body");
    }

    public function testCreateCommentForNonExistentArticle(): void 
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson("/api/articles/non-existent-article/comments", [
                'comment' => [
                    'body' => $this->faker->sentence(),
                ],
            ]);

        $response->assertNotFound();
    }

    public function testCreateCommentWithoutAuth(): void 
    {
        $response = $this->postJson("/api/articles/".$this->article->slug."/comments", [
            'comment' => [
                'body' => $this->faker->sentence(),
            ],
        ]);

        $response->assertUnauthorized();
    }

}
