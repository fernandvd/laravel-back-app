<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

use App\Models\User;

class UpdateUserRequest extends FormRequest
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
        $user = $this->user();
        if ($user === null) {
            throw new InvalidArgumentException('User not authenticate');
        }
        return [
            'username' => [
                'sometimes', 'string', 'max:255', 'regex:' . User::REGEX_USERNAME,
                Rule::unique('users', 'username')->ignore($user->getKey()),
            ],
            'email' => [
                'sometimes', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')
                    ->ignore($user->getKey()),
            ],
            'bio' => 'sometimes|nullable|string',
            'image' => 'sometimes|nullable|string|url',
        ];
    }

    public function validationData()
    {
        return Arr::wrap($this->input('user'));
    }
}
