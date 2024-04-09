<?php

namespace App\Http\Requests;

class ChangePasswordRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'min:8',
                'max:200',
            ],
            're_password' => [
                'required',
                'same:password',
            ],
        ];
    }
}
