<?php

namespace App\Policies;

use App\Models\Film;
use App\Models\User;

/**
 * @psalm-suppress PossiblyUnusedMethod
 */
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
}
