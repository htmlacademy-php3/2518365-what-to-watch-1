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
     * Тест получения списка жанров.
     *
     * @return void
     */
    public function testIndex(): void
    {
        Genre::factory()->count(3)->create();

        $response = $this->getJson('/api/genres');

        $response->assertStatus(Response::HTTP_OK);

        $responseData = $response->json();
        $this->assertIsArray($responseData);

        if (isset($responseData['data'])) {
            $data = $responseData['data'];
            $this->assertIsArray($data);
            $this->assertCount(3, $data);

            if (count($data) > 0) {
                $firstGenre = $data[0];
                $this->assertArrayHasKey('id', $firstGenre);
                $this->assertArrayHasKey('name', $firstGenre);
            }
        }
    }

    /**
     * Тест обновления жанра неавторизованным пользователем.
     *
     * @return void
     */
    public function testUpdateUnauthorized(): void
    {
        $genre = Genre::factory()->create();
        $newName = 'Новый жанр';

        $response = $this->patchJson("/api/genres/{$genre->id}", [
            'name' => $newName,
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }


    /**
     * Тест обновления жанра модератором.
     *
     * @return void
     */
    public function testUpdateModerator(): void
    {
        $genre = Genre::factory()->create();
        $user = User::factory()->create(['role' => User::ROLE_MODERATOR]);
        $newName = 'Новый жанр';

        $response = $this->actingAs($user)
            ->patchJson("/api/genres/{$genre->id}", [
                'name' => $newName,
            ]);

        $response->assertSuccessful();


        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => $newName,
        ]);
    }

    /**
     * Тест валидации при обновлении жанра.
     *
     * @return void
     */
    public function testUpdateValidationError(): void
    {
        $genre = Genre::factory()->create();
        $user = User::factory()->create(['role' => User::ROLE_MODERATOR]);
        $newName = '';

        $response = $this->actingAs($user)
            ->patchJson("/api/genres/{$genre->id}", [
                'name' => $newName,
            ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['name']);
    }
}
