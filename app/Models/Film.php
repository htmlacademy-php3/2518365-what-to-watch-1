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
 * @property int $id Идентификатор
 * @property string $name Название фильма
 * @property string|null $poster_image Постер
 * @property string|null $preview_image Превью изображение
 * @property string|null $background_image Фоновое изображение
 * @property string|null $background_color Цвет фона
 * @property string|null $video_link Ссылка на видео
 * @property string|null $preview_video_link Ссылка на превью видео
 * @property string|null $description Описание
 * @property string|null $director Режиссер
 * @property int|null $released Год выпуска
 * @property string|null $run_time Продолжительность
 * @property float|null $rating Рейтинг
 * @property int|null $scores_count Количество оценок
 * @property string|null $imdb_id IMDb ID
 * @property string|null $status Статус
 * @property Carbon $created_at Дата создания
 * @property Carbon $updated_at Дата обновления
 *
 * @property-read Collection<int, Genre> $genres Жанры
 * @property-read Collection<int, Actor> $actors Актёры
 * @property-read Collection<int, User> favoritedByUsers Пользователи, добавившие в избранное
 * @property-read Collection<int, Comment> $comments Комментарии
 * @property-read array $genre Список жанров
 * @property-read array $starring Список актёров
 * @property-read int|null $genres_count Количество жанров
 * @property-read int|null $actors_count Количество актёров
 * @property-read bool $is_favorite В избранном у текущего пользователя
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
    public const string ORDER_ASC = 'asc';
    public const string ORDER_DESC = 'desc';

    /**
     * Атрибуты, которые можно массово назначать.
     *
     * @var array<int, string>
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
     * Отношения, которые всегда загружаются с моделью.
     *
     * @var array
     */
    protected $with = [
        'actors',
        'genres',
    ];

    /**
     * Атрибуты, которые должны быть приведены к определенному типу.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'released' => 'integer',
        'rating' => 'float',
    ];

    /**
     * Дополнительные вычисляемые атрибуты.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'starring',
        'genre',
        'is_favorite',
    ];

    /**
     * Скрытые атрибуты.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'actors',
        'genres',
    ];

    /**
     * Связь "многие ко многим" к модели Genre.
     *
     * @return BelongsToMany<Genre>
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'film_genre', 'film_id', 'genre_id');
    }

    /**
     * Связь "многие ко многим" к модели Actor.
     *
     * @return BelongsToMany<Actor>
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'actor_film');
    }

    /**
     * Связь "многие ко многим" к модели User.
     *
     * @return BelongsToMany<User>
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_favorites');
    }

    /**
     * Связь "один ко многим" к модели Comment.
     *
     * @return HasMany<Comment>
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Получает рейтинг фильма.
     *
     * @psalm-suppress PossiblyUnusedMethod
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
     * Получает список жанров.
     *
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<int, string>
     */
    public function getGenreAttribute(): array
    {
        return $this->genres->pluck('name')->toArray();
    }

    /**
     * Получает список актеров.
     *
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<int, string>
     */
    public function getStarringAttribute(): array
    {
        return $this->actors->pluck('name')->toArray();
    }

    /**
     * Проверяет избранный фильм для текущего пользователя.
     *
     * @psalm-suppress PossiblyUnusedMethod
     * @psalm-suppress NoInterfaceProperties
     * @return bool
     */
    public function getIsFavoriteAttribute(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user !== null) {
            return $this->favoritedByUsers()->where('user_id', $user->id)->exists();
        }

        return false;
    }
}
