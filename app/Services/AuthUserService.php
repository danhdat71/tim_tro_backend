<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Traits\NumberTrait;
use Carbon\Carbon;

class AuthUserService
{
    use NumberTrait;

    public $request = null;
    public $model = null;

    public function getRegisterFields()
    {
        return [
            'full_name',
            'email',
            'tel',
            'user_type',
            'password',
            'app_id',
            'verify_otp',
            'otp_expired_at',
        ];
    }

    public function fillDataByFields($fiels = [])
    {
        foreach ($fiels as $value) {
            if ($value == 'app_id') {
                $this->model->{$value} = Str::slug($this->request->full_name) . '-' . date('YmdHis');
            } 
            else if ($value == 'password') {
                $this->model->{$value} = Hash::make($this->request->password);
            }
            else if ($value == 'otp_expired_at') {
                $this->model->{$value} = Carbon::now()
                    ->addMinutes(config('auth.otp_expired'))
                    ->toDateTimeString();
            }
            else {
                $this->model->{$value} = $this->request->{$value};
            }
        }

        $this->model->save();
        return $this->model;
    }

    public function register($request)
    {
        $this->request = $request;
        $this->model = new User;

        return $this->fillDataByFields($this->getRegisterFields());
    }
}