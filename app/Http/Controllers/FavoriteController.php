<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Film;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Получение списка фильмов добавленных пользователем в избранное
     *
     * @return BaseResponse
     */
    public function index(): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }

    /**
     * Добавление фильма в избранное
     *
     * @param Request $request Запрос
     * @param Film $film Объект фильма
     * @return BaseResponse
     */
    public function store(Request $request, Film $film): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }

    /**
     * Удаление фильма из избранного
     *
     * @param Film $film Объект фильма
     * @return BaseResponse
     */
    public function destroy(Film $film): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }
}
