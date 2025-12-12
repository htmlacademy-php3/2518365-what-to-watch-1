<?php

namespace Tests\Unit;

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

    public function testCreateFilmJob()
    {
        $imdbId = 'tt0944947';

        $data = [
            'imdb_id' => $imdbId,
            'status' => Film::STATUS_PENDING,
        ];

        $newMovie = Film::factory()->make([
            'imdb_id' => $imdbId,
        ])->toArray();

        $mockMovieRepository = Mockery::mock(MovieRepositoryInterface::class);
        $mockMovieRepository->shouldReceive('findMovieById')
            ->with($imdbId)
            ->once()
            ->andReturn($newMovie);
        $movieService = new MovieService($mockMovieRepository);

        $actorService = new ActorService();
        $genreService = new GenreService();
        $filmService = new FilmService($actorService, $genreService);

        Film::factory()->create($data);
        $job = new CreateFilmJob($data);
        $job->handle($movieService, $filmService);

        $this->assertDatabaseHas('films', [
            'imdb_id' => $imdbId,
            'status' => Film::STATUS_MODERATE,
        ]);
    }
}
