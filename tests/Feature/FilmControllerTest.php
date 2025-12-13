<?php

namespace Tests\Feature;

use App\Jobs\CreateFilmJob;
use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use App\Services\MovieService\MovieService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FilmControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест на получение структуру данных фильма.
     *
     * @return array
     */
    private function getTypicalFilmStructure(): array
    {
        return [
            'id',
            'name',
            'poster_image',
            'preview_image',
            'background_image',
            'background_color',
            'video_link',
            'preview_video_link',
            'description',
            'director',
            'released',
            'run_time',
            'rating',
            'scores_count',
            'imdb_id',
            'status',
            'starring',
            'genre',
        ];
    }

    /**
     * Тест метода index для получения всех фильмов.
     */
    public function testIndexAllFilms(): void
    {
        Film::factory()->count(10)->create(['status' => Film::STATUS_READY]);

        $response = $this->getJson('/api/films');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(8, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'preview_image',
                ],
            ],
            'current_page',
            'first_page_url',
            'next_page_url',
            'prev_page_url',
            'per_page',
            'total',
        ]);
    }

    /**
     * Тест метода index для фильтрации фильмов по жанру.
     */
    public function testIndexFilteredByGenre(): void
    {
        $genre = Genre::factory()->create();

        Film::factory()
            ->count(5)
            ->hasAttached($genre)
            ->create(['status' => Film::STATUS_READY]);

        $response = $this->getJson('/api/films?genre=' . $genre->name);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(5, 'data');
    }

    /**
     * Тест метода index для фильтрации фильмов по статусу для пользователя.
     */
    public function testIndexFilteredByStatusForUser(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        Film::factory()->count(3)->create(['status' => Film::STATUS_PENDING]);
        Film::factory()->count(3)->create(['status' => Film::STATUS_MODERATE]);

        $response = $this->actingAs($user)->getJson('/api/films?status=' . Film::STATUS_PENDING);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson([
            'message' => 'Недостаточно прав для просмотра фильмов в статусе ' . Film::STATUS_PENDING,
        ]);

        $response = $this->actingAs($user)->getJson('/api/films?status=' . Film::STATUS_MODERATE);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson([
            'message' => 'Недостаточно прав для просмотра фильмов в статусе ' . Film::STATUS_MODERATE,
        ]);
    }

    /**
     * Тест метода index для фильтрации фильмов по статусу для модератора.
     */
    public function testIndexFilteredByStatusForModerator(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_MODERATOR]);

        Film::factory()->count(3)->create(['status' => Film::STATUS_PENDING]);
        Film::factory()->count(3)->create(['status' => Film::STATUS_MODERATE]);

        $response = $this->actingAs($user)->getJson('/api/films?status=' . Film::STATUS_PENDING);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(3, 'data');

        $response = $this->actingAs($user)->getJson('/api/films?status=' . Film::STATUS_MODERATE);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(3, 'data');
    }

    /**
     * Тест метода index для получения фильмов, отсортированных по дате релиза.
     */
    public function testIndexFilmsOrderedByReleased(): void
    {
        Film::factory()->count(5)->create(['status' => Film::STATUS_READY]);

        $response = $this->getJson('/api/films?order_by=' . Film::ORDER_BY_RELEASED . '&order_to=desc');

        $response->assertStatus(Response::HTTP_OK);
        $responseData = json_decode($response->getContent(), true)['data'];

        for ($i = 1; $i < count($responseData); $i++) {
            $this->assertTrue($responseData[$i - 1][Film::ORDER_BY_RELEASED] >= $responseData[$i][Film::ORDER_BY_RELEASED]);
        }
    }

    /**
     * Тест метода index для получения фильмов, отсортированных по рейтингу.
     */
    public function testIndexFilmsOrderedByRating(): void
    {
        Film::factory()->count(5)->create(['status' => Film::STATUS_READY]);

        $response = $this->getJson('/api/films?order_by=' . Film::ORDER_BY_RATING . '&order_to=desc');

        $response->assertStatus(Response::HTTP_OK);
        $responseData = json_decode($response->getContent(), true)['data'];

        for ($i = 1; $i < count($responseData); $i++) {
            $this->assertTrue($responseData[$i - 1][Film::ORDER_BY_RATING] >= $responseData[$i][Film::ORDER_BY_RATING]);
        }
    }

    /**
     * Тест метода store без авторизации.
     */
    public function testStoreUnauthorized(): void
    {
        $data = [
            'imdb_id' => 'tt0944947',
        ];

        $response = $this->postJson("/api/films", $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson([
            'message' => 'Запрос требует аутентификации',
        ]);
    }

    /**
     * Тест метода store с недостаточными правами.
     */
    public function testStoreAuthorized(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $data = [
            'imdb_id' => 'tt0944947',
        ];

        $response = $this->actingAs($user)->postJson("/api/films", $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson([
            'message' => 'Недостаточно прав',
        ]);
    }

    /**
     * Тест метода store для модератора.
     */
    public function testStoreModerator(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_MODERATOR]);

        $imdbId = 'tt0944947';

        $data = [
            'imdb_id' => $imdbId,
        ];

        $newMovie = Film::factory()->make([
            'imdb_id' => $imdbId,
        ])->toArray();

        Queue::fake();

        $movieService = Mockery::mock(MovieService::class);
        $movieService->shouldReceive('getMovie')
            ->with($imdbId)
            ->andReturn($newMovie);
        $this->app->instance(MovieService::class, $movieService);

        $response = $this->actingAs($user)->postJson("/api/films", $data);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'imdb_id' => $imdbId,
                'status' => Film::STATUS_PENDING,
            ],
        ]);
    }

    /**
     * Тест метода store для проверки добавления задачи в очередь.
     */
    public function testStoreQueue(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_MODERATOR]);

        $imdbId = 'tt0944947';

        $data = [
            'imdb_id' => $imdbId,
        ];

        Queue::fake();

        $response = $this->actingAs($user)->postJson("/api/films", $data);

        $response->assertStatus(Response::HTTP_CREATED);
        Queue::assertPushed(function (CreateFilmJob $job) use ($imdbId) {
            return $job->imdbId === $imdbId;
        });
    }

    /**
     * Тест на создание фильма, который уже существует.
     */
    public function testStoreFilmAlreadyExists(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_MODERATOR]);

        $imdbId = 'tt0944947';

        $film = Film::factory()->create([
            'imdb_id' => $imdbId,
        ]);

        $data = [
            'imdb_id' => $imdbId,
        ];

        $response = $this->actingAs($user)->postJson("/api/films", $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'message' => 'Переданные данные не корректны',
        ]);
        $response->assertJsonValidationErrors([
            'imdb_id',
        ]);
    }

    /**
     * Тест на создание фильма с некорректными данными.
     */
    public function testStoreValidationError(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_MODERATOR]);

        $data = [
            'imdb_id' => 'Невалидный imdb_id',
        ];

        $response = $this->actingAs($user)->postJson("/api/films", $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'message' => 'Переданные данные не корректны',
        ]);
        $response->assertJsonValidationErrors([
            'imdb_id',
        ]);
    }

    /**
     * Тест на получение информации о фильме.
     */
    public function testShowFilm(): void
    {
        $film = Film::factory()->create(['status' => Film::STATUS_READY]);

        $response = $this->get("/api/films/{$film->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => $this->getTypicalFilmStructure(),
        ]);
    }

    /**
     * Тест на получение информации о фильме для авторизованного пользователя с отмеченным избранным статусом.
     */
    public function testShowFavoriteForAuthorized(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $film = Film::factory()->create(['status' => Film::STATUS_READY]);

        $user->favoriteFilms()->attach($film);

        $response = $this->actingAs($user)->get("/api/films/{$film->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => array_merge($this->getTypicalFilmStructure(), ['is_favorite']),
        ]);
        $response->assertJsonPath('data.is_favorite', true);
    }

    /**
     * Тест на получение информации о фильме для гостя.
     */
    public function testShowFavoriteForGuest(): void
    {
        $film = Film::factory()->create(['status' => Film::STATUS_READY]);

        $response = $this->get("/api/films/{$film->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => $this->getTypicalFilmStructure(),
        ]);
        $response->assertJsonMissing(['data' => ['is_favorite']]);
    }

    /**
     * Тест на обновление фильма без авторизации.
     */
    public function testUpdateUnauthorized(): void
    {
        $film = Film::factory()->create(['status' => Film::STATUS_PENDING]);

        $newName = 'Новое название фильма';
        $newImdbId = 'tt0944947';
        $newStatus = Film::STATUS_READY;

        $response = $this->patchJson("/api/films/{$film->id}", [
            'name' => $newName,
            'imdb_id' => $newImdbId,
            'status' => $newStatus,
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson([
            'message' => 'Запрос требует аутентификации',
        ]);
    }

    /**
     * Тест на обновление фильма с авторизованным пользователем без необходимых прав.
     */
    public function testUpdateAuthorized(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $film = Film::factory()->create(['status' => Film::STATUS_PENDING]);

        $newName = 'Новое название фильма';
        $newImdbId = 'tt0944947';
        $newStatus = Film::STATUS_READY;

        $response = $this->actingAs($user)
            ->patchJson("/api/films/{$film->id}", [
                'name' => $newName,
                'imdb_id' => $newImdbId,
                'status' => $newStatus,
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson([
            'message' => 'Недостаточно прав',
        ]);
    }

    /**
     * Тест на обновление фильма с модератором, который имеет необходимые права.
     */
    public function testUpdateModerator(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_MODERATOR]);
        $film = Film::factory()->create(['status' => Film::STATUS_PENDING]);

        $newName = 'Новое название фильма';
        $newImdbId = 'tt1234567';
        $newStatus = Film::STATUS_READY;

        $response = $this->actingAs($user)
            ->patchJson("/api/films/{$film->id}", [
                'name' => $newName,
                'imdb_id' => $newImdbId,
                'status' => $newStatus,
            ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $film->id,
                'name' => $newName,
                'imdb_id' => $newImdbId,
                'status' => $newStatus,
            ],
        ]);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'name' => $newName,
            'imdb_id' => $newImdbId,
            'status' => $newStatus,
        ]);
    }

    /**
     * Тест на обновление фильма с некорректными данными.
     */
    public function testUpdateValidationError(): void
    {
        $film = Film::factory()->create();
        $user = User::factory()->create(['role' => User::ROLE_MODERATOR]);

        $invalidData = [
            'name' => '',
            'poster_image' => str_repeat('a', 256),
            'preview_image' => str_repeat('a', 256),
            'background_image' => str_repeat('a', 256),
            'background_color' => str_repeat('a', 10),
            'video_link' => str_repeat('a', 256),
            'preview_video_link' => str_repeat('a', 256),
            'description' => str_repeat('a', 1001),
            'director' => str_repeat('a', 256),
            'starring' => 'Не массив',
            'genre' => 'Не массив',
            'run_time' => 'Не число',
            'released' => 'Не число',
            'imdb_id' => 'Невалидный imdb_id',
            'status' => 'Несуществующий статус',
        ];

        $response = $this->actingAs($user)
            ->patchJson("/api/films/{$film->id}", $invalidData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors([
            'name',
            'poster_image',
            'preview_image',
            'background_image',
            'background_color',
            'video_link',
            'preview_video_link',
            'description',
            'director',
            'starring',
            'genre',
            'run_time',
            'released',
            'imdb_id',
            'status',
        ]);
    }
}
