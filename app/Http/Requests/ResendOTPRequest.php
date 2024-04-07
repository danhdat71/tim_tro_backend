<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResendOTPRequest extends FormRequest
{
    public function rules(): array
    {
        $userIdentifier = request()->user_identifier;
        $rules = [
            'user_identifier' => ['required']
        ];

        if (filter_var($userIdentifier, FILTER_VALIDATE_EMAIL)) {
            $rules['user_identifier'] = ['required', 'email'];
        }

        return $rules;
    }
}
