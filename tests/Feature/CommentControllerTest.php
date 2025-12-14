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
     * Тест на получение структуры комментария в формате JSON.
     *
     * @return array
     */
    private function getTypicalCommentStructure(): array
    {
        return [
            'data' => [
                'id',
                'comment_id',
                'film_id',
                'text',
                'rating',
                'user_id',
                'author_name',
                'created_at',
            ],
        ];
    }

    /**
     * Тест метода index.
     */
    public function testIndex(): void
    {
        $film = Film::factory()->create();
        $commentCount = 10;

        Comment::factory()->count($commentCount)->create([
            'film_id' => $film->id,
        ]);

        $response = $this->getJson("/api/films/{$film->id}/comments");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'comment_id',
                        'film_id',
                        'text',
                        'rating',
                        'user_id',
                        'author_name',
                        'is_external',
                        'created_at',
                    ],
                ],
            ],
        ]);

        $comments = $response->json('data.data');
        $this->assertGreaterThan(0, count($comments));

        foreach ($comments as $comment) {
            $this->assertEquals($film->id, $comment['film_id']);
        }
    }

    /**
     * Тест автора внешнего комментария.
     */
    public function testExternalCommentAuthorName(): void
    {
        $film = Film::factory()->create();
        Comment::where('film_id', $film->id)->delete();

        Comment::create([
            'film_id' => $film->id,
            'user_id' => null,
            'comment_id' => null,
            'text' => 'Test comment',
            'rating' => 5,
            'is_external' => true,
        ]);

        $response = $this->getJson("/api/films/{$film->id}/comments");

        $response->assertStatus(Response::HTTP_OK);

        $responseData = $response->json();

        $this->assertArrayHasKey('data', $responseData);

        $paginationData = $responseData['data'];
        $this->assertArrayHasKey('data', $paginationData);

        $comments = $paginationData['data'];

        $this->assertCount(1, $comments);

        $comment = $comments[0];

        $this->assertEquals(Comment::ANONYMOUS_USER, $comment['author_name']);
        $this->assertNull($comment['user_id']);
        $this->assertTrue((bool)$comment['is_external']);
    }

    /**
     * Тест метода store при отсутствии авторизации.
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
        $response->assertJson([
            'message' => 'Запрос требует аутентификации',
        ]);
    }

    /**
     * Тест метода store при авторизации.
     */
    public function testStoreAuthorized(): void
    {
        $user = User::factory()->create();
        $film = Film::factory()->create();

        $data = [
            'text' => 'Это текст тестового комментария, с помощью которого осуществляется тестирование функционала комментариев.',
            'rating' => 8,
        ];

        $response = $this->actingAs($user)->postJson("/api/films/{$film->id}/comments", $data);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->getTypicalCommentStructure());
        $response->assertJsonPath('data.comment_id', null);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'film_id' => $film->id,
            'text' => $data['text'],
            'rating' => $data['rating'],
        ]);
    }

    /**
     * Тест метода store с некорректными данными.
     */
    public function testStoreValidationError(): void
    {
        $user = User::factory()->create();
        $film = Film::factory()->create();

        $data = [
            'text' => 'Короткий неправильный комментарий.',
            'rating' => 11,
        ];

        $response = $this->actingAs($user)->postJson("/api/films/{$film->id}/comments", $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'message' => 'Переданные данные не корректны',
        ]);
        $response->assertJsonValidationErrors([
            'text',
            'rating',
        ]);
    }

    /**
     * Тест метода store для ответа на комментарий.
     */
    public function testStoreReplyToComment(): void
    {
        $user = User::factory()->create();
        $film = Film::factory()->create();
        $parentComment = Comment::factory()->create(['film_id' => $film->id]);

        $data = [
            'text' => 'Это текст тестового комментария, с помощью которого осуществляется тестирование функционала комментариев.',
            'rating' => 8,
            'comment_id' => $parentComment->id,
        ];

        $response = $this->actingAs($user)->postJson("/api/films/{$film->id}/comments", $data);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->getTypicalCommentStructure());
        $response->assertJsonPath('data.comment_id', $parentComment->id);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'film_id' => $film->id,
            'text' => $data['text'],
            'rating' => $data['rating'],
            'comment_id' => $data['comment_id'],
        ]);
    }

    /**
     * Тест метода update при отсутствии авторизации.
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
        $response->assertJson([
            'message' => 'Запрос требует аутентификации',
        ]);
    }

    /**
     * Тест метода update с запретом доступа.
     */
    public function testUpdateForbidden(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $anotherUser = User::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $data = [
            'text' => 'Обновленный текст тестового комментария достаточной длины для прохождения валидации.',
            'rating' => 9,
        ];

        $response = $this->actingAs($user)->patchJson("/api/comments/{$comment->id}", $data);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson([
            'message' => 'Недостаточно прав',
        ]);
    }

    /**
     * Тест метода update с валидными данными.
     */
    public function testUpdateSuccess(): void
    {
        $comment = Comment::factory()->create();

        $data = [
            'text' => 'Обновленный текст тестового комментария достаточной длины для прохождения валидации.',
            'rating' => 9,
        ];

        $response = $this->actingAs($comment->user)->patchJson("/api/comments/{$comment->id}", $data);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->getTypicalCommentStructure());
        $response->assertJsonPath('data.text', $data['text']);
        $response->assertJsonPath('data.rating', $data['rating']);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'text' => $data['text'],
            'rating' => $data['rating'],
        ]);
    }

    /**
     * Тест метода update для модератора.
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

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->getTypicalCommentStructure());
        $response->assertJsonPath('data.text', $data['text']);
        $response->assertJsonPath('data.rating', $data['rating']);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'text' => $data['text'],
            'rating' => $data['rating'],
        ]);
    }

    /**
     * Тест метода update с невалидными данными.
     */
    public function testUpdateValidationError(): void
    {
        $comment = Comment::factory()->create();

        $data = [
            'text' => 'Короткий неправильный комментарий.',
            'rating' => 11,
        ];

        $response = $this->actingAs($comment->user)->patchJson("/api/comments/{$comment->id}", $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'message' => 'Переданные данные не корректны',
        ]);
        $response->assertJsonValidationErrors([
            'text',
            'rating',
        ]);
    }

    /**
     * Тест метода destroy без авторизации.
     */
    public function testDestroyUnauthorized(): void
    {
        $comment = Comment::factory()->create();

        $response = $this->deleteJson("/api/comments/{$comment->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson([
            'message' => 'Запрос требует аутентификации',
        ]);
    }

    /**
     * Тест метода destroy с отсутствующими правами.
     */
    public function testDestroyForbidden(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $comment = Comment::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/comments/{$comment->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson([
            'message' => 'Недостаточно прав',
        ]);
    }

    /**
     * Тест метода destroy с валидными данными.
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
     * Тест метода destroy для модератора.
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
     * Тест метода destroy с дочерними комментариями.
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
