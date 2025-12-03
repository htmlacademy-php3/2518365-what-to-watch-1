<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property string $name
 * @property string|null $poster_image
 * @property string|null $preview_image
 * @property string|null $background_image
 * @property string|null $background_color
 * @property string|null $video_link
 * @property string|null $preview_video_link
 * @property string|null $description
 * @property string|null $director
 * @property int|null $released
 * @property string|null $run_time
 * @property float|null $rating
 * @property int|null $scores_count
 * @property string|null $imdb_id
 * @property string|null $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Collection|Genre[] $genres
 * @property Collection|Actor[] $actors
 * @property Collection|User[] $favoritedByUsers
 * @property Collection|Comment[] $comments
 */
class Film extends Model
{
    /** @use HasFactory<\Database\Factories\FilmFactory> */
    use HasFactory;

    public const string STATUS_READY = 'ready';
    public const string STATUS_PENDING = 'pending';
    public const string STATUS_MODERATE = 'moderate';
    public const string ORDER_BY_RELEASED = 'released';
    public const string ORDER_BY_RATING = 'rating';
    public const string ORDER_TO_ASC = 'asc';
    public const string ORDER_TO_DESC = 'desc';

    /**
     * Атрибуты
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'poster_image',
        'preview_image',
        'background_image',
        'background_color',
        'video_link',
        'preview_video_link',
        'description',
        'director',
        'released',
        'run_time',
        'rating',
        'scores_count',
        'imdb_id',
        'status',
    ];

    /**
     * Отношения
     *
     * @var array
     */
    protected $with = [
        'actors',
        'genres',
    ];

    /**
     * Атрибуты
     *
     * @var array
     */
    protected $casts = [
        'released' => 'integer',
        'rating' => 'float',
    ];

    /**
     * Атрибуты
     *
     * @var array
     */
    protected $appends = [
        'starring',
        'genre',
        'is_favorite',
    ];

    /**
     * Атрибуты
     *
     * @var array
     */
    protected $hidden = [
        'actors',
        'genres',
    ];

    /**
     * Связь "многие ко многим" к модели Genre
     *
     * @return BelongsToMany
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'film_genre', 'film_id', 'genre_id');
    }

    /**
     * Связь "многие ко многим" к модели Actor
     *
     * @return BelongsToMany
     */
    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'actor_film');
    }

    /**
     * Связь "многие ко многим" к модели User
     *
     * @return BelongsToMany
     */
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_favorites');
    }

    /**
     * Связь "один ко многим" к модели Comment
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Получает рейтинг фильма
     *
     * @return void
     */
    public function rating(): void
    {
        $avgRating = $this->comments()->avg('rating');
        $avgRating = $avgRating ? round($avgRating, 1) : 0;

        $this->rating = $avgRating;
        $this->save();
    }

    /**
     * Получает список жанров
     *
     * @return array
     */
    public function getGenreAttribute(): array
    {
        return $this->genres->pluck('name')->toArray();
    }

    /**
     * Получает список актеров
     *
     * @return array
     */
    public function getStarringAttribute(): array
    {
        return $this->actors->pluck('name')->toArray();
    }

    /**
     * Проверяет избранный фильм для текущего пользователя
     *
     * @return bool
     */
    public function getIsFavoriteAttribute(): bool
    {

        if (Auth::user()) {
            return $this->favoritedByUsers()->where('user_id', Auth::user()->id)->exists();
        }

        return false;
    }

}
