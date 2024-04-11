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
                'min:10',
                'max:50',
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
            $rules['gender'] = ['required', 'in:' . implode(',', UserGenderEnum::keyKeys())];
        }

        if ($this->request->has('birthday')) {
            $rules['gender'] = ['nullable', 'date_format:Y-m-d'];
        }

        if ($this->request->has('description')) {
            $rules['description'] = ['nullable', 'max:5000'];
        }

        if ($this->request->has('password') || $this->request->has('re_password')) {
            $rules['password'] = ['required', 'min:8', 'max:200'];
            $rules['re_password'] = ['required', 'same:password', 'required_with:password'];
        }

        return $rules;
    }
}
