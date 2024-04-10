<?php

namespace App\Http\Controllers;

use App\Http\Requests\Mypage\UpdateAvatarRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class ProviderMypageController extends Controller
{
    public UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
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
}
