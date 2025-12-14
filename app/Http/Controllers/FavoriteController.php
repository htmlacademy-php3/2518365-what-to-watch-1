<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Film;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * @psalm-suppress UnusedClass
 */
class FavoriteController extends Controller
{
    /**
     * Получение списка фильмов добавленных пользователем в избранное.
     *
     * @return BaseResponse Ответ.
     */
    public function index(): BaseResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        /** @psalm-suppress UndefinedMethod */
        $favoriteFilms = $user->favoriteFilms()->orderBy('created_at', 'desc')->simplePaginate();

        return new SuccessResponse($favoriteFilms);
    }

    /**
     * Добавление фильма в избранное.
     *
     * @param Film $film Объект фильма.
     * @return BaseResponse Ответ.
     */
    public function store(Film $film): BaseResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        /** @psalm-suppress UndefinedMethod */
        if ($user->isFavoriteFilm($film->id)) {
            return new FailResponse('Фильм уже добавлен в избранное', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        /** @psalm-suppress UndefinedMethod */
        $user->favoriteFilms()->attach($film);

        return new SuccessResponse();
    }

    /**
     * Удаление фильма из избранного.
     *
     * @param Film $film Объект фильма.
     * @return BaseResponse Ответ.
     */
    public function destroy(Film $film): BaseResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        /** @psalm-suppress UndefinedMethod */
        if (!$user->isFavoriteFilm($film->id)) {
            return new FailResponse('Фильм отсутствует в избранном', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        /** @psalm-suppress UndefinedMethod */
        $user->favoriteFilms()->detach($film);

        return new SuccessResponse();
    }
}
