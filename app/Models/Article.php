<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'slug',
        'title',
        'description',
        'body',
    ];

    public function favoredBy(User $user): bool 
    {
        return $this->favoredUsers()
            ->whereKey($user->getKey())
            ->exists();
    }

    public function scopeList(Builder $query, int $take, int $skip)
    {
        return $query->latest()
            ->limit($take)
            ->offset($skip);
    }

    public function scopeHavingTag(Builder $query, string $tag)
    {
        return $query->whereHas('tags', fn (Builder $builder) => 
            $builder->where('name', $tag)
        );
    }

    public function scopeOfAuthor(Builder $query, string $username) {
        return $query->whereHas('author', fn (Builder $builder) =>
            $builder->where('username', $username)
    );
    }

    public function scopeFavoredByUser(Builder $query, string $username) {
        return $query->whereHas('favoredUsers', fn(Builder $builder) => 
            $builder->where('username', $username)
    );
    }

    public function scopeFollowedAuthorsOf(Builder $query, User $user) 
    {
        return $query->whereHas('author', fn(Builder $builder) => 
            $builder->whereIn('id', $user->authors->pluck('id'))
    );

    }

    public function attachTags(array $tags): void
    {
        foreach ($tags as $tagName) {
            $tag = Tag::firstOrCreate([
                'name' => $tagName,
            ]);

            $this->tags()->syncWithoutDetaching($tag);
        }
    }

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }


    public function favoredUsers()
    {
        return $this->belongsToMany(User::class, 'article_favorite');
    }
}
