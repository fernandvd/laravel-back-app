<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\{Account, User, Article, Tag, Comment, Contact, Organization};
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

        User::factory()->state(['email' => "dave@example.com"])->create();
        $this->call([
            RolAndPermissionSeeder::class,
        ]);

        $account = Account::create(['name' => 'Acme Corporation']);

        User::factory()->create([
            'account_id' => $account->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'password' => 'secret',
            'owner' => true,
            'image' => null,
        ]);

        User::factory(5)->create(['account_id' => $account->id, 'image' => null]);

        $organizations = Organization::factory(100)
            ->create(['account_id' => $account->id]);

        Contact::factory(100)
            ->create(['account_id' => $account->id])
            ->each(function ($contact) use ($organizations) {
                $contact->update(['organization_id' => $organizations->random()->id]);
            });
    }
}
