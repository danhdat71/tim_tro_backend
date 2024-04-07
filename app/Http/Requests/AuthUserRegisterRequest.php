<?php

namespace App\Http\Requests;

use App\Enums\UserTypeEnum;

class AuthUserRegisterRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'min:5', 'max:50'],
            'email' => [
                'required',
                'min:10',
                'max:100',
                'email',
                'unique:users,email',
            ],
            'tel' => [
                'required',
                'min:10',
                'max:50',
                'unique:users,tel',
            ],
            'user_type' => [
                'required',
                'in:' . implode(',', UserTypeEnum::getKeys()),
            ],
            'password' => [
                'required',
                'min:8',
                'max:200',
            ],
            're_password' => [
                'required',
                'same:password',
            ]
        ];
    }
}
