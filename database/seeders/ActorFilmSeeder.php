<?php

namespace Database\Seeders;

use App\Models\Actor;
use App\Models\Film;
use Illuminate\Database\Seeder;

class ActorFilmSeeder extends Seeder
{
    public function run(): void
    {
        $films = Film::all();
        $actors = Actor::all();

        $films->each(function (Film $film) use ($actors) {
            $randomActors = $actors->random(rand(1, 10));
            $film->actors()->attach($randomActors);
        });
    }
}
