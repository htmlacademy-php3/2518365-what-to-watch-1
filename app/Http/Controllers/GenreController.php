<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenreRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Genre;

class GenreController extends Controller
{
    /**
     * Получение списка жанров
     *
     * @return BaseResponse
     */
    public function index(): BaseResponse
    {
        $genres = Genre::paginate();
        return new SuccessResponse($genres);
    }

    /**
     * Редактирование жанра
     *
     * @param GenreRequest $request Запрос
     * @param Genre $genre Объект жанра
     * @return BaseResponse Ответ
     */
    public function update(GenreRequest $request, Genre $genre): BaseResponse
    {
        $genre->update([
            'name' => $request->input('name'),
        ]);
        return new SuccessResponse($genre);
    }
}
