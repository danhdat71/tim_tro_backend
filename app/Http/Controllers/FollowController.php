<?php

namespace App\Http\Controllers;

use App\Http\Requests\FollowRequest;
use App\Services\FollowService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public FollowService $followService;
    public NotificationService $notificationService;

    public function __construct(
        FollowService $followService,
        NotificationService $notificationService
    ) {
        $this->followService = $followService;
        $this->notificationService = $notificationService;
    }

    public function followers(Request $request)
    {
        $result = $this->followService->getFollowers($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function following(Request $request)
    {
        $result = $this->followService->getFollowings($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function follow(FollowRequest $request)
    {
        $result = $this->followService->follow($request);

        if ($result) {
            $request->is_all = true;
            $result = $this->followService->getFollowings($request);
            // Send notification to provider
            $this->notificationService->checkExistAndPush(
                "Thành viên {$request->user()->full_name} vừa theo dõi bạn.",
                "Hãy cập nhật bài viết ngay để mọi người dễ dàng tìm thấy.",
                $request->follower_receive_id,
                '/provider/hostel-regist'
            );

            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }
}
