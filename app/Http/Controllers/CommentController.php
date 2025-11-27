<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Comment;
use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $comments = Comment::where('film_id', $film->id)->simplePaginate();
            return new SuccessResponse($comments);
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }

    /**
     * Добавление отзыва к фильму
     *
     * @param CommentRequest $request Запрос
     * @param Film $film Объект фильма
     * @return BaseResponse Ответ
     */
    public function store(CommentRequest $request, Film $film): BaseResponse
    {
        try {

            $comment = $film->comments()->create([
                'text' => $request->input('text'),
                'rating' => $request->input('rating'),
                'user_id' => Auth::user()->id,
            ]);
            return new SuccessResponse($comment);
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }

    /**
     * Редактирование отзыва к фильму
     *
     * @param CommentRequest $request Запрос
     * @param Comment $comment Объект отзыва
     * @return BaseResponse Ответ
     */
    public function update(CommentRequest $request, Comment $comment): BaseResponse
    {
        if (Auth::user()->cannot('update', $comment)) {
            return new FailResponse('Недостаточно прав', Response::HTTP_FORBIDDEN);
        }

        try {
            $comment->update([
                'text' => $request->input('text'),
                'rating' => $request->input('rating'),
            ]);
            return new SuccessResponse($comment);
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }

    /**
     * Удаление отзыва к фильму
     *
     * @param Request $request Запрос
     * @param Comment $comment Объект отзыва
     * @return BaseResponse Ответ
     */
    public function destroy(Request $request, Comment $comment): BaseResponse
    {
        if (Auth::user()->cannot('delete', $comment)) {
            return new FailResponse('Недостаточно прав', Response::HTTP_FORBIDDEN);
        }

        try {
            $comment->delete();
            return new SuccessResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }
}
