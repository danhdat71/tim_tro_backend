<?php

namespace App\Services;

use App\Enums\UserStatusEnum;
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

    public function getResendOTPFields()
    {
        return [
            'verify_otp',
            'otp_expired_at',
        ];
    }

    public function getVerfiryOTPFields()
    {
        return [
            'verify_otp',
            'otp_expired_at',
            'status',
        ];
    }
    
    public function getLoginFields()
    {
        return [
            'last_login_at',
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
            else if ($value == 'last_login_at') {
                $this->model->{$value} = Carbon::now()->toDateTimeString();
            }
            else {
                $this->model->{$value} = $this->request->{$value};
            }
        }

        $this->model->save();
        return $this->model;
    }

    public function setUserByEmailOrTel($userIdentifier = null)
    {
        if ($userIdentifier == null) {
            $userIdentifier = $this->request->user_identifier;
        }

        if (filter_var($userIdentifier, FILTER_VALIDATE_EMAIL)) {
            $this->model = User::where('email', $userIdentifier)->first();
        } else {
            $this->model = User::where('tel', $userIdentifier)->first();
        }
    }

    public function register($request): User
    {
        $this->request = $request;
        $this->model = new User;

        return $this->fillDataByFields($this->getRegisterFields());
    }

    public function resendOTP($request): User
    {
        $this->request = $request;
        $this->setUserByEmailOrTel();

        return $this->fillDataByFields($this->getResendOTPFields());
    }

    public function verifyOTP($request): mixed
    {
        $this->request = $request;
        $this->setUserByEmailOrTel();
        
        if ($this->model == null) {
            return false;
        }

        if (Hash::check($this->request->verify_otp, $this->model->verify_otp)) {
            $this->request->status = UserStatusEnum::ACTIVE->value;
            $this->request->verify_otp = null;
            $this->request->otp_expired_at = null;
        } else {
            return false;
        }

        return $this->fillDataByFields($this->getVerfiryOTPFields());
    }

    public function login($request): mixed
    {
        $this->request = $request;
        $this->setUserByEmailOrTel();

        if (!Hash::check($this->request->password, $this->model->password)) {
            return false;
        }

        $tokenResult = $this->model->createToken(
            config('app.name'),
            ['*'],
            $this->request->is_remember == true ? null : Carbon::now()->addHours(config('auth.login_expired')),
        )->plainTextToken;

        $result = $this->fillDataByFields($this->getLoginFields());
        $result->access_token = explode('|', $tokenResult)[1];

        return $result;
    }
}
