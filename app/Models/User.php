<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id Идентификатор пользователя
 * @property string $name Имя пользователя
 * @property string $email Email пользователя
 * @property string $password Пароль
 * @property string|null $avatar URL аватара
 * @property string $role Роль пользователя
 * @property Carbon $created_at Дата создания
 * @property Carbon $updated_at Дата обновления
 *
 * @property-read Collection<int, Film> $favoriteFilms Избранные фильмы
 * @property-read Collection<int, Comment> $comments Комментарии пользователя
 */
class User extends Authenticatable
{
    /** @use HasApiTokens<\Laravel\Sanctum\PersonalAccessToken> */
    use HasApiTokens;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    public const string ROLE_USER = 'user';
    public const string ROLE_MODERATOR = 'moderator';

    /**
     * Атрибуты, которые можно массово назначать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'role',
    ];

    /**
     * Скрытые атрибуты.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Связь "многие ко многим" к модели Film.
     *
     * @return BelongsToMany<Film>
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function favoriteFilms(): BelongsToMany
    {
        return $this->belongsToMany(Film::class, 'user_favorites');
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
     * Проверяет роль модератора у пользователя.
     *
     * @psalm-suppress PossiblyUnusedMethod
     * @return bool
     */
    public function isModerator(): bool
    {
        return $this->role === self::ROLE_MODERATOR;
    }

    /**
     * Проверяет фильм в избранном у пользователя.
     *
     * @psalm-suppress PossiblyUnusedMethod
     * @param int $filmId ID фильма.
     *
     * @return bool
     */
    public function isFavoriteFilm(int $filmId): bool
    {
        return $this->favoriteFilms()->where('film_id', $filmId)->exists();
    }
}
