<?php

namespace App\Http\Controllers;

use App\Http\Requests\FollowRequest;
use App\Services\FollowService;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public FollowService $followService;

    public function __construct(FollowService $followService)
    {
        $this->followService = $followService;
    }

    public function followers(Request $request)
    {
        $result = $this->followService->getFollowers($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function follow(FollowRequest $request)
    {
        $result = $this->followService->follow($request);

        if ($result) {
            return $this->responseMessageSuccess();
        }

        return $this->responseMessageBadrequest();
    }
}
