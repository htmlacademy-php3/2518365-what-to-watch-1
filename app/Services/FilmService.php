<?php

namespace App\Services;

use App\Models\Film;

final readonly class FilmService
{
    /**
     * Конструктор класса FilmService
     *
     * @param ActorService $actorService Сервис для работы с актерами
     * @param GenreService $genreService Сервис для работы с жанрами
     */
    public function __construct(private ActorService $actorService, private GenreService $genreService)
    {
    }

    /**
     * Создает фильм на основе данных
     *
     * @param array $data Данные фильма
     * @param string $nextStatus Следующий статус фильма
     * @return Film Созданный фильм
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function createFromData(array $data, string $nextStatus): Film
    {
        $film = Film::firstOrCreate(
            ['imdb_id' => $data['imdb_id']],
            ['status' => $nextStatus]
        );
        $this->saveFilm($film, $data, $nextStatus);

        /** @var Film $film */
        return $film;
    }

    /**
     * Обновляет фильм на основе данных или создает, если его не существует.
     *
     * @param array $data Данные фильма
     * @param string $nextStatus Следующий статус фильма
     * @return Film|null Обновленный фильм или null, если фильм не найден
     * @psalm-suppress PossiblyUnusedMethod
     * @psalm-suppress PossiblyUnusedReturnValue
     */
    public function updateFromData(array $data, string $nextStatus): ?Film
    {
        if (!isset($data['imdb_id'])) {
            return null;
        }

        $film = Film::updateOrCreate(
            ['imdb_id' => $data['imdb_id']],
            $data
        );

        if (!$film) {
            return null;
        }

        $this->saveFilm($film, $data, $nextStatus);

        /** @var Film|null $film */
        return $film;
    }

    /**
     * Сохраняет данные фильма и связанные данные (актеры, жанры)
     *
     * @param Film $film Объект фильма
     * @param array $data Данные фильма
     * @param string $nextStatus Следующий статус фильма
     * @return void
     * @psalm-suppress PossiblyUnusedMethod
     */
    private function saveFilm(Film $film, array $data, string $nextStatus): void
    {
        $film->fill($data);
        $film->status = $nextStatus;
        $film->save();

        if (isset($data['starring'])) {
            /** @psalm-suppress UndefinedMethod */
            $this->actorService->syncActors($film, $data['starring']);
        }

        if (isset($data['genre'])) {
            /** @psalm-suppress UndefinedMethod */
            $this->genreService->syncGenres($film, $data['genre']);
        }
    }

    /**
     * Удаляет фильм по его IMDB ID
     *
     * @param string $imdbId IMDB ID фильма
     * @return void
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function deleteFilm(string $imdbId): void
    {
        $film = Film::firstWhere('imdb_id', $imdbId);
        if ($film) {
            $film->delete();
        }
    }
}
