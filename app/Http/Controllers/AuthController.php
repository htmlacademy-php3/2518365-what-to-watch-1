<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Авторизация пользователя.
     *
     * @param LoginRequest $request Запрос.
     * @return BaseResponse Ответ.
     */
    public function login(LoginRequest $request): BaseResponse
    {
        if (!Auth::guard('web')->attempt($request->validated())) {
            return new FailResponse(trans('auth.failed'), Response::HTTP_UNAUTHORIZED);
        }

        $request->session()->regenerate();
        $token = Auth::user()->createToken('auth_token')->plainTextToken;

        return new SuccessResponse([
            'token' => $token,
        ]);
    }

    /**
     * Выход пользователя.
     *
     * @return BaseResponse Ответ.
     */
    public function logout(Request $request): BaseResponse
    {
        Auth::user()->tokens()->delete();
        $request->session()->invalidate();

        return new SuccessResponse(null, Response::HTTP_NO_CONTENT);
    }
}
