<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест метода index, который возвращает список жанров.
     */
    public function testIndex(): void
    {
        Genre::factory()->count(3)->create();

        $response = $this->getJson('/api/genres');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                ],
            ],
        ]);
    }

    /**
     * Тест метода update без авторизации.
     */
    public function testUpdateUnauthorized(): void
    {
        $genre = Genre::factory()->create();
        $newGenre = 'newGenre';

        $response = $this->patchJson("/api/genres/{$genre->id}", [
            'name' => $newGenre,
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson([
            'message' => 'Запрос требует аутентификации',
        ]);
    }

    /**
     * Тест метода update при авторизации без прав модератора.
     */
    public function testUpdateAuthorized(): void
    {
        $genre = Genre::factory()->create();
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $newGenre = 'newGenre';

        $response = $this->actingAs($user)
            ->patchJson("/api/genres/{$genre->id}", [
                'name' => $newGenre,
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson([
            'message' => 'Недостаточно прав',
        ]);
    }

    /**
     * Тест метода update при авторизации с правами модератора.
     */
    public function testUpdateModerator(): void
    {
        $genre = Genre::factory()->create();
        $user = User::factory()->create(['role' => User::ROLE_MODERATOR]);
        $newGenre = 'newGenre';

        $response = $this->actingAs($user)
            ->patchJson("/api/genres/{$genre->id}", [
                'name' => $newGenre,
            ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'id' => $genre->id,
                'name' => $newGenre,
            ],
        ]);

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => $newGenre,
        ]);
    }

    /**
     * Тест валидации при обновлении жанра.
     */
    public function testUpdateValidationError(): void
    {
        $genre = Genre::factory()->create();
        $user = User::factory()->create(['role' => User::ROLE_MODERATOR]);
        $newGenre = '';

        $response = $this->actingAs($user)
            ->patchJson("/api/genres/{$genre->id}", [
                'name' => $newGenre,
            ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'message' => 'Переданные данные не корректны',
            'errors' => [
                'name' => [
                    'Поле Название обязательно для заполнения',
                ],
            ],
        ]);
    }
}
