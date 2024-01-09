<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Password;

use App\Models\User;

class NewUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => [
                'required', 'string', 'regex:' . User::REGEX_USERNAME,
                'max:255', 'unique:users,username',
            ],
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => [
                'required', 'string', 'max:255',
                Password::min(8),
            ]
        ];
    }

    public function validationData()
    {
        return Arr::wrap($this->input('user'));
    }
}
