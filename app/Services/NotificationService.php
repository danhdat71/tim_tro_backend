<?php

namespace App\Services;
use App\Enums\NotificationStatusEnum;
use App\Enums\PaginateEnum;
use App\Models\Notification;
use Carbon\Carbon;

class NotificationService
{
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

    public function notificationsUnreadCount($request)
    {
        $this->model = Notification::class;
        $this->request = $request;

        $count = $this->model::where('user_id', $this->request->user()->id)
            ->where('status', NotificationStatusEnum::UN_READ->value)
            ->count();

        return [
            'count' => $count,
        ];
    }

    public function push($title, $description, $user_id = null, $link = null)
    {
        $notification = new Notification;
        $notification->title = $title;
        $notification->description = $description;
        $notification->sent_at = Carbon::now()->format('Y-m-d H:i:s');
        if ($user_id) {
            $notification->user_id = $user_id;
        }
        if ($link) {
            $notification->link = $link;
        }
        $notification->save();

        return $notification;
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
}
