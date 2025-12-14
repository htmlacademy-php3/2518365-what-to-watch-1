<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * @psalm-suppress UnusedClass
 */
class RegisterController extends Controller
{
    /**
     * Регистрация пользователя.
     *
     * @param RegisterRequest $request Запрос.
     * @return BaseResponse Ответ.
     */
    public function register(RegisterRequest $request): BaseResponse
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = $avatar->store('public/avatars', 'local');
            $data['avatar'] = $filename;
        }

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        /** @psalm-suppress UndefinedMethod */
        $token = $user->createToken('auth_token')->plainTextToken;

        return new SuccessResponse(['user' => $user, 'token' => $token]);
    }
}
