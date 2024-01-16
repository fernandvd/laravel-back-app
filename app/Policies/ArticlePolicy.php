<?php

namespace App\Policies;

use App\Models\{User, Article};
use App\Enums\{RolEnum, PermissionEnum};
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability): bool|null 
    {
        if ($user->hasRole([RolEnum::ADMIN])) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Article $article): bool 
    {
        if ($user->getPermissionsViaRoles()->contains('name', PermissionEnum::UPDATE_ARTICLE->value)) {
            return true;
        }
        return $user->getKey() === $article->author->getKey();
    }

    /**
     * Determine wheter the user can delete the model.
     */
    public function delete(User $user, Article $article): bool 
    {
        if ($user->getPermissionsViaRoles()->contains('name', PermissionEnum::DELETE_ARTICLE->value)) {
            return true;
        }
        return $user->getKey() === $article->author->getKey();
    }
}
