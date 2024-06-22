<?php

namespace App\Services;

use App\Enums\NotificationStatusEnum;
use App\Enums\PaginateEnum;
use App\Jobs\SendFirebaseNotificationJob;
use App\Models\Notification;
use App\Models\UserFcmToken;
use Carbon\Carbon;
use App\Traits\NotificationTrait;

class NotificationService
{
    use NotificationTrait;

    public $model = null;
    public $request = null;

    public function getNotificationsAttr()
    {
        return [
            'id',
            'title',
            'description',
            'status',
            'link',
            'sent_at',
        ];
    }

    public function getList($request)
    {
        $this->model = Notification::class;
        $this->request = $request;

        return $this->model::select($this->getNotificationsAttr())
            ->where('user_id', $this->request->user()->id)
            ->orderBy('sent_at', 'desc')
            ->paginate($this->request->limit ?? PaginateEnum::PAGINATE_10->value);
    }

    public function push($title, $description, $userId = null, $link = null, $imageUrl = null)
    {
        $notification = new Notification;
        $notification->title = $title;
        $notification->description = $description;
        $notification->sent_at = Carbon::now()->format('Y-m-d H:i:s');
        if ($userId) {
            $notification->user_id = $userId;
        }
        if ($link) {
            $notification->link = $link;
        }
        $notification->save();

        // dispatch push firebase notification
        dispatch(
            new SendFirebaseNotificationJob(
                [
                    'title' => $notification->title,
                    'body' => $notification->description,
                    'image' => $imageUrl
                ],
                $userId
            )
        );

        return $notification;
    }

    public function pushManyUsers($title, $description, $userIds = [], $link = null)
    {
        foreach ($userIds as $userId) {
            $this->push($title, $description, $userId, $link);
        }

        return true;
    }

    public function checkExistAndPush($title, $description, $user_id = null, $link = null)
    {
        $isExist = Notification::where([
            'title' => $title,
            'description' => $description,
            'user_id' => $user_id,
            'status' => NotificationStatusEnum::UN_READ->value,
        ])->exists();

        if ($isExist == false) {
            return $this->push($title, $description, $user_id, $link);
        }

        return;
    }

    public function countUnread($request)
    {
        $this->model = Notification::class;
        $this->request = $request;

        return $this->model::where('user_id', $this->request->user()->id)
            ->where('status', NotificationStatusEnum::UN_READ->value)
            ->count();
    }

    public function markRead($request)
    {
        $this->model = Notification::class;
        $this->request = $request;

        $notification = $this->model::where('id', $this->request->id)
            ->where('user_id', $this->request->user()->id)
            ->first();
        $notification->status = $this->request->status;
        $notification->save();

        return [
            'un_read_count' => $this->countUnread($request),
        ];
    }

    public function markReadAll($request)
    {
        $this->model = Notification::class;
        $this->request = $request;

        return $this->model::where('user_id', $this->request->user()->id)
            ->update([
                'status' => NotificationStatusEnum::READ->value,
            ]);
    }

    public function deleteAll($request)
    {
        $this->model = Notification::class;
        $this->request = $request;

        $this->model::where('user_id', $this->request->user()->id)
            ->delete(); 

        return true;
    }

    public function storeFcmToken($request)
    {
        $this->request = $request;
        $this->model = UserFcmToken::class;

        if ($this->request->user() && $this->request->fcm_token != '') {
            $this->model::firstOrCreate(
                [
                    'fcm_token' => $this->request->fcm_token,
                    'user_id' => $this->request->user()->id,
                ],
                [
                    'fcm_token' => $this->request->fcm_token,
                ]
            );

            return $this->request->fcm_token;
        }

        return null;
    }

    public function removeFcmToken($fcmToken)
    {
        $this->model = UserFcmToken::class;

        return $this->model::where('fcm_token', $fcmToken)->delete();
    }

    public function testPushNotification($tokens)
    {
        $data = [
            'title' => 'Test',
            'body' => 'Test',
            'image' => null,
        ];

        return $this->sendNotificationToMultiple($tokens, $data);
    }
}
