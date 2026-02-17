<?php

namespace Modules\User\Http\Requests;

use Illuminate\Validation\Rules\Password;
use Spatie\LaravelData\Data;

class RegisterRequest extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {
    }

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'max:255',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                'confirmed'
            ],
        ];
    }
}
