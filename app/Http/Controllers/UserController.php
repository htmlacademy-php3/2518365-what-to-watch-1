<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Получение профиля пользователя
     *
     * @return BaseResponse Ответ
     */
    public function show(): BaseResponse
    {
        try {
            $user = Auth::user();
            return new SuccessResponse([
                'user' => $user,
            ]);
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
        try {
            return new SuccessResponse();
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }
}
