<?php

namespace App\DTO;

class FilmData
{
    public string $name;
    public ?string $poster_image = null;
    public ?string $preview_image = null;
    public ?string $background_image = null;
    public ?string $background_color = null;
    public ?string $video_link = null;
    public ?string $preview_video_link = null;
    public string $description;
    public string $director;
    public int $released;
    public int $run_time;
    public string $imdb_id;
    public array $starring;
    public array $genre;
    public ?float $rating = null;
    public ?int $scores_count = null;

    public function __construct(
        string $name,
        string $description,
        string $director,
        int $released,
        int $run_time,
        string $imdb_id,
        array $starring,
        array $genre
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->director = $director;
        $this->released = $released;
        $this->run_time = $run_time;
        $this->imdb_id = $imdb_id;
        $this->starring = $starring;
        $this->genre = $genre;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'poster_image' => $this->poster_image,
            'preview_image' => $this->preview_image,
            'background_image' => $this->background_image,
            'background_color' => $this->background_color,
            'video_link' => $this->video_link,
            'preview_video_link' => $this->preview_video_link,
            'description' => $this->description,
            'director' => $this->director,
            'released' => $this->released,
            'run_time' => $this->run_time,
            'imdb_id' => $this->imdb_id,
            'starring' => $this->starring,
            'genre' => $this->genre,
            'rating' => $this->rating,
            'scores_count' => $this->scores_count,
        ];
    }
}
