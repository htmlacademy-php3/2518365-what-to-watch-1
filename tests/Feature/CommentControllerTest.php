<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест получения списка комментариев к фильму.
     *
     * @return void
     */
    public function testIndex(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $film = Film::factory()->create();
        $commentCount = 10;

        Comment::factory()->count($commentCount)->create([
            'film_id' => $film->id,
            'user_id' => $user->id,
        ]);

        $response = $this->getJson("/api/films/{$film->id}/comments");

        $response->assertStatus(Response::HTTP_OK);

        $responseData = $response->json();
        $this->assertIsArray($responseData);

        if (isset($responseData['success'])) {
            $this->assertTrue($responseData['success']);
        }

        if (isset($responseData['data'])) {
            $data = $responseData['data'];
            $this->assertIsArray($data);

            if (isset($data['data'])) {
                $comments = $data['data'];
                $this->assertNotEmpty($comments);

                $firstComment = $comments[0];
                $this->assertArrayHasKey('id', $firstComment);
                $this->assertArrayHasKey('text', $firstComment);
                $this->assertArrayHasKey('rating', $firstComment);
                $this->assertArrayHasKey('user_id', $firstComment);
                $this->assertArrayHasKey('film_id', $firstComment);
                $this->assertArrayHasKey('author_name', $firstComment);
            }
        }
    }

    /**
     * Тест проверки имени автора внешнего комментария.
     *
     * @return void
     */
    public function testExternalCommentAuthorName(): void
    {
        $film = Film::factory()->create();

        Comment::factory()->create([
            'film_id' => $film->id,
            'is_external' => true,
        ]);

        $response = $this->getJson("/api/films/{$film->id}/comments");
        $response->assertStatus(Response::HTTP_OK);

        $responseData = $response->json();
        $comments = [];

        if (isset($responseData['data']['data'])) {
            $comments = $responseData['data']['data'];
        } elseif (isset($responseData['data'])) {
            $comments = is_array($responseData['data']) ? $responseData['data'] : [];
        }

        foreach ($comments as $comment) {
            $this->assertEquals(Comment::ANONYMOUS_USER, $comment['author_name'] ?? null);
        }
    }

    /**
     * Тест создания комментария неавторизованным пользователем.
     *
     * @return void
     */
    public function testStoreUnauthorized(): void
    {
        $film = Film::factory()->create();

        $data = [
            'text' => 'Это текст тестового комментария, с помощью которого осуществляется тестирование функционала комментариев.',
            'rating' => 8,
        ];

        $response = $this->postJson("/api/films/{$film->id}/comments", $data);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Тест успешного создания комментария авторизованным пользователем.
     *
     * @return void
     */
    public function testStoreAuthorized(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $film = Film::factory()->create();

        $data = [
            'text' => 'Это текст тестового комментария, с помощью которого осуществляется тестирование функционала комментариев.',
            'rating' => 8,
        ];

        $response = $this->actingAs($user)->postJson("/api/films/{$film->id}/comments", $data);
        $response->assertSuccessful();

        $this->assertDatabaseHas('comments', [
            'film_id' => $film->id,
            'user_id' => $user->id,
            'text' => $data['text'],
            'rating' => $data['rating'],
        ]);
    }

    /**
     * Тест валидации при создании комментария.
     *
     * @return void
     */
    public function testStoreValidationError(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $film = Film::factory()->create();

        $data = [
            'text' => 'Короткий',
            'rating' => 11,
        ];

        $response = $this->actingAs($user)->postJson("/api/films/{$film->id}/comments", $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['text', 'rating']);
    }

    /**
     * Тест создания ответа на комментарий.
     *
     * @return void
     */
    public function testStoreReplyToComment(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $film = Film::factory()->create();
        $parentComment = Comment::factory()->create(['film_id' => $film->id]);

        $data = [
            'text' => 'Это текст тестового комментария, с помощью которого осуществляется тестирование функционала комментариев.',
            'rating' => 8,
            'comment_id' => $parentComment->id,
        ];

        $response = $this->actingAs($user)->postJson("/api/films/{$film->id}/comments", $data);
        $response->assertSuccessful();

        $this->assertDatabaseHas('comments', [
            'film_id' => $film->id,
            'user_id' => $user->id,
            'comment_id' => $parentComment->id,
            'text' => $data['text'],
        ]);
    }

    /**
     * Тест обновления комментария неавторизованным пользователем.
     *
     * @return void
     */
    public function testUpdateUnauthorized(): void
    {
        $comment = Comment::factory()->create();

        $data = [
            'text' => 'Обновленный текст тестового комментария достаточной длины для прохождения валидации.',
            'rating' => 9,
        ];

        $response = $this->patchJson("/api/comments/{$comment->id}", $data);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Тест обновления чужого комментария пользователем.
     *
     * @return void
     */
    public function testUpdateForbidden(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $comment = Comment::factory()->create();

        $data = [
            'text' => 'Обновленный текст тестового комментария достаточной длины для прохождения валидации.',
            'rating' => 9,
        ];

        $response = $this->actingAs($user)->patchJson("/api/comments/{$comment->id}", $data);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Тест успешного обновления собственного комментария.
     *
     * @return void
     */
    public function testUpdateSuccess(): void
    {
        $comment = Comment::factory()->create();

        $data = [
            'text' => 'Обновленный текст тестового комментария достаточной длины для прохождения валидации.',
            'rating' => 9,
        ];

        $response = $this->actingAs($comment->user)->patchJson("/api/comments/{$comment->id}", $data);
        $response->assertSuccessful();

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'text' => $data['text'],
            'rating' => $data['rating'],
        ]);
    }

    /**
     * Тест обновления комментария модератором.
     *
     * @return void
     */
    public function testUpdateByModeratorSuccess(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_MODERATOR]);
        $comment = Comment::factory()->create();

        $data = [
            'text' => 'Обновленный текст тестового комментария достаточной длины для прохождения валидации.',
            'rating' => 9,
        ];

        $response = $this->actingAs($user)->patchJson("/api/comments/{$comment->id}", $data);
        $response->assertSuccessful();

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'text' => $data['text'],
            'rating' => $data['rating'],
        ]);
    }

    /**
     * Тест валидации при обновлении комментария.
     *
     * @return void
     */
    public function testUpdateValidationError(): void
    {
        $comment = Comment::factory()->create();

        $data = [
            'text' => 'Короткий',
            'rating' => 11,
        ];

        $response = $this->actingAs($comment->user)->patchJson("/api/comments/{$comment->id}", $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['text', 'rating']);
    }

    /**
     * Тест удаления комментария неавторизованным пользователем.
     *
     * @return void
     */
    public function testDestroyUnauthorized(): void
    {
        $comment = Comment::factory()->create();

        $response = $this->deleteJson("/api/comments/{$comment->id}");
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Тест удаления чужого комментария пользователем.
     *
     * @return void
     */
    public function testDestroyForbidden(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $comment = Comment::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/comments/{$comment->id}");
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * Тест успешного удаления собственного комментария.
     *
     * @return void
     */
    public function testDestroySuccess(): void
    {
        $comment = Comment::factory()->create();

        $response = $this->actingAs($comment->user)->deleteJson("/api/comments/{$comment->id}");
        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    /**
     * Тест удаления комментария модератором.
     *
     * @return void
     */
    public function testDestroyByModeratorSuccess(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_MODERATOR]);
        $comment = Comment::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/comments/{$comment->id}");
        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    /**
     * Тест удаления комментария с дочерними комментариями.
     *
     * @return void
     */
    public function testDestroyWithChildrenSuccess(): void
    {
        $childrenCount = 3;
        $user = User::factory()->create(['role' => User::ROLE_MODERATOR]);
        $comment = Comment::factory()->create();

        $childrenComments = Comment::factory()->count($childrenCount)->create([
            'film_id' => $comment->film_id,
            'comment_id' => $comment->id,
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/comments/{$comment->id}");
        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);

        foreach ($childrenComments as $childComment) {
            $this->assertDatabaseMissing('comments', [
                'id' => $childComment->id,
            ]);
        }
    }
}
