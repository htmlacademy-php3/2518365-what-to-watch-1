<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Film;
use Illuminate\Database\Seeder;

class UserFavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $films = Film::all();

        foreach ($users as $user) {
            $randomFilms = $films->random(rand(1, 10));
            $user->favoriteFilms()->attach($randomFilms);
        }
    }
}
