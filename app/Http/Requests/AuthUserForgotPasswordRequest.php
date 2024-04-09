<?php

namespace App\Http\Requests;

class AuthUserForgotPasswordRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'user_identifier' => ['required']
        ];
    }
}
