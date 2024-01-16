<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\{User, Article, Tag, Comment};
use Illuminate\Database\Eloquent\Factories\Sequence;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory()->count(20)->create();

        foreach ($users as $user) {
            $user->followers()->attach($users->random(0,5));
        }

        $articles = Article::factory()
            ->count(30)
            ->state(new Sequence(fn() => [
                'author_id' => $users->random()
            ]))
            ->create();

        $tags = Tag::factory()->count(20)->create();

        foreach ($articles as $article) {
            $article->tags()->attach($tags->random(0,6));
            $article->favoredUsers()->attach($users->random());
        }

        Comment::factory()->count(60)
            ->state(new Sequence(fn() => [
                'article_id' => $articles->random(),
                'author_id' => $users->random(),
            ]))
            ->create();

        $this->call([
            RolAndPermissionSeeder::class,
        ]);
    }
}
