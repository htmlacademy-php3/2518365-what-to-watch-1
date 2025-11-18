<?php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\Http\Request;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\FailResponse;
use Symfony\Component\HttpFoundation\Response;

class FilmController extends Controller
{
    /**
     * Получение списка фильмов
     *
     * @param Request $request Запрос
     * @param int|null $page Номер страницы для пагинации
     * @param string|null $genre Фильтрация по жанру
     * @param string|null $status Фильтрация по статусу
     * @param string|null $order_by Правило сортировки
     * @param string|null $order_to Направление сортировки
     * @return BaseResponse Ответ
     */
    public function index(Request $request, ?int $page, ?string $genre, ?string $status, ?string $order_by, ?string $order_to): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }

    /**
     * Добавление фильма в базу
     *
     * @param Request $request Запрос
     * @return BaseResponse Ответ
     */
    public function store(Request $request): BaseResponse
    {
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
        try {
            return new SuccessResponse($film);
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
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
        try {
            return new SuccessResponse();
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }
}
