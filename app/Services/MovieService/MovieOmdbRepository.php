<?php

namespace App\Services\MovieService;

use App\DTO\FilmData;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use JsonException;

class MovieOmdbRepository implements MovieRepositoryInterface
{
    private Client $client;
    private string $apiKey;
    private string $baseUrl;
    private int $cacheTime;

    public function __construct(Client $client, array $config)
    {
        $this->client = $client;
        $this->apiKey = $config['api_key'];
        $this->baseUrl = $config['base_url'];
        $this->cacheTime = $config['cache_time'];
    }

    /**
     * Поиск фильма по его IMDB ID
     *
     * @param string $imdbId IMDB ID фильма
     * @return array|null Возвращает массив с информацией о фильме или null, если фильм не найден
     * @throws GuzzleException При ошибках сетевого запроса
     * @throws JsonException При ошибках парсинга JSON
     */
    public function findMovieById(string $imdbId): ?array
    {
        $cacheKey = 'movie_' . $imdbId;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = $this->client->request('GET', $this->baseUrl, [
            'query' => [
                'apikey' => $this->apiKey,
                'i' => $imdbId,
            ],
        ]);

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            return null;
        }

        $movieData = $response->json();

        if (($movieData['Response'] ?? 'False') === 'False') {
            return null;
        }

        $filmData = new FilmData(
            $movieData['Title'] ?? null,
            $movieData['Plot'] ?? null,
            $movieData['Director'] ?? null,
            (int)($movieData['Year'] ?? 0),
            (int)($movieData['Runtime'] ?? 0),
            $movieData['imdbID'] ?? null,
            array_map('trim', explode(',', $movieData['Actors'] ?? '')),
            array_map('trim', explode(',', $movieData['Genre'] ?? ''))
        );

        $filmData->poster_image = $movieData['Poster'] ?? null;
        $filmData->rating = (float)($movieData['imdbRating'] ?? 0);
        $filmData->scores_count = (int)str_replace(',', '', $movieData['imdbVotes'] ?? '0');

        $data = $filmData->toArray();
        $cacheTimeCarbon = Carbon::now()->addSeconds($this->cacheTime);
        Cache::put($cacheKey, $data, $cacheTimeCarbon);

        return $data;
    }
}
