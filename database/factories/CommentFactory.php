<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Article, User};


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'article_id' => Article::factory(),
            'author_id' => User::factory(),
            'body' => $this->faker->sentence(),
            'created_at' => function (array $attributes) {
                $article = Article::find($attributes['article_id']);
                return $this->faker->dateTimeBetween($article->created_at);
            },
            'updated_at' => function (array $attrs) {
                return $attrs['created_at'];
            },
        ];
    }
}
