<?php

namespace App\Services;

use App\Models\Actor;
use App\Models\Film;

class ActorService
{
    /**
     * Синхронизация актеров
     *
     * @param Film $film Фильм
     * @param array $actorsNames Имена актеров
     * @return void
     */
    public function syncActors(Film $film, array $actorsNames): void
    {
        $film->actors()->detach();
        foreach ($actorsNames as $actorName) {
            $actor = Actor::firstOrCreate(['name' => $actorName]);
            $film->actors()->attach($actor);
        }
    }

}
