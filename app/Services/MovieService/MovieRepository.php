<?php

namespace WhatToWatch\Services\MovieService;

use Psr\Http\Client\ClientExceptionInterface;

final readonly class MovieRepository implements MovieRepositoryInterface
{

  public function __construct(private \Psr\Http\Client\ClientInterface $httpClient)
  {
  }

  /**
   * Поиск фильма по его IMDB ID
   *
   * @param string $imdbId IMDB ID фильма
   * @return array|null Возвращает массив с информацией о фильме или null, если фильм не найден
   * @throws ClientExceptionInterface
   */
  public function findMovieById(string $imdbId): ?array
  {
    $response = $this->httpClient->sendRequest($imdbId);

    return json_decode($response->getBody()->getContents(), true);
  }
}
