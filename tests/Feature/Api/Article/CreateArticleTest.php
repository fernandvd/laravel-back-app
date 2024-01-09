<?php

namespace Tests\Feature\Api\Article;

use App\Models\{User, Tag, Article};
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use WithFaker;

    public function testCreateArticle(): void 
    {
        $author = User::factory()->create();

        $title = 'Original title';
        $description = fake()->paragraph();
        $body = $this->faker()->text();
        $tags = ['one', 'two', 'three', 'four', 'five'];
        $slug = "different-slug";

        $response = $this->actingAs($author, 'api')
            ->postJson('/api/articles', [
                'article' => [
                    'title' => $title,
                    'slug' => $slug,
                    'description' => $description,
                    'body' => $body,
                    'tagList' => $tags,
                ],
            ]);

        $response->assertCreated()
            ->assertJson(fn (AssertableJson $json) => 
                $json->has('article', fn(AssertableJson $item) => 
                    $item->where('tagList', $tags)
                        ->whereAll([
                            'slug' => $slug,
                            'title' => $title,
                            'description' => $description,
                            'body' => $body,
                            'favorited' => false,
                            'favoritesCount' => 0,
                        ])
                        ->whereAllType([
                            'createdAt' => 'string',
                            'updatedAt' => 'string',
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

    public function testCreateArticleEmptyTags(): void 
    {
        $author = User::factory()->create();

        $response = $this->actingAs($author, 'api')
            ->postJson('/api/articles', [
                'article' => [
                    'title' => $this->faker->sentence(4),
                    'slug' => $this->faker->unique()->sentence(4),
                    'description' => $this->faker->paragraph(),
                    'body' => $this->faker->text(),
                    'tagList' => [],
                ]
                ]);
        $response->assertCreated()
            ->assertJsonPath('article.tagList', []);


    }

    public function testCreateArticleExistingTags(): void 
    {
        $author = User::factory()->create();

        $tags = Tag::factory()->count(5)->create();

        $tagsList = $tags->pluck('name')->toArray();

        $response = $this->actingAs($author, 'api')
            ->postJson('/api/articles', [
                'article' => [
                    'title' =>  $this->faker->sentence(4),
                    'description' => $this->faker->paragraph(),
                    'body' => $this->faker->text(),
                    'tagList' => $tagsList,
                    'slug' => $this->faker->unique()->sentence(4),
                ]
                ]);
        
        $response->assertCreated()
                ->assertJsonPath('article.tagList', $tagsList);
        $this->assertDatabaseCount('tags', 5);
        $this->assertDatabaseCount('article_tag', 5);
    }

    public function testCreateArticleValidationUnique(): void 
    {
        $article = Article::factory()->create();

        $response = $this->actingAs($article->author, 'api')
            ->postJson('/api/articles', [
                'article' => [
                    'title' => $article->title,
                    'description' => $this->faker->paragraph(),
                    'body' => $this->faker->text(),
                ],
            ]);
        $response->assertUnprocessable()
            ->assertInvalid('slug');
    }

    public function testCreateArticleWithoutAuth(): void 
    {
        $response = $this->postJson('/api/articles', [
                'article' => [
                    'title' => fake()->sentence(4),
                    'description' => $this->faker->paragraph(),
                    'body' => $this->faker->text(),
                ],
            ]);
        
        $response->assertUnauthorized();
    }

}
