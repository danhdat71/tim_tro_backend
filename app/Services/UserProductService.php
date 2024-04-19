<?php

namespace App\Services;

use App\Enums\SyncStatusEnum;
use App\Models\Product;
use App\Models\User;
use App\Models\UserReportProduct;
use Carbon\Carbon;

class UserProductService
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

    public function getReportProductAttr()
    {
        return [
            'user_id',
            'product_id',
            'full_name',
            'email',
            'tel',
            'is_read',
            'report_type',
            'description',
        ];
    }

    public function getSelectDetailProduct()
    {
        return ['id', 'user_id', 'title', 'posted_at'];
    }

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

    public function reportProduct($request)
    {
        $this->request = $request;
        $this->request->is_read = false;
        $this->model = new UserReportProduct;

        $this->fillDataByFields($this->getReportProductAttr());

        return $this->getDetailReportProduct($this->request->product_id);
    }

    public function getDetailReportProduct($productId)
    {
        return $this->model = Product::select($this->getSelectDetailProduct())
            ->where('id', $productId)
            ->first();
    }
}
