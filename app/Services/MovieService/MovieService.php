<?php

namespace WhatToWatch\Services\MovieService;

final readonly class MovieService
{
  public function __construct(private MovieRepositoryInterface $repository)
  {
  }

  /**
   * Получение информации о фильме по его IMDB ID через репозиторий
   *
   * @param string $imdbId IMDB ID фильма
   * @return array|null Возвращает массив с информацией о фильме или null, если фильм не найден
   */
  public function getMovie(string $imdbId): ?array
  {
    return $this->repository->findMovieById($imdbId);
  }
}
