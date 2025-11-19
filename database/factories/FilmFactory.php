<?php

namespace Database\Factories;

use App\Models\Film;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Film>
 */
class FilmFactory extends Factory
{
    protected $model = Film::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(),
            'poster_image' => $this->faker->imageUrl(),
            'preview_image' => $this->faker->imageUrl(),
            'background_image' => $this->faker->imageUrl(),
            'background_color' => $this->faker->hexColor(),
            'video_link' => 'https://www.youtube.com/watch?v=' . $this->faker->sha1(),
            'preview_video_link' => 'https://www.youtube.com/watch?v=' . $this->faker->sha1(),
            'description' => $this->faker->paragraph(),
            'director' => $this->faker->name(),
            'released' => $this->faker->year(),
            'run_time' => $this->faker->numberBetween(60, 240),
            'rating' => $this->faker->randomFloat(1, 1, 5),
            'scores_count' => $this->faker->numberBetween(0, 10000),
            'imdb_id' => 'tt' . $this->faker->numberBetween(0000001, 9999999),
            'status' => $this->faker->randomElement(['pending', 'moderate', 'ready']),
        ];
    }
}
