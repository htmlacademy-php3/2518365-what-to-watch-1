<?php

namespace App\Policies;

use App\Models\Film;
use App\Models\User;

class FilmPolicy
{
    /**
     * Determine whether the user can view films with specific status.
     */
    public function viewWithStatus(?User $user, string $status): bool
    {
        if ($user && $user->isModerator()) {
            return true;
        }

        return $status === Film::STATUS_READY;
    }

    /**
     * Determine whether the user can create films.
     */
    public function create(User $user): bool
    {
        return $user->isModerator();
    }

    /**
     * Determine whether the user can update the film.
     */
    public function update(User $user, Film $film): bool
    {
        return $user->isModerator();
    }

    /**
     * Determine whether the user can delete the film.
     */
    public function delete(User $user, Film $film): bool
    {
        return $user->isModerator();
    }
}
