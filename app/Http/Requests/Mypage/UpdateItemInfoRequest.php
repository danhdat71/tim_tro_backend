<?php

namespace App\Http\Requests\Mypage;

use App\Enums\UserGenderEnum;
use App\Http\Requests\BaseRequest;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UpdateItemInfoRequest extends BaseRequest
{
    public $user = null;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function rules(): array
    {
        return [
            'app_id' => [
                'nullable',
                'min:5',
                'max:200',
                function($attr, $value, $fail) {
                    $appIdSluged = Str::slug($value);
                    $authUserId = $this->user->id;
                    $exists = User::where('app_id', $appIdSluged)->where('id', '<>', $authUserId)->exists();
                    if ($exists) {
                        return $fail(__('validation.unique'));
                    }
                },
            ],
            'full_name' => ['nullable', 'min:5', 'max:50'],
            'tel' => [
                'nullable',
                'min:10',
                'max:50',
                function($attr, $value, $fail) {
                    $authUserId = $this->user->id;
                    $exists = User::where('tel', $value)->where('id', '<>', $authUserId)->exists();
                    if ($exists) {
                        return $fail(__('validation.unique'));
                    }
                }
            ],
            'gender' => ['nullable', 'in:' . implode(',', UserGenderEnum::keyKeys())],
            'birthday' => ['nullable', 'date_format:Y-m-d'],
            'description' => ['nullable', 'max:5000'],
            'password' => ['nullable', 'min:8', 'max:200'],
            're_password' => ['nullable', 'same:password', 'required_with:password']
        ];
    }
}
