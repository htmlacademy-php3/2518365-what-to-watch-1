<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Film;
use App\Models\Promo;
use Illuminate\Http\Request;

/**
 * @psalm-suppress UnusedClass
 */
class PromoController extends Controller
{
    /**
     * Получение текущего промо-фильма.
     *
     * @return BaseResponse Ответ.
     */
    public function index(): BaseResponse
    {
        $promo = Promo::latest()->first();
        return new SuccessResponse($promo);
    }

    /**
     * Установка нового промо-фильма.
     *
     * @param Request $request Запрос.
     * @psalm-suppress PossiblyUnusedParam
     * @param Film $film Объект фильма.
     * @return BaseResponse Ответ.
     */
    public function store(Request $request, Film $film): BaseResponse
    {
        $promo = Promo::create(['film_id' => $film->id]);
        return new SuccessResponse($promo);
    }
}
