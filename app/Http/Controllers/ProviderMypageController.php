<?php

namespace App\Http\Controllers;

use App\Http\Requests\Mypage\UpdateAvatarRequest;
use App\Http\Requests\Mypage\UpdateItemInfoRequest;
use App\Services\AuthUserService;
use App\Services\ProductService;
use App\Services\UserService;
use Illuminate\Http\Request;

class ProviderMypageController extends Controller
{
    public UserService $userService;
    public ProductService $productService;
    public AuthUserService $authUserService;

    public function __construct(
        UserService $userService,
        ProductService $productService,
        AuthUserService $authUserService
    ) {
        $this->userService = $userService;
        $this->productService = $productService;
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

    public function publicInfo(Request $request)
    {
        $result = $this->userService->publicProviderInfo($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageNotfound();
    }

    public function publicProducts(Request $request)
    {
        $result = $this->productService->publicProviderProducts($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageNotfound();
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
