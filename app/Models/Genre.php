<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id Идентификатор жанра
 * @property string $name Название жанра
 * @property Carbon $created_at Дата создания
 * @property Carbon $updated_at Дата обновления
 *
 * @property-read Collection<int, Film> $films Фильмы этого жанра
 */
class Genre extends Model
{
    use HasFactory;

    /**
     * Атрибуты, которые можно массово назначать.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Связь "многие ко многим" к модели Film.
     *
     * @return BelongsToMany
     */
    public function films(): BelongsToMany
    {
        return $this->belongsToMany(Film::class, 'film_genre', 'genre_id', 'film_id');
    }
}
