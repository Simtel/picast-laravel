<?php

namespace App\Policies;

use App\Models\User;
use App\Models\YouTubeVideo;

class YouTubeVideoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, YouTubeVideo $youTubeVideo): bool
    {
        return $user->id === $youTubeVideo->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, YouTubeVideo $youTubeVideo): bool
    {
        return $user->id === $youTubeVideo->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, YouTubeVideo $youTubeVideo): bool
    {
        return $user->id === $youTubeVideo->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, YouTubeVideo $youTubeVideo): bool
    {
        return $user->id === $youTubeVideo->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, YouTubeVideo $youTubeVideo): bool
    {
        return $user->id === $youTubeVideo->user_id;
    }
}
