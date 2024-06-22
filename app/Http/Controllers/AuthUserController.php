<?php

namespace App\Http\Controllers;

use App\Enums\UserStatusEnum;
use App\Http\Requests\AuthUserForgotPasswordRequest;
use App\Http\Requests\AuthUserRegisterRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LeaveSystemRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResendOTPRequest;
use App\Mail\AuthUserRegisterMail;
use App\Mail\AuthUserResetPasswordMail;
use App\Services\AuthUserService;
use App\Services\NotificationService;
use App\Services\SendMailService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\NumberTrait;
use Carbon\Carbon;

class AuthUserController extends Controller
{
    use NumberTrait;

    public AuthUserService $authUserService;
    public SendMailService $sendMailService;
    public UserService $userService;
    public NotificationService $notificationService;

    public function __construct(
        AuthUserService $authUserService,
        SendMailService $sendMailService,
        UserService $userService,
        NotificationService $notificationService
    ) {
        $this->authUserService = $authUserService;
        $this->sendMailService = $sendMailService;
        $this->userService = $userService;
        $this->notificationService = $notificationService;
    }

    public function register(AuthUserRegisterRequest $request)
    {
        if ($request->check) {
            return $this->responseMessageSuccess('Checked!');
        }
        $otp = $this->generateOTP();
        $request->verify_otp = Hash::make($otp);
        $request->otp_expired_at = Carbon::now()
            ->addMinutes(config('auth.otp_expired'))
            ->toDateTimeString();

        $this->sendMailService->sendMail(
            $request->email,
            AuthUserRegisterMail::class,
            $request->full_name,
            $otp
        );

        $result = $this->authUserService->register($request);

        if ($result) {
            return $this->responseMessageSuccess();
        }

        return $this->responseMessageBadrequest();
    }

    public function resendOTP(ResendOTPRequest $request)
    {
        $user = $this->userService->getUser($request->user_identifier);
        if (!$user) {
            return $this->responseMessageNotfound('user_not_found');
        }

        $otp = $this->generateOTP();
        $request->verify_otp = Hash::make($otp);
        $request->otp_expired_at = Carbon::now()
            ->addMinutes(config('auth.otp_expired'))
            ->toDateTimeString();

        $this->sendMailService->sendMail(
            $user->email,
            AuthUserRegisterMail::class,
            $user->full_name,
            $otp
        );

        $result = $this->authUserService->resendOTP($request);

        if ($result) {
            return $this->responseMessageSuccess();
        }

        return $this->responseMessageBadrequest();
    }

    public function verifyOTP(Request $request)
    {
        $result = $this->authUserService->verifyOTP($request);

        if ($result) {
            // Send notification to created user
            $this->notificationService->push(
                'Tạo tài khoản thành công !',
                'Chào mừng bạn đã đến với hệ thống tìm trọ ' . env('APP_NAME'),
                $result->id
            );
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest('Mã OTP không đúng vui lòng kiểm tra lại !');
    }

    public function forgotPassword(AuthUserForgotPasswordRequest $request)
    {
        $user = $this->userService->getUser($request->user_identifier);
        if (!$user) {
            return $this->responseMessageNotfound('user_not_found');
        }

        $otp = $this->generateOTP();
        $request->verify_otp = Hash::make($otp);
        $request->otp_expired_at = Carbon::now()
            ->addMinutes(config('auth.otp_expired'))
            ->toDateTimeString();

        $this->sendMailService->sendMail(
            $user->email,
            AuthUserResetPasswordMail::class,
            $user->full_name,
            $otp
        );

        $result = $this->authUserService->resendOTP($request);

        if ($result) {
            return $this->responseMessageSuccess();
        }

        return $this->responseMessageBadrequest();
    }

    public function verifyOTPChangePassword(Request $request)
    {
        $result = $this->authUserService->verifyOTPChangePassword($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest('Mã OTP không đúng vui lòng kiểm tra lại !');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $result = $this->authUserService->changePassword($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function login(LoginRequest $request)
    {
        $user = $this->userService->getUser($request->user_identifier);

        // When user is not verify OTP
        if ($user && $user->status == UserStatusEnum::INACTIVE->value) {
            $otp = $this->generateOTP();
            $request->verify_otp = Hash::make($otp);
            $request->otp_expired_at = Carbon::now()
                ->addMinutes(config('auth.otp_expired'))
                ->toDateTimeString();
            $this->sendMailService->sendMail(
                $user->email,
                AuthUserRegisterMail::class,
                $user->full_name,
                $otp
            );
            $result = $this->authUserService->resendOTP($request);

            return $this->responseDataSuccess([
                'status' => UserStatusEnum::INACTIVE,
            ]);
        }
        else if ($user && $user->status == UserStatusEnum::LEAVE->value) {
            return $this->responseMessageUnAuthorization('Tài khoản không tồn tại');
        }

        $result = $this->authUserService->login($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest('Tên đăng nhập hoặc mật khẩu không đúng !');
    }

    public function logout(Request $request)
    {
        $result = $this->authUserService->logout($request);

        if ($result) {
            $this->notificationService->removeFcmToken($request->fcm_token);
            return $this->responseMessageSuccess();
        }

        return $this->responseMessageBadrequest();
    }

    public function getMe()
    {
        $result = $this->authUserService->getMe();

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function leaveSystem(LeaveSystemRequest $request)
    {
        $result = $this->authUserService->leaveSystem($request);

        if ($result) {
            return $this->responseMessageSuccess();
        }

        return $this->responseMessageBadrequest();
    }
}
