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

            $request->session()->regenerate();
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
    public function logout(Request $request): BaseResponse
    {
        try {
            Auth::user()->tokens()->delete();
            $request->session()->invalidate();
            return new SuccessResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new FailResponse(null, null, $e);
        }
    }
}
