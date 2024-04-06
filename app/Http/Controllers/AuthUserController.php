<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthUserRegisterRequest;
use App\Mail\AuthUserRegisterMail;
use App\Services\AuthUserService;
use App\Services\SendMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\NumberTrait;

class AuthUserController extends Controller
{
    use NumberTrait;

    public AuthUserService $authUserService;
    public SendMailService $sendMailService;

    public function __construct(
        AuthUserService $authUserService,
        SendMailService $sendMailService
    ) {
        $this->authUserService = $authUserService;
        $this->sendMailService = $sendMailService;
    }

    public function register(AuthUserRegisterRequest $request)
    {
        $otp = $this->generateOTP();
        $request->verify_otp = Hash::make($otp);

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
}
