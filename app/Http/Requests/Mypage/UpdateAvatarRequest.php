<?php

namespace App\Http\Requests\Mypage;

use App\Http\Requests\BaseRequest;

class UpdateAvatarRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'avatar' => [
                'bail',
                'required',
                'image',
                'mimes:jpg,jpeg,png',
            ]
        ];
    }
}
