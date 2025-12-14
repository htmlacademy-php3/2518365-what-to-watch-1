<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Контроллер для аутентификации пользователей.
 *
 * @psalm-suppress UnusedClass
 */
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
        /** @psalm-suppress UndefinedMethod */
        if (!Auth::guard('web')->attempt($request->validated())) {
            return new FailResponse(trans('auth.failed'), Response::HTTP_UNAUTHORIZED);
        }

        $request->session()->regenerate();

        /**
         * @var \App\Models\User $user
         * @psalm-suppress UndefinedMethod
         */
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return new SuccessResponse([
            'token' => $token,
        ]);
    }

    /**
     * Выход пользователя.
     *
     * @param Request $request Запрос.
     * @return BaseResponse Ответ.
     */
    public function logout(Request $request): BaseResponse
    {
        /**
         * @var \App\Models\User|null $user
         * @psalm-suppress UndefinedMethod
         */
        $user = Auth::user();

        if ($user) {
            /** @psalm-suppress UndefinedMethod */
            $user->tokens()->delete();
        }

        $request->session()->invalidate();

        return new SuccessResponse(null, Response::HTTP_NO_CONTENT);
    }
}
