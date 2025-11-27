<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $film_id
 * @property int|null $parent_id
 * @property string $text
 * @property int|null $rating
 * @property bool $is_external
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property User $user
 * @property Film $film
 * @property Comment|null $parent
 * @property Collection|Comment[] $children
 */
class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;

    public const string ANONYMOUS_USER = 'Анонимный пользователь';

    /**
     * Атрибуты
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'film_id',
        'parent_id',
        'text',
        'rating',
        'is_external',
    ];

    /**
     * Дополнительные атрибуты
     *
     * @var string[]
     */
    protected $appends = [
        'author_name',
    ];

    /**
     * Связь "один ко многим" к модели User
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь "один ко многим" к модели Film
     *
     * @return BelongsTo
     */
    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    /**
     * Связь "многие к одному" к модели Comment (родительский комментарий)
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Связь "один ко многим" к модели Comment (дочерние комментарии)
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Проверка на дочерние комментарии
     *
     * @return bool
     */
    public function isHaveChildren(): bool
    {
        return $this->children()->count() === 0;
    }

    /**
     * Получает имя автора
     *
     * @return string
     */
    public function getAuthorNameAttribute(): string
    {
        if ($this->is_external) {
            return self::ANONYMOUS_USER;
        }

        return $this->user->name ?? self::ANONYMOUS_USER;
    }
}
