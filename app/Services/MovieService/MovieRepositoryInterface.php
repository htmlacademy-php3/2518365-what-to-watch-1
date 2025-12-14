<?php

namespace App\Services\MovieService;

/**
 * @psalm-suppress PossiblyUnusedMethod
 */
interface MovieRepositoryInterface
{
    /**
     * Поиск фильма по его IMDB ID
     *
     * @param string $imdbId IMDB ID фильма
     * @return array|null Возвращает массив с информацией о фильме или null, если фильм не найден
     */
    public function findMovieById(string $imdbId): ?array;
}
