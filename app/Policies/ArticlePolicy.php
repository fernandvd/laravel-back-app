<?php

namespace App\Policies;

use App\Models\{User, Article};
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Article $article): bool 
    {
        return $user->getKey() === $article->author->getKey();
    }

    /**
     * Determine wheter the user can delete the model.
     */
    public function delete(User $user, Article $article): bool 
    {
        return $this->update($user, $article);
    }
}
