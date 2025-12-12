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
     * Получение отзывов к фильму.
     *
     * @param Film $film Объект фильма.
     * @return BaseResponse Ответ.
     */
    public function index(Film $film): BaseResponse
    {
        $comments = Comment::where('film_id', $film->id)->simplePaginate();
        return new SuccessResponse($comments);
    }

    /**
     * Добавление отзыва к фильму.
     *
     * @param CommentRequest $request Запрос.
     * @param Film $film Объект фильма.
     * @return BaseResponse Ответ.
     */
    public function store(CommentRequest $request, Film $film): BaseResponse
    {

        $comment = $film->comments()->create([
            'comment_id' => $request->get('comment_id', null),
            'text' => $request->input('text'),
            'rating' => $request->input('rating'),
            'user_id' => Auth::user()->id,
        ]);

        $film->rating();

        return new SuccessResponse($comment);

    }

    /**
     * Редактирование отзыва к фильму.
     *
     * @param CommentRequest $request Запрос.
     * @param Comment $comment Объект отзыва.
     * @return BaseResponse Ответ.
     */
    public function update(CommentRequest $request, Comment $comment): BaseResponse
    {
        $user = Auth::user();

        if ($user && $user->cannot('update', $comment)) {
            return new FailResponse('Недостаточно прав', Response::HTTP_FORBIDDEN);
        }

        $comment->update([
            'text' => $request->input('text'),
            'rating' => $request->input('rating'),
        ]);

        $film = $comment->film;
        $film->rating();

        return new SuccessResponse($comment);
    }

    /**
     * Удаление отзыва к фильму.
     *
     * @param Request $request Запрос.
     * @param Comment $comment Объект отзыва.
     * @return BaseResponse Ответ.
     */
    public function destroy(Request $request, Comment $comment): BaseResponse
    {
        $user = Auth::user();

        if ($user && $user->cannot('delete', $comment)) {
            return new FailResponse('Недостаточно прав', Response::HTTP_FORBIDDEN);
        }
        $comment->children()->delete();
        $comment->delete();

        $film = $comment->film;
        $film->rating();

        return new SuccessResponse(null, Response::HTTP_NO_CONTENT);
    }
}
