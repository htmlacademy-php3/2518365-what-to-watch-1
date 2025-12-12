<?php

namespace App\Jobs;

use App\Models\Film;
use App\Services\FilmService;
use App\Services\MovieService\MovieService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateFilmJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $data)
    {
    }

    /**
     * Execute the job.
     *
     * @param MovieService $movieService
     * @param FilmService $filmService
     * @return void
     */
    public function handle(MovieService $movieService, FilmService $filmService): void
    {
        $imdbId = $this->data['imdb_id'];

        $movieData = $movieService->getMovie($imdbId);

        if (!$movieData) {
            $filmService->deleteFilm($imdbId);
            return;
        }
        $filmService->updateFromData($movieData, Film::STATUS_MODERATE);
    }
}
