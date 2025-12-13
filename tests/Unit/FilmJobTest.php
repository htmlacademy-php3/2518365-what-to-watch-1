<?php

namespace Tests\Unit;

use App\DTO\FilmData;
use App\Jobs\CreateFilmJob;
use App\Models\Film;
use App\Services\ActorService;
use App\Services\FilmService;
use App\Services\GenreService;
use App\Services\MovieService\MovieRepositoryInterface;
use App\Services\MovieService\MovieService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class FilmJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест задачи CreateFilmJob.
     */
    public function testCreateFilmJob(): void
    {
        $imdbId = 'tt0944947';

        Film::factory()->create([
            'imdb_id' => $imdbId,
            'status' => Film::STATUS_PENDING,
        ]);

        $newMovieData = [
            'name' => 'Test Movie',
            'description' => 'Test description',
            'director' => 'Test Director',
            'released' => 2024,
            'run_time' => 120,
            'imdb_id' => $imdbId,
            'starring' => ['Actor 1', 'Actor 2'],
            'genre' => ['Action', 'Drama'],
            'poster_image' => 'test.jpg',
            'rating' => 8.5,
            'scores_count' => 1000,
        ];

        $mockMovieRepository = Mockery::mock(MovieRepositoryInterface::class);
        $mockMovieRepository->shouldReceive('findMovieById')
            ->with($imdbId)
            ->once()
            ->andReturn($newMovieData);

        $movieService = new MovieService($mockMovieRepository);
        $actorService = new ActorService();
        $genreService = new GenreService();
        $filmService = new FilmService($actorService, $genreService);

        $job = new CreateFilmJob($imdbId);
        $job->handle($movieService, $filmService);

        $this->assertDatabaseHas('films', [
            'imdb_id' => $imdbId,
            'status' => Film::STATUS_MODERATE,
            'name' => 'Test Movie',
            'description' => 'Test description',
            'director' => 'Test Director',
        ]);
    }

    /**
     * Тест задачи CreateFilmJob, когда фильм не найден.
     */
    public function testCreateFilmJobWhenMovieNotFound(): void
    {
        $imdbId = 'tt0944947';

        Film::factory()->create([
            'imdb_id' => $imdbId,
            'status' => Film::STATUS_PENDING,
        ]);

        $mockMovieRepository = Mockery::mock(MovieRepositoryInterface::class);
        $mockMovieRepository->shouldReceive('findMovieById')
            ->with($imdbId)
            ->once()
            ->andReturn(null);

        $movieService = new MovieService($mockMovieRepository);
        $actorService = new ActorService();
        $genreService = new GenreService();
        $filmService = new FilmService($actorService, $genreService);

        $job = new CreateFilmJob($imdbId);
        $job->handle($movieService, $filmService);

        $this->assertDatabaseMissing('films', [
            'imdb_id' => $imdbId,
        ]);
    }

    /**
     * Тест, что FilmData правильно преобразуется в массив.
     */
    public function testFilmDataToArray(): void
    {
        $filmData = new FilmData(
            'Test Movie',
            'Test description',
            'Test Director',
            ['Actor 1', 'Actor 2'],
            ['Action', 'Drama'],
            120,
            2024,
            'tt0944947'
        );

        $filmData->poster_image = 'test.jpg';
        $filmData->rating = 8.5;
        $filmData->scores_count = 1000;

        $array = $filmData->toArray();

        $this->assertEquals('Test Movie', $array['name']);
        $this->assertEquals('Test description', $array['description']);
        $this->assertEquals('Test Director', $array['director']);
        $this->assertEquals(['Actor 1', 'Actor 2'], $array['starring']);
        $this->assertEquals(['Action', 'Drama'], $array['genre']);
        $this->assertEquals(120, $array['run_time']);
        $this->assertEquals(2024, $array['released']);
        $this->assertEquals('tt0944947', $array['imdb_id']);
        $this->assertEquals('test.jpg', $array['poster_image']);
        $this->assertEquals(8.5, $array['rating']);
        $this->assertEquals(1000, $array['scores_count']);
    }

    /**
     * Очистка моков после каждого теста.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
