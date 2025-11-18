<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Promo;
use Illuminate\Http\Request;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\FailResponse;
use Symfony\Component\HttpFoundation\Response;

class PromoController extends Controller
{
    /**
     * Получение текущего промо-фильма
     *
     * @return BaseResponse Ответ
     */
    public function index(): BaseResponse
    {
        try {
            $promo = Promo::latest()->first();
            return new SuccessResponse($promo);
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }

    /**
     * Установка нового промо-фильма
     *
     * @param Film $film Объект фильма
     * @return BaseResponse Ответ
     */
    public function store(Film $film): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }
}
