<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SimilarControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест получения похожих фильмов.
     */
    public function testIndexSuccess(): void
    {
        $genre = Genre::factory()->create();

        $film = Film::factory()->create(['status' => Film::STATUS_READY]);
        $film->genres()->attach([$genre->id]);

        $similarFilm = Film::factory()->create(['status' => Film::STATUS_READY]);
        $similarFilm->genres()->attach([$genre->id]);

        $response = $this->getJson("/api/films/{$film->id}/similar");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
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
                ],
            ],
        ]);
    }

    /**
     * Тест выборки похожих фильмов.
     */
    public function testIndexSelectedCorrectly(): void
    {
        $genre1 = Genre::factory()->create();
        $genre2 = Genre::factory()->create();
        $genre3 = Genre::factory()->create();

        $film = Film::factory()->create(['status' => Film::STATUS_READY]);
        $film->genres()->attach([$genre1->id, $genre2->id, $genre3->id]);

        $similarFilm1 = Film::factory()->create(['status' => Film::STATUS_READY]);
        $similarFilm1->genres()->attach([$genre1->id]);

        $similarFilm2 = Film::factory()->create(['status' => Film::STATUS_READY]);
        $similarFilm2->genres()->attach([$genre1->id, $genre2->id]);

        $similarFilm3 = Film::factory()->create(['status' => Film::STATUS_READY]);
        $similarFilm3->genres()->attach([$genre1->id, $genre2->id, $genre3->id]);

        $differentFilm = Film::factory()->create(['status' => Film::STATUS_READY]);

        $response = $this->getJson("/api/films/{$film->id}/similar");
        $similarFilms = $response->json()['data'];
        $similarFilmsIds = array_column($similarFilms, 'id');

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(3, $similarFilms);
        $this->assertContains($similarFilm1->id, $similarFilmsIds);
        $this->assertContains($similarFilm2->id, $similarFilmsIds);
        $this->assertContains($similarFilm3->id, $similarFilmsIds);
        $this->assertNotContains($differentFilm->id, $similarFilmsIds);
    }
}
