<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Contracts\JwtSubjectInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements JwtSubjectInterface
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;


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

        'owner',
        'first_name',
        'last_name',
        'account_id',
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
        'owner' => 'boolean',
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

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

    public function getNameAttribute()
    {
        return $this->first_name. ' '. $this->last_name;
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::needsRehash($password) ? Hash::make($password) : $password;
    }

    public function scopeOrderByName($query)
    {
        $query->orderBy('last_name')->orderBy('first_name');
    }

    public function scopeWhereRole($query, $role) {
        switch($role) {
            case 'user' : return $query->where('owner', false);
            case 'owner': return $query->where('owner', true);
            default: return $query;
        }
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('username', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        })->when($filters['email_verified_at'] ?? null, function ($query, $email_verified_at) {
            if ($email_verified_at=='yes') {
                $query->where(['email_verified_at' => true]);
            } else {
                $query->where(['email_verified_at' => false]);
            }
        })->when($filters['role'] ?? null, function ($query, $role) {
            $query->whereRole($role);
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
