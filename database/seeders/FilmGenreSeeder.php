<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Film;
use Illuminate\Database\Seeder;

class FilmGenreSeeder extends Seeder
{
    public function run(): void
    {
        $films = Film::all();
        $genres = Genre::all();

        foreach ($films as $film) {
            $randomGenres = $genres->random(rand(1, 3));
            $film->genres()->attach($randomGenres);
        }
    }
}
