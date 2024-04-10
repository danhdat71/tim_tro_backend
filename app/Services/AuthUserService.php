<?php

namespace App\Services;

use App\Enums\UserStatusEnum;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Traits\NumberTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Crypt;

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

    public function getVerfiryOTPChangePasswordFields()
    {
        return [
            'verify_otp',
            'otp_expired_at',
            'remember_token',
        ];
    }

    public function getChangePasswordFields()
    {
        return [
            'password',
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

    public function resendOTP($request)
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

        $now = Carbon::now();
        $expiredAt = Carbon::parse($this->model->otp_expired_at);
        if (Hash::check($this->request->verify_otp, $this->model->verify_otp) && $now->lt($expiredAt)) {
            $this->request->status = UserStatusEnum::ACTIVE->value;
            $this->request->verify_otp = null;
            $this->request->otp_expired_at = null;
        } else {
            return false;
        }

        return $this->fillDataByFields($this->getVerfiryOTPFields());
    }

    public function verifyOTPChangePassword($request): mixed
    {
        $this->request = $request;
        $this->setUserByEmailOrTel();

        if ($this->model == null) {
            return false;
        }

        $now = Carbon::now();
        $expiredAt = Carbon::parse($this->model->otp_expired_at);
        if (Hash::check($this->request->verify_otp, $this->model->verify_otp) && $now->lt($expiredAt)) {
            $this->request->verify_otp = null;
            $this->request->otp_expired_at = null;
        } else {
            return false;
        }

        $this->fillDataByFields($this->getVerfiryOTPChangePasswordFields());
        $token = Crypt::encrypt([
            'token' => Password::createToken($this->model),
            'expired_at' => Carbon::now()->addMinutes(config('auth.otp_expired'))->toDateTimeString(),
            'user_identifier' => $this->model->email,
        ]);

        return [
            'token' => $token
        ];
    }

    public function changePassword($request): mixed
    {
        $this->request = $request;
        $info = Crypt::decrypt($this->request->token);
        $token = $info['token'];
        $expiredAt = $info['expired_at'];
        $userIdentifier = $info['user_identifier'];

        $this->setUserByEmailOrTel($userIdentifier);
        if ($this->model == null) {
            return false;
        }

        $now = Carbon::now();
        $expiredAt = Carbon::parse($expiredAt);

        if (Password::tokenExists($this->model, $token) && $now->lt($expiredAt)) {
            $this->fillDataByFields($this->getChangePasswordFields());
            Password::deleteToken($this->model);

            return true;
        }

        return false;
    }

    public function login($request): mixed
    {
        $this->request = $request;
        $this->setUserByEmailOrTel();

        if (!$this->model) {
            return false;
        }

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

    public function setUserByAuth()
    {
        $this->model = $this->request->user();
    }

    public function logout($request)
    {
        $this->request = $request;
        $this->setUserByAuth();
        return $this->model->currentAccessToken()->delete();
    }

    public function getMe()
    {
        $user = Auth::user();

        return $user;
    }
}
