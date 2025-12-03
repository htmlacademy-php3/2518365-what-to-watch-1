<?php

namespace App\Services;

use App\Models\Actor;
use App\Models\Film;
use App\Models\Genre;

class SyncService
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

    /**
     * Синхронизация жанров
     *
     * @param Film $film Фильм
     * @param array $genresNames Названия жанров
     * @return void
     */
    public function syncGenres(Film $film, array $genresNames): void
    {
        $film->genres()->detach();
        foreach ($genresNames as $genreName) {
            $genre = Genre::firstOrCreate(['name' => $genreName]);
            $film->genres()->attach($genre);
        }
    }
}
