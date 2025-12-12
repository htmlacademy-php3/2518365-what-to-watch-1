<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id Идентификатор комментария
 * @property int $user_id Идентификатор пользователя
 * @property int $film_id Идентификатор фильма
 * @property int|null $comment_id Идентификатор родительского комментария
 * @property string $text Текст комментария
 * @property int|null $rating Рейтинг
 * @property bool $is_external Внешний ли комментарий
 * @property Carbon $created_at Дата создания
 * @property Carbon $updated_at Дата обновления
 *
 * @property-read User|null $user Автор комментария
 * @property-read Film $film Фильм
 * @property-read Comment|null $parent Родительский комментарий
 * @property-read Collection<int, Comment> $children Дочерние комментарии
 * @property-read int|null $children_count Количество дочерних комментариев
 * @property-read string $author_name Имя автора
 */
class Comment extends Model
{
    use HasFactory;

    public const string ANONYMOUS_USER = 'Гость';

    /**
     * Атрибуты, которые можно массово назначать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'film_id',
        'comment_id',
        'text',
        'rating',
        'is_external',
    ];

    /**
     * Вычисляемые атрибуты.
     *
     * @var array<string>
     */
    protected $appends = [
        'author_name',
    ];

    /**
     * Связь "один ко многим" к модели User.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь "один ко многим" к модели Film.
     *
     * @return BelongsTo
     */
    public function film(): BelongsTo
    {
        return $this->belongsTo(Film::class);
    }

    /**
     * Связь "многие к одному" к модели Comment (родительский комментарий).
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }

    /**
     * Связь "один ко многим" к модели Comment (дочерние комментарии).
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'comment_id');
    }

    /**
     * Проверка на дочерние комментарии.
     *
     * @return bool
     */
    public function isNotHaveChildren(): bool
    {
        return $this->children()->count() === 0;
    }

    /**
     * Получает имя автора комментария.
     *
     * @return string
     */
    public function getAuthorNameAttribute(): string
    {
        if ($this->is_external) {
            return self::ANONYMOUS_USER;
        }

        return $this->user->name;
    }

    /**
     * Получает дату последнего внешнего комментария.
     *
     * @return Carbon|null
     */
    public static function getLastExternalCommentDate(): ?Carbon
    {
        $lastCommentDate = self::where('is_external', true)->max('created_at');

        return $lastCommentDate ? Carbon::parse($lastCommentDate) : null;
    }
}
