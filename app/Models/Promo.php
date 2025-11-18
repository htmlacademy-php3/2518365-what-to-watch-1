<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $film_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Film $film
 */
class Promo extends Model
{
    /** @use HasFactory<\Database\Factories\PromoFactory> */
    use HasFactory, Notifiable;

    /**
     * Название таблицы
     *
     * @var string
     */
    protected $table = 'promo';

    /**
     * Атрибуты
     *
     * @var string[]
     */
    protected $fillable = [
        'film_id',
    ];

    /**
     * Связь "один к одному" к модели Film
     *
     * @return BelongsTo
     */
    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }
}
