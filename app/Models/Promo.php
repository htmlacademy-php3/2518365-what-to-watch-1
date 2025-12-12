<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id Идентификатор промо
 * @property int $film_id Идентификатор фильма
 * @property Carbon $created_at Дата создания
 * @property Carbon $updated_at Дата обновления
 *
 * @property-read Film $film Промо-фильм
 */
class Promo extends Model
{
    use HasFactory, Notifiable;

    /**
     * Название таблицы, связанной с моделью.
     *
     * @var string
     */
    protected $table = 'promo';

    /**
     * Атрибуты, которые можно массово назначать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'film_id',
    ];

    /**
     * Связь "один к одному" к модели Film.
     *
     * @return BelongsTo
     */
    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }
}
