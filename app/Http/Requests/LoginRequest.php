<?php

namespace App\Http\Requests;

class LoginRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'user_identifier' => [
                'required'
            ],
            'password' => [
                'required',
            ],
        ];
    }
}
