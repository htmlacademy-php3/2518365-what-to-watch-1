<?php

namespace App\Providers;

use App\Services\MovieService\MovieOmdbRepository;
use App\Services\MovieService\MovieRepositoryInterface;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MovieRepositoryInterface::class, function () {
            $client = new Client([
                'timeout' => config('services.omdb.timeout', 10),
                'connect_timeout' => config('services.omdb.connect_timeout', 5),
            ]);

            $config = [
                'api_key' => config('services.omdb.api_key'),
                'base_url' => config('services.omdb.base_url'),
                'cache_time' => (int)config('services.omdb.cache_time', 3600),
            ];

            return new MovieOmdbRepository($client, $config);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
