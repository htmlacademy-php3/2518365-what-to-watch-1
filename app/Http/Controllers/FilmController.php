<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilmRequest;
use App\Http\Requests\StoreFilmRequest;
use App\Http\Requests\UpdateFilmRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use App\Jobs\CreateFilmJob;
use App\Models\Film;
use App\Services\ActorService;
use App\Services\GenreService;
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

        if (Auth::user() && Auth::user()->cannot('viewWithStatus', [Film::class, $status])) {
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
     * @param StoreFilmRequest $request Запрос
     * @return BaseResponse Ответ
     */
    public function store(StoreFilmRequest $request): BaseResponse
    {
        $imdbId = $request->validated('imdb_id');

        $data = [
            'imdb_id' => $imdbId,
            'status' => Film::STATUS_PENDING,
        ];

        Film::create($data);
        CreateFilmJob::dispatch($data);

        return new SuccessResponse($data, Response::HTTP_CREATED);
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
     * @param UpdateFilmRequest $request Запрос
     * @param Film $film Объект фильма
     * @return BaseResponse Ответ
     */
    public function update(UpdateFilmRequest $request, Film $film): BaseResponse
    {
        $film->update($request->validated());

        if ($request->has('starring')) {
            app(ActorService::class)->syncActors($film, $request->input('starring'));
        }

        if ($request->has('genre')) {
            app(GenreService::class)->syncGenres($film, $request->input('genre'));
        }

        return new SuccessResponse($film);
    }
}
