<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilmRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Film;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FilmController extends Controller
{
    protected const int PAGE_COUNT = 8;

    /**
     * Получение списка фильмов
     *
     * @param FilmRequest $request Запрос
     * @return BaseResponse Ответ
     */
    public function index(FilmRequest $request): BaseResponse
    {
        $pageCount = self::PAGE_COUNT;
        $page = $request->query('page');
        $genre = $request->query('genre');
        $status = $request->query('status', Film::STATUS_READY);
        $order_by = $request->query('order_by', Film::ORDER_BY_RELEASED);
        $order_to = $request->query('order_to', Film::ORDER_TO_DESC);

        if (Auth::user()->cannot('viewWithStatus', [Film::class, $status])) {
            return new FailResponse("Недостаточно прав для просмотра фильмов в статусе $status", Response::HTTP_FORBIDDEN);
        }

        $films = Film::query()
            ->select('id', 'name', 'preview_image', 'released', 'rating')
            ->when($genre, function ($query, $genre) {
                return $query->whereRelation('genres', 'name', $genre);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy($order_by, $order_to)
            ->paginate($pageCount);

        return new SuccessResponse($films);
    }

    /**
     * Добавление фильма в базу
     *
     * @param Request $request Запрос
     * @return BaseResponse Ответ
     */
    public function store(Request $request): BaseResponse
    {
        if (Auth::user()->cannot('create', Film::class)) {
            return new FailResponse('Недостаточно прав для создания фильма', Response::HTTP_FORBIDDEN);
        }
        try {
            return new SuccessResponse();
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }

    /**
     * Получение информации о фильме
     *
     * @param Film $film Объект фильма
     * @return BaseResponse Ответ
     */
    public function show(Film $film): BaseResponse
    {
        return new SuccessResponse($film);
    }

    /**
     * Редактирование фильма
     *
     * @param Request $request Запрос
     * @param Film $film Объект фильма
     * @return BaseResponse Ответ
     */
    public function update(Request $request, Film $film): BaseResponse
    {
        if (Auth::user()->cannot('update', $film)) {
            return new FailResponse('Недостаточно прав для редактирования фильма', Response::HTTP_FORBIDDEN);
        }
        try {
            return new SuccessResponse($film);
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }
}
