<?php

namespace App\Http\Controllers;

use App\Http\Requests\Mypage\UpdateAvatarRequest;
use App\Http\Requests\Mypage\UpdateItemInfoRequest;
use App\Services\AuthUserService;
use App\Services\UserService;
use Illuminate\Http\Request;

class ProviderMypageController extends Controller
{
    public UserService $userService;
    public AuthUserService $authUserService;

    public function __construct(
        UserService $userService,
        AuthUserService $authUserService
    ) {
        $this->userService = $userService;
        $this->authUserService = $authUserService;
    }

    public function mypage(Request $request)
    {
        $result = $this->userService->mypageUserProvider($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function updateAvatar(UpdateAvatarRequest $request)
    {
        $result = $this->userService->updateUserAvatar($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function updateItemData(UpdateItemInfoRequest $request)
    {
        if ($request->logout_other == true) {
            $this->authUserService->logoutOtherDevice($request);
        }
        $result = $this->userService->updateUserItemInfo($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }
}
