<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Film;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilmTest extends TestCase
{
    use RefreshDatabase;

    private const int USERS_COUNT = 5;

    /**
     * Тест получения рейтинга фильма.
     */

    public function testFilmRating(): void
    {

        $film = Film::factory()->create();
        $users = User::factory()->count(self::USERS_COUNT)->create();

        foreach ($users as $index => $user) {
            Comment::factory()->create([
                'film_id' => $film->id,
                'user_id' => $user->id,
            ]);
        }
        $film->rating();

        $avgRating = $film->comments()->avg('rating');
        $avgRating = $avgRating ? round($avgRating, 1) : 0;

        $this->assertEquals($avgRating, $film->rating);
    }
}
