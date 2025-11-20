<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\SuccessResponse;
use App\Http\Responses\FailResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Аутентификация
     *
     * @param LoginRequest $request Запрос
     * @return BaseResponse Ответ
     */
    public function login(LoginRequest $request): BaseResponse
    {
        try {
            if (!Auth::attempt($request->validated())) {
                abort(Response::HTTP_UNAUTHORIZED, trans('auth.failed'));
            }

            $token = Auth::user()->createToken('auth_token')->plainTextToken;

            return new SuccessResponse([
                'token' => $token,
            ]);
        } catch (\Exception$e) {
            return new FailResponse(null, null, $e);
        }
    }

    /**
     * Выход
     *
     * @return BaseResponse Ответ
     */
    public function logout(): BaseResponse
    {
        try {
            Auth::user()->tokens()->delete();
            return new SuccessResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }
}
