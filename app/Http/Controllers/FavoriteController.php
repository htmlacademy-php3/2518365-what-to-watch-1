<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Film;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FavoriteController extends Controller
{
    /**
     * Получение списка фильмов добавленных пользователем в избранное
     *
     * @return BaseResponse
     */
    public function index(): BaseResponse
    {
        $favoriteFilms = Auth::user()->favoriteFilms()->orderBy('created_at', 'desc')->simplePaginate();

        return new SuccessResponse($favoriteFilms);
    }

    /**
     * Добавление фильма в избранное
     *
     * @param Film $film Объект фильма
     * @return BaseResponse
     */
    public function store(Film $film): BaseResponse
    {
        if (Auth::user()->isFavoriteFilm($film->id)) {
            return new FailResponse('Фильм уже добавлен в избранное', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        Auth::user()->favoriteFilms()->attach($film);

        return new SuccessResponse();
    }

    /**
     * Удаление фильма из избранного
     *
     * @param Film $film Объект фильма
     * @return BaseResponse
     */
    public function destroy(Film $film): BaseResponse
    {

        if (!Auth::user()->isFavoriteFilm($film->id)) {
            return new FailResponse('Фильм отсутствует в избранном', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        Auth::user()->favoriteFilms()->detach($film);

        return new SuccessResponse();
    }
}
