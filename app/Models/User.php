<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $avatar
 * @property string $role
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Collection|Film[] $favoriteFilms
 * @property Collection|Comment[] $comments
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const string ROLE_USER = 'user';
    public const string ROLE_MODERATOR = 'moderator';

    /**
     * Атрибуты
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'role',
    ];

    /**
     * Скрытые атрибуты
     *
     * @var string[]
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Связь "многие ко многим" к модели Film
     *
     * @return BelongsToMany
     */
    public function favoriteFilms(): BelongsToMany
    {
        return $this->belongsToMany(Film::class, 'user_favorites');
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
     * Проверяет роль модератора у пользователя
     *
     * @return bool
     */
    public function isModerator(): bool
    {
        return $this->role === self::ROLE_MODERATOR;
    }
}
