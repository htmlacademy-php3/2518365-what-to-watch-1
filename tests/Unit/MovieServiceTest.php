<?php

namespace Tests\Unit;

use App\Models\Film;
use App\Services\MovieService\MovieRepositoryInterface;
use App\Services\MovieService\MovieService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class MovieServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тестирование метода getMovie() класса MovieService
     */
    public function testMovieServiceData(): void
    {
        $imdbId = 'tt0944947';

        $newMovie = Film::factory()->make([
            'imdb_id' => $imdbId,
        ])->toArray();

        $mockMovieRepository = Mockery::mock(MovieRepositoryInterface::class);
        $mockMovieRepository->shouldReceive('findMovieById')
            ->with($imdbId)
            ->once()
            ->andReturn($newMovie);
        $movieService = new MovieService($mockMovieRepository);

        $result = $movieService->getMovie($imdbId);

        $this->assertEquals($newMovie, $result);
    }
}
