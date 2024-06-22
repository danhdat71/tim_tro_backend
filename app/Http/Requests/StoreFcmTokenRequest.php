<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFcmTokenRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'fcm_token' => ['nullable', 'string', 'max:255'],
        ];
    }
}
