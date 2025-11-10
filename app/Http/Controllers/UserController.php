<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\FailResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Получение профиля пользователя
     *
     * @return BaseResponse Ответ
     */
    public function show(): BaseResponse
    {
        if (false) {
            return new FailResponse('Необходима авторизация', Response::HTTP_UNAUTHORIZED);
        }

        try {
            return new SuccessResponse();
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }

    /**
     * Обновление профиля пользователя
     *
     * @param Request $request Запрос
     * @return BaseResponse Ответ
     */
    public function update(Request $request): BaseResponse
    {
        if (false) {
            return new FailResponse();
        }

        try {
            return new SuccessResponse();
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }
}
