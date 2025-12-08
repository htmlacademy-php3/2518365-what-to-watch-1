<?php

namespace App\Jobs;

use App\Models\Film;
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
    public function __construct(private readonly string $imdbId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(MovieService $movieService): void
    {
        $movieData = $movieService->getMovie($this->imdbId);

        if ($movieData) {
            Film::createFromData($movieData);
        }
    }
}
