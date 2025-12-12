<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест регистрации пользователя.
     */
    public function testSuccessfulRegistration(): void
    {
        Storage::fake('local');

        $data = [
            'name' => 'John Doe',
            'email' => 'email@example.com',
            'password' => '12345678',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(['data' => ['token']]);

        $user = User::where('email', $data['email'])->first();

        $this->assertNotNull($user);
        $this->assertTrue(Hash::check($data['password'], $user->password));
        $this->assertEquals('public/avatars/' . $data['avatar']->hashName(), $user->avatar);
        Storage::disk('local')->assertExists('public/avatars/' . $data['avatar']->hashName());
    }

    /**
     * Тест валидации при регистрации.
     */
    public function testRegistrationValidationError(): void
    {
        $data = [
            'name' => '',
            'email' => 'not-valid-email',
            'password' => 'pw',
            'avatar' => UploadedFile::fake()->create('docx.docx', 51200),
        ];

        $response = $this->postJson("/api/register", $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['name', 'email', 'password', 'avatar']);
    }
}
