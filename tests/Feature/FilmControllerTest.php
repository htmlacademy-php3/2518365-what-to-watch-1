<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FilmControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Получить ожидаемую структуру для списка фильмов.
     *
     * @return array
     */
    private function getFilmIndexStructure(): array
    {
        return [
            'id',
            'name',
            'preview_image',
            'released',
            'rating',
            'starring',
            'genre',
            'is_favorite',
        ];
    }

    /**
     * Получить ожидаемую структуру для одного фильма.
     *
     * @return array
     */
    private function getFilmShowStructure(): array
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
     * Тест получения списка фильмов.
     *
     * @return void
     */
    public function testGetShowsList(): void
    {
        Film::factory()->count(10)->create(['status' => Film::STATUS_READY]);
        Film::factory()->count(5)->create(['status' => Film::STATUS_PENDING]);

        $response = $this->getJson('/api/films');

        $response->assertStatus(Response::HTTP_OK);

        $data = $response->json();
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('current_page', $data['data']);
        $this->assertArrayHasKey('data', $data['data']);

        $films = $data['data']['data'];
        $this->assertNotEmpty($films);

        $firstFilm = $films[0];
        foreach ($this->getFilmIndexStructure() as $field) {
            $this->assertArrayHasKey($field, $firstFilm);
        }

        $this->assertEquals(8, $data['data']['per_page']);
        $this->assertEquals(10, $data['data']['total']);
    }

    /**
     * Тест фильтрации фильмов по жанру.
     *
     * @return void
     */
    public function testCanFilterFilmsByGenre(): void
    {
        $drama = Genre::factory()->create(['name' => 'драма']);
        $comedy = Genre::factory()->create(['name' => 'комедия']);

        $filmsWithDrama = Film::factory()->count(3)->create(['status' => Film::STATUS_READY]);
        foreach ($filmsWithDrama as $film) {
            $film->genres()->attach($drama);
        }

        $filmsWithComedy = Film::factory()->count(2)->create(['status' => Film::STATUS_READY]);
        foreach ($filmsWithComedy as $film) {
            $film->genres()->attach($comedy);
        }

        $response = $this->getJson('/api/films?genre=драма');

        $response->assertStatus(Response::HTTP_OK);

        $data = $response->json();
        $films = $data['data']['data'];
        $this->assertCount(3, $films);
    }

    /**
     * Тест что обычный пользователь не видит фильмы со статусом pending.
     *
     * @return void
     */
    public function testUserCannotSeePendingFilms(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        Film::factory()->count(3)->create(['status' => Film::STATUS_PENDING]);
        Film::factory()->count(2)->create(['status' => Film::STATUS_READY]);

        $response = $this->actingAs($user)->getJson('/api/films?status=' . Film::STATUS_PENDING);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $responseData = $response->json();
        $this->assertArrayHasKey('message', $responseData);
        $this->assertStringContainsString('Недостаточно прав', $responseData['message']);
    }

    /**
     * Тест что модератор видит фильмы со статусом pending.
     *
     * @return void
     */
    public function testModeratorCanSeePendingFilms(): void
    {
        $moderator = User::factory()->create(['role' => User::ROLE_MODERATOR]);

        Film::factory()->count(3)->create(['status' => Film::STATUS_PENDING]);
        Film::factory()->count(2)->create(['status' => Film::STATUS_READY]);

        $response = $this->actingAs($moderator)->getJson('/api/films?status=' . Film::STATUS_PENDING);

        $response->assertStatus(Response::HTTP_OK);

        $data = $response->json();
        $films = $data['data']['data'];
        $this->assertCount(3, $films);
    }

    /**
     * Тест сортировки фильмов.
     *
     * @return void
     */
    public function testFilmsCanBeSorted(): void
    {
        Film::factory()->create([
            'status' => Film::STATUS_READY,
            'released' => 2020,
            'rating' => 4.5,
            'name' => 'Фильм A'
        ]);

        Film::factory()->create([
            'status' => Film::STATUS_READY,
            'released' => 2022,
            'rating' => 4.2,
            'name' => 'Фильм B'
        ]);

        $response = $this->getJson('/api/films?order_by=released&order_to=desc');

        $response->assertStatus(Response::HTTP_OK);

        $data = $response->json();
        $films = $data['data']['data'];

        $this->assertEquals(2022, $films[0]['released']);
        $this->assertEquals(2020, $films[1]['released']);
    }

    /**
     * Тест что гость не может создать фильм.
     *
     * @return void
     */
    public function testGuestCannotCreateFilm(): void
    {
        $data = [
            'imdb_id' => 'tt' . rand(1000000, 9999999),
        ];

        $response = $this->postJson('/api/films', $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Тест что обычный пользователь не может создать фильм.
     *
     * @return void
     */
    public function testUserCannotCreateFilm(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $data = [
            'imdb_id' => 'tt' . rand(1000000, 9999999),
        ];

        $response = $this->actingAs($user)->postJson('/api/films', $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Тест что модератор может создать фильм.
     *
     * @return void
     */
    public function testModeratorCanCreateFilm(): void
    {
        $moderator = User::factory()->create(['role' => User::ROLE_MODERATOR]);

        $imdbId = 'tt' . rand(1000000, 9999999);
        $data = [
            'imdb_id' => $imdbId,
        ];

        $response = $this->actingAs($moderator)->postJson('/api/films', $data);

        $response->assertStatus(Response::HTTP_CREATED);

        $responseData = $response->json();
        $this->assertArrayHasKey('data', $responseData);

        $filmData = $responseData['data'];
        $this->assertEquals($imdbId, $filmData['imdb_id']);
        $this->assertEquals(Film::STATUS_PENDING, $filmData['status']);

        $this->assertDatabaseHas('films', [
            'imdb_id' => $imdbId,
            'status' => Film::STATUS_PENDING,
        ]);
    }

    /**
     * Тест валидации при создании фильма.
     *
     * @return void
     */
    public function testValidationOnFilmCreation(): void
    {
        $moderator = User::factory()->create(['role' => User::ROLE_MODERATOR]);

        $response = $this->actingAs($moderator)->postJson('/api/films', []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['imdb_id']);

        $response = $this->actingAs($moderator)->postJson('/api/films', [
            'imdb_id' => 'invalid_id',
        ]);
        $response->assertJsonValidationErrors(['imdb_id']);

        $existingFilm = Film::factory()->create(['imdb_id' => 'tt1234567']);

        $response = $this->actingAs($moderator)->postJson('/api/films', [
            'imdb_id' => 'tt1234567',
        ]);
        $response->assertJsonValidationErrors(['imdb_id']);
    }

    /**
     * Тест получения информации о фильме.
     *
     * @return void
     */
    public function testCanGetSingleFilm(): void
    {
        $film = Film::factory()->create(['status' => Film::STATUS_READY]);

        $response = $this->getJson("/api/films/{$film->id}");

        $response->assertStatus(Response::HTTP_OK);

        $responseData = $response->json();
        $this->assertArrayHasKey('data', $responseData);

        $filmData = $responseData['data'];

        foreach ($this->getFilmShowStructure() as $field) {
            $this->assertArrayHasKey($field, $filmData);
        }

        $this->assertEquals($film->id, $filmData['id']);
        $this->assertEquals($film->name, $filmData['name']);
        $this->assertEquals($film->description, $filmData['description']);
        $this->assertEquals($film->imdb_id, $filmData['imdb_id']);
        $this->assertEquals($film->status, $filmData['status']);
    }

    /**
     * Тест что гость не может обновить фильм.
     *
     * @return void
     */
    public function testGuestCannotUpdateFilm(): void
    {
        $film = Film::factory()->create();

        $response = $this->patchJson("/api/films/{$film->id}", [
            'name' => 'Новое название',
            'imdb_id' => $film->imdb_id,
            'status' => Film::STATUS_READY,
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Тест что обычный пользователь не может обновить фильм.
     *
     * @return void
     */
    public function testUserCannotUpdateFilm(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $film = Film::factory()->create();

        $response = $this->actingAs($user)->patchJson("/api/films/{$film->id}", [
            'name' => 'Новое название',
            'imdb_id' => $film->imdb_id,
            'status' => Film::STATUS_READY,
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Тест что модератор может обновить фильм.
     *
     * @return void
     */
    public function testModeratorCanUpdateFilm(): void
    {
        $moderator = User::factory()->create(['role' => User::ROLE_MODERATOR]);
        $film = Film::factory()->create();

        $newData = [
            'name' => 'Обновленное название фильма',
            'description' => 'Обновленное описание фильма',
            'director' => 'Новый режиссер',
            'released' => 2024,
            'run_time' => 150,
            'imdb_id' => $film->imdb_id,
            'status' => Film::STATUS_READY,
        ];

        $response = $this->actingAs($moderator)->patchJson("/api/films/{$film->id}", $newData);

        $response->assertStatus(Response::HTTP_OK);

        $responseData = $response->json();
        $this->assertArrayHasKey('data', $responseData);

        $updatedFilm = $responseData['data'];
        $this->assertEquals('Обновленное название фильма', $updatedFilm['name']);
        $this->assertEquals('Обновленное описание фильма', $updatedFilm['description']);
        $this->assertEquals(2024, $updatedFilm['released']);
        $this->assertEquals(Film::STATUS_READY, $updatedFilm['status']);

        $this->assertDatabaseHas('films', [
            'id' => $film->id,
            'name' => 'Обновленное название фильма',
            'status' => Film::STATUS_READY,
        ]);
    }

    /**
     * Тест обновления фильма с актерами и жанрами.
     *
     * @return void
     */
    public function testCanUpdateFilmWithActorsAndGenres(): void
    {
        $moderator = User::factory()->create(['role' => User::ROLE_MODERATOR]);
        $film = Film::factory()->create();

        $updateData = [
            'name' => 'Фильм с актерами и жанрами',
            'starring' => ['Актер 1', 'Актер 2', 'Актер 3'],
            'genre' => ['драма', 'триллер', 'боевик'],
            'imdb_id' => $film->imdb_id,
            'status' => Film::STATUS_READY,
        ];

        $response = $this->actingAs($moderator)->patchJson("/api/films/{$film->id}", $updateData);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('actors', ['name' => 'Актер 1']);
        $this->assertDatabaseHas('actors', ['name' => 'Актер 2']);
        $this->assertDatabaseHas('actors', ['name' => 'Актер 3']);

        $this->assertDatabaseHas('genres', ['name' => 'драма']);
        $this->assertDatabaseHas('genres', ['name' => 'триллер']);
        $this->assertDatabaseHas('genres', ['name' => 'боевик']);

        $film->refresh();
        $this->assertCount(3, $film->actors);
        $this->assertCount(3, $film->genres);
    }

    /**
     * Тест валидации при обновлении фильма.
     *
     * @return void
     */
    public function testValidationOnFilmUpdate(): void
    {
        $moderator = User::factory()->create(['role' => User::ROLE_MODERATOR]);
        $film = Film::factory()->create();

        $invalidData = [
            'name' => '',
            'imdb_id' => 'invalid',
            'status' => 'invalid_status',
        ];

        $response = $this->actingAs($moderator)->patchJson("/api/films/{$film->id}", $invalidData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['name', 'imdb_id', 'status']);
    }

    /**
     * Тест получения несуществующего фильма.
     *
     * @return void
     */
    public function testCannotGetNonexistentFilm(): void
    {
        $response = $this->getJson('/api/films/999999');

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * Тест что пользователь видит фильм в избранном.
     *
     * @return void
     */
    public function testUserSeesFavoriteFilm(): void
    {
        $user = User::factory()->create();
        $film = Film::factory()->create(['status' => Film::STATUS_READY]);

        $user->favoriteFilms()->attach($film);

        $response = $this->actingAs($user)->getJson("/api/films/{$film->id}");

        $response->assertStatus(Response::HTTP_OK);

        $responseData = $response->json();
        $filmData = $responseData['data'];

        $this->assertArrayHasKey('is_favorite', $filmData);
        $this->assertTrue($filmData['is_favorite']);
    }

    /**
     * Тест пагинации в списке фильмов.
     *
     * @return void
     */
    public function testFilmsListHasPagination(): void
    {
        Film::factory()->count(15)->create(['status' => Film::STATUS_READY]);

        $response = $this->getJson('/api/films');

        $response->assertStatus(Response::HTTP_OK);

        $data = $response->json();

        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('current_page', $data['data']);
        $this->assertArrayHasKey('per_page', $data['data']);
        $this->assertArrayHasKey('total', $data['data']);
        $this->assertArrayHasKey('last_page', $data['data']);
        $this->assertArrayHasKey('links', $data['data']);

        $this->assertEquals(1, $data['data']['current_page']);
        $this->assertEquals(8, $data['data']['per_page']);
        $this->assertEquals(15, $data['data']['total']);
        $this->assertEquals(2, $data['data']['last_page']);

        $films = $data['data']['data'];
        $this->assertCount(8, $films);
    }
}
