<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\SuccessResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * @psalm-suppress UnusedClass
 */
class UserController extends Controller
{
    /**
     * Получение профиля пользователя.
     *
     * @return BaseResponse Ответ.
     */
    public function show(): BaseResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return new SuccessResponse([
            'user' => $user,
        ]);
    }

    /**
     * Обновление профиля пользователя.
     *
     * @param UpdateUserRequest $request Запрос.
     * @return BaseResponse Ответ.
     */
    public function update(UpdateUserRequest $request): BaseResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = [
            'email' => $request->input('email'),
            'name' => $request->input('name'),
        ];

        if ($request->has('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }

        $oldAvatar = null;
        if ($request->hasFile('avatar')) {
            /** @var \Illuminate\Http\UploadedFile $newAvatar */
            $newAvatar = $request->file('avatar');
            $oldAvatar = $user->avatar;
            $filename = $newAvatar->store('public/avatars', 'local');
            $data['avatar'] = $filename;
        }

        /** @psalm-suppress UndefinedMethod */
        $user->update($data);

        if ($oldAvatar) {
            Storage::delete($oldAvatar);
        }

        return new SuccessResponse([
            'user' => $user,
        ]);
    }
}
