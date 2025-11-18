<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\FailResponse;
use App\Models\Film;
use Symfony\Component\HttpFoundation\Response;

class SimilarController extends Controller
{
    /**
     * Получение списка похожих фильмов
     *
     * @param Film $film Объект фильма
     * @return BaseResponse Ответ
     */
    public function index(Film $film): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }
}
