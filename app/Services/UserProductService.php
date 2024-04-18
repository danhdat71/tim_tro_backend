<?php

namespace App\Services;

use App\Enums\SyncStatusEnum;
use App\Models\User;
use Carbon\Carbon;

class UserProductService
{
    public $model = null;
    public $request = null;

    public function saveProduct($request)
    {
        $this->request = $request;
        $this->model = User::find($this->request->user()->id);

        if ($this->request->action == SyncStatusEnum::ATTACH->value) {
            $this->model->userSavedProducts()->syncWithoutDetaching($this->request->product_id);
        } else {
            $this->model->userSavedProducts()->detach($this->request->product_id);
        }

        return true;
    }
}