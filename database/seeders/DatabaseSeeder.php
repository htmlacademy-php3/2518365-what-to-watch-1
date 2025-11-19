<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            GenreSeeder::class,
            ActorSeeder::class,
            FilmSeeder::class,
            CommentSeeder::class,
            PromoSeeder::class,
            ActorFilmSeeder::class,
            FilmGenreSeeder::class,
            UserFavoriteSeeder::class,
        ]);
    }
}
