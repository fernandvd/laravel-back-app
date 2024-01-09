<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Contracts\JwtSubjectInterface;


class User extends Authenticatable implements JwtSubjectInterface
{
    use HasApiTokens, HasFactory, Notifiable;


    /**
     * Regular expression for username.
     */
    public const REGEX_USERNAME = '/^[\pL\pM\pN._-]+$/u';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bio',
        'image',
        'username',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getJwtIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function following(User $author): bool
    {
        return $this->authors()
            ->whereKey($author->getKey())
            ->exists();
    }

    public function followedBy(User $follower): bool
    {
        return $this->followers()
            ->whereKey($follower->getKey())
            ->exists();
    }

    public function authors()
    {
        return $this->belongsToMany(User::class, 'author_follower', 'author_id', 'follower_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'author_follower', 'follower_id', 'author_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'author_id');
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function favorites()
    {
        return $this->belongsToMany(Article::class, 'article_favorite', );
    }
}
