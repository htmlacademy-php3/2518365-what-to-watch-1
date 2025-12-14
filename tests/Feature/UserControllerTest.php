<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест получения данных пользователя.
     */
    public function testShowUser(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/api/user');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'avatar',
                    'role',
                ],
            ],
        ]);
        $response->assertJson([
            'data' => [
                'user' => $user->toArray(),
            ],
        ]);
    }

    /**
     * Тест обновления данных пользователя гостем.
     */
    public function testUpdateUserDataByGuest(): void
    {
        $newName = 'New Name';
        $newEmail = 'newemail@mail.ru';

        $response = $this->patchJson('/api/user', [
            'name' => $newName,
            'email' => $newEmail,
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson([
            'message' => 'Запрос требует аутентификации',
        ]);
    }

    /**
     * Тест обновления данных пользователя.
     */
    public function testUpdateUserData(): void
    {
        $user = User::factory()->create();
        $newName = 'New Name';
        $newEmail = 'newemail@mail.ru';

        $response = $this->actingAs($user)->patchJson('/api/user', [
            'name' => $newName,
            'email' => $newEmail,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'user' => [
                    'name' => $newName,
                    'email' => $newEmail,
                ],
            ],
        ]);
    }

    /**
     * Тест изменения пароля пользователя.
     */
    public function testUpdatePasswordChanges(): void
    {
        $user = User::factory()->create();
        $newPassword = 'Qwerty123';

        $response = $this->actingAs($user)->patchJson('/api/user', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $newPassword,
        ]);

        $user->refresh();

        $response->assertStatus(Response::HTTP_OK);
        $this->assertTrue(Hash::check($newPassword, $user->password));
    }

    /**
     * Тест изменения аватара пользователя.
     */
    public function testUpdateAvatarChanges(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $avatar = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)->patchJson('/api/user', [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $avatar,
        ]);

        $user->refresh();

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals('public/avatars/' . $avatar->hashName(), $user->avatar);
        Storage::disk('local')->assertExists('public/avatars/' . $avatar->hashName());
    }

    /**
     * Тест ошибок валидации при обновлении данных пользователя.
     */
    public function testUpdateValidationError(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        $data = [
            'email' => $anotherUser->email,
            'password' => 'Qwerty',
            'name' => '',
            'avatar' => UploadedFile::fake()->create('docx.docx', 51200),
        ];

        $response = $this->actingAs($user)->patchJson("/api/user", $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors([
            'email',
            'password',
            'name',
            'avatar',
        ]);
    }

}
