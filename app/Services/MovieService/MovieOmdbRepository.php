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
            /** @var array|null $cachedData */
            $cachedData = Cache::get($cacheKey);
            return $cachedData;
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

        /** @psalm-suppress UndefinedInterfaceMethod */
        $movieData = json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        if (($movieData['Response'] ?? 'False') === 'False') {
            return null;
        }

        $actors = $this->parseCommaSeparatedList($movieData['Actors'] ?? '');
        $genres = $this->parseCommaSeparatedList($movieData['Genre'] ?? '');

        $filmData = new FilmData(
            $movieData['Title'] ?? '', // string - обязательно
            $movieData['Plot'] ?? '', // string - обязательно
            $movieData['Director'] ?? '', // string - обязательно
            $actors, // array - OK
            $genres, // array - OK
            $this->parseRuntime($movieData['Runtime'] ?? '0'), // int - OK
            (int)($movieData['Year'] ?? 0), // int - OK
            $movieData['imdbID'] ?? '' // string - обязательно
        );

        $filmData->poster_image = $movieData['Poster'] ?? null;
        $filmData->rating = (float)($movieData['imdbRating'] ?? 0);
        $filmData->scores_count = (int)str_replace(',', '', $movieData['imdbVotes'] ?? '0');

        $data = $filmData->toArray();
        $cacheTimeCarbon = Carbon::now()->addSeconds($this->cacheTime);
        Cache::put($cacheKey, $data, $cacheTimeCarbon);

        return $data;
    }

    /**
     * Парсит строку с разделителями-запятыми в массив.
     *
     * @param string $list
     * @return array<int, string>
     */
    private function parseCommaSeparatedList(string $list): array
    {
        if (empty($list)) {
            return [];
        }

        $items = array_map('trim', explode(',', $list));
        return array_filter($items, static fn ($item) => $item !== '');
    }

    /**
     * Парсит строку времени выполнения в минуты.
     *
     * @param string $runtime
     * @return int
     */
    private function parseRuntime(string $runtime): int
    {
        if (preg_match('/(\d+)/', $runtime, $matches)) {
            return (int)$matches[1];
        }

        return 0;
    }
}
