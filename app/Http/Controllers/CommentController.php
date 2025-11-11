<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\FailResponse;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    /**
     * Получение отзывов к фильму
     *
     * @param string $filmId ID фильма
     * @return BaseResponse Ответ
     */
    public function index(string $filmId): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }

    /**
     * Добавление отзыва к фильму
     *
     * @param Request $request Запрос
     * @param string $filmId ID фильма
     * @return BaseResponse Ответ
     */
    public function store(Request $request, string $filmId): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }

    /**
     * Редактирование отзыва к фильму
     *
     * @param Request $request Запрос
     * @param string $id ID отзыва
     * @return BaseResponse
     */
    public function update(Request $request, string $id): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }

    /**
     * Удаление отзыва к фильму
     *
     * @param string $id ID отзыва
     * @return BaseResponse
     */
    public function destroy(string $id): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }
}
