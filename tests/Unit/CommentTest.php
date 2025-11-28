<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест получения имени автора комментария
     */

    public function testAuthorName(): void
    {
        $user = User::factory()->create(['name' => 'Опознанный Енот']);
        $comment = Comment::factory()->for($user, 'user')->create(['is_external' => false]);

        $authorName = $comment->author_name;

        $this->assertEquals('Опознанный Енот', $authorName);
    }

    /**
     * Тест получения имени анонимного автора комментария
     */

    public function testAnonymousAuthorName(): void
    {
        $comment = Comment::factory()->create(['is_external' => true]);

        $authorName = $comment->author_name;

        $this->assertEquals(Comment::ANONYMOUS_USER, $authorName);
    }
}
