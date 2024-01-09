<?php

namespace App\Policies;

use App\Models\{User, Comment};
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): bool 
    {
        return $user->getKey() === $comment->author->getKey();
    }
}
