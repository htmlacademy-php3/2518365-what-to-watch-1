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
    public array $starring;
    public array $genre;
    public int $run_time;
    public int $released;
    public string $imdb_id;
    public ?float $rating = null;
    public ?int $scores_count = null;

    /**
     * Конструктор класса FilmData.
     *
     * @param string $name Название фильма.
     * @param string $description Описание фильма.
     * @param string $director Режиссер фильма.
     * @param array $starring Актеры фильма.
     * @param array $genre Жанры фильма.
     * @param int $run_time Длительность фильма.
     * @param int $released Год выпуска фильма.
     * @param string $imdb_id ID фильма в IMDB.
     */
    public function __construct(
        string $name,
        string $description,
        string $director,
        array  $starring,
        array  $genre,
        int    $run_time,
        int    $released,
        string $imdb_id,
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->director = $director;
        $this->starring = $starring;
        $this->genre = $genre;
        $this->run_time = $run_time;
        $this->released = $released;
        $this->imdb_id = $imdb_id;
    }

    /**
     * Преобразование объекта FilmData в массив.
     *
     * @return array Массив с данными фильма.
     */
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
            'starring' => $this->starring,
            'genre' => $this->genre,
            'run_time' => $this->run_time,
            'released' => $this->released,
            'imdb_id' => $this->imdb_id,
            'rating' => $this->rating,
            'scores_count' => $this->scores_count,
        ];
    }
}
