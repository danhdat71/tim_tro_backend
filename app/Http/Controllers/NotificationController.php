<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFcmTokenRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function getList(Request $request)
    {
        $result = $this->notificationService->getList($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function markRead(Request $request)
    {
        $result = $this->notificationService->markRead($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function markReadAll(Request $request)
    {
        $result = $this->notificationService->markReadAll($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function deleteAll(Request $request)
    {
        $result = $this->notificationService->deleteAll($request);

        if ($result) {
            return $this->responseMessageSuccess();
        }

        return $this->responseMessageBadrequest();
    }

    public function storeFcmToken(StoreFcmTokenRequest $request)
    {
        $result = $this->notificationService->storeFcmToken($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function testPushNotification(Request $request)
    {
        $result = $this->notificationService->testPushNotification($request->tokens);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }
}