<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** @psalm-suppress PossiblyUnusedMethod */
class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email,' . $this->user()->id,
            'password' => 'nullable|string|min:8',
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|file|image|max:10240',
        ];
    }
}
