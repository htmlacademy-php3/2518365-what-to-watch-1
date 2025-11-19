<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Film;
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
     * @param Film $film Объект фильма
     * @return BaseResponse Ответ
     */
    public function index(Film $film): BaseResponse
    {
        try {
            $comments = $film->comments;
            return new SuccessResponse($comments);
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }

    /**
     * Добавление отзыва к фильму
     *
     * @param Request $request Запрос
     * @param Film $film Объект фильма
     * @return BaseResponse Ответ
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
     * Редактирование отзыва к фильму
     *
     * @param Request $request Запрос
     * @param Comment $comment Объект отзыва
     * @return BaseResponse Ответ
     */
    public function update(Request $request, Comment $comment): BaseResponse
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
     * @param Comment $comment Объект отзыва
     * @return BaseResponse Ответ
     */
    public function destroy(Comment $comment): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }
}
