<?php

namespace App\Services\MovieService;

use GuzzleHttp\Client;
use App\DTO\FilmData;


class MovieOmdbRepository implements MovieRepositoryInterface
{

    private Client $client;
    private string $apiKey;
    private string $baseUrl;

  public function __construct(Client $client)
  {
      $this->client = $client;
      $this->apiKey = config('services.omdb.api_key');
      $this->baseUrl = config('services.omdb.base_url');
  }

  /**
   * Поиск фильма по его IMDB ID
   *
   * @param string $imdbId IMDB ID фильма
   * @return array|null Возвращает массив с информацией о фильме или null, если фильм не найден
   */
  public function findMovieById(string $imdbId): ?array
  {
      $response = $this->client->request('GET', $this->baseUrl, [
          'query' => [
              'apikey' => $this->apiKey,
              'i' => $imdbId,
          ],
      ]);

      $movieData = json_decode($response->getBody()->getContents(), true);

      $filmData = new FilmData(
          $movieData['Title'],
          $movieData['Plot'],
          $movieData['Director'],
          (int) $movieData['Year'],
          (int) $movieData['Runtime'],
          $movieData['imdbID'],
          array_map('trim', explode(',', $movieData['Actors'])),
          array_map('trim', explode(',', $movieData['Genre']))
      );

      $filmData->poster_image = $movieData['Poster'];
      $filmData->rating = (float) $movieData['imdbRating'];
      $filmData->scores_count = (int) str_replace(',', '', $movieData['imdbVotes']);

      return $filmData->toArray();
  }
}
