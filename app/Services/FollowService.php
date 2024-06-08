<?php

namespace App\Services;
use App\Enums\PaginateEnum;
use App\Enums\SyncStatusEnum;
use App\Models\Follow;
use App\Models\User;

class FollowService
{
    public $model = null;
    public $request = null;

    public function fillDataByFields($fiels = [])
    {
        foreach ($fiels as $value) {
            $this->model->{$value} = $this->request->{$value};
        }

        $this->model->save();
        return $this->model;
    }

    public function selectFollowersAttr()
    {
        return [
            'id',
            'app_id',
            'avatar',
            'full_name',
            'users.created_at',
        ];
    }

    public function getFollowers($request)
    {
        $this->request = $request;
        $this->model = User::find($this->request->user()->id);

        return $this->model->followers()
            ->paginate(PaginateEnum::PAGINATE_5->value, $this->selectFollowersAttr());
    }

    public function getFollowings($request)
    {
        $this->request = $request;
        $this->model = User::find($this->request->user()->id);

        $list = $this->model->follow();

        if ($this->request->is_all) {
            $list = $list->pluck('id');
        } else {
            $list = $list->paginate(PaginateEnum::PAGINATE_5->value, $this->selectFollowersAttr());
        }

        return $list;
    }

    public function getFollowerIdsOfProvider($request)
    {
        $this->request = $request;
        $this->model = User::find($this->request->user()->id);

        return $this->request->user()->followers()->pluck('id');
    }

    public function follow($request)
    {
        $this->request = $request;
        $this->model = User::find($this->request->user()->id);

        if ($this->request->action == SyncStatusEnum::ATTACH->value) {
            $this->model->follow()->syncWithoutDetaching($this->request->follower_receive_id);
        } else {
            $this->model->follow()->detach($this->request->follower_receive_id);
        }

        return true;
    }
}
