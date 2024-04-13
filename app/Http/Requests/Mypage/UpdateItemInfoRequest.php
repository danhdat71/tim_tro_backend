<?php

namespace App\Http\Requests\Mypage;

use App\Enums\UserGenderEnum;
use App\Http\Requests\BaseRequest;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdateItemInfoRequest extends BaseRequest
{
    public $user = null;
    public $request = null;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->request = request();
    }

    public function rules(): array
    {
        $rules = [];

        if ($this->request->has('app_id')) {
            $rules['app_id'] = [
                'required',
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
            ];
        }

        if ($this->request->has('full_name')) {
            $rules['full_name'] = ['required', 'min:5', 'max:50'];
        }

        if ($this->request->has('tel')) {
            $rules['tel'] = [
                'required',
                'digits_between:10,20',
                function($attr, $value, $fail) {
                    $authUserId = $this->user->id;
                    $exists = User::where('tel', $value)->where('id', '<>', $authUserId)->exists();
                    if ($exists) {
                        return $fail(__('validation.unique'));
                    }
                }
            ];
        }

        if ($this->request->has('gender')) {
            $rules['gender'] = ['required', 'in:' . implode(',', UserGenderEnum::getKeys())];
        }

        if ($this->request->has('birthday')) {
            $rules['gender'] = ['nullable', 'date_format:Y-m-d'];
        }

        if ($this->request->has('description')) {
            $rules['description'] = ['nullable', 'max:5000'];
        }

        if (
            $this->request->has('old_password') ||
            $this->request->has('password') ||
            $this->request->has('re_password')
        ) {
            $rules['old_password'] = [
                'required',
                'max:200',
                function ($attr, $value, $fail) {
                    if (!Hash::check($value, $this->user->password)) {
                        return $fail(__('validation.not_correct'));
                    }
                }
            ];
            $rules['password'] = ['required', 'min:8', 'max:200'];
            $rules['re_password'] = ['required', 'same:password'];
        }

        return $rules;
    }
}
