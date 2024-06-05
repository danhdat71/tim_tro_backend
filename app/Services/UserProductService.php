<?php

namespace App\Services;

use App\Enums\PaginateEnum;
use App\Enums\ReportStatusReadEnum;
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

    public function getSelectPublicProductAttr()
    {
        return [
            'products.id',
            'title',
            'slug',
            'price',
            'acreage',
            'bed_rooms',
            'toilet_rooms',
            'ward_id',
            'district_id',
            'province_id',
        ];
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

    public function listSavedProducts($request)
    {
        $this->request = $request;
        $this->model = User::find($this->request->user()->id);

        $list = $this->model->userSavedProducts()
            ->with([
                'productImages' => function($q) {
                    $q->select('product_id', 'thumb_url');
                },
                'district' => function($q){
                    $q->select('id', 'name');
                },
                'province' => function($q){
                    $q->select('id', 'name');
                },
                'ward' => function($q){
                    $q->select('id', 'name');
                },
            ])
            ->when($this->request->order_by != '', function($q) {
                $orderBy = explode('|', $this->request->order_by);
                $q->orderBy($orderBy[0], $orderBy[1]);
            });

        if ($this->request->is_all) {
            $list = $list->pluck('id');
        } else {
            $list = $list->paginate(PaginateEnum::PAGINATE_10->value, $this->getSelectPublicProductAttr());
        }

        return $list;
    }

    public function listViewedProduct($request)
    {
        $this->request = $request;
        $this->model = User::find($this->request->user()->id);

        return $this->model->userViewedProduct()
            ->with([
                'productImages' => function($q) {
                    $q->select('product_id', 'thumb_url');
                },
                'district' => function($q){
                    $q->select('id', 'name');
                },
                'province' => function($q){
                    $q->select('id', 'name');
                },
                'ward' => function($q){
                    $q->select('id', 'name');
                },
            ])
            ->orderBy('users_viewed_products.created_at', 'desc')
            ->paginate(PaginateEnum::PAGINATE_10->value, $this->getSelectPublicProductAttr());
    }

    public function adminGetListBugReport($request)
    {
        $this->request = $request;
        $this->model = Product::class;

        return $this->model::select([
            'id',
            'title',
            'slug',
            'status',
            'posted_at',
            'district_id',
            'province_id',
            'ward_id',
            'user_id',
        ])
        ->whereHas('userReport')
        ->withCount('userReport')
        ->with([
            'productImages' => function($q) {
                $q->select('product_id', 'thumb_url');
            },
            'district' => function($q){
                $q->select('id', 'name');
            },
            'province' => function($q){
                $q->select('id', 'name');
            },
            'ward' => function($q){
                $q->select('id', 'name');
            },
            'user'=> function($q){
                $q->select('id', 'full_name', 'avatar');
            },
        ])
        ->orderBy('status', 'asc')
        ->paginate(PaginateEnum::PAGINATE_10->value);
    }

    public function adminGetDetailProductReport($request)
    {
        $this->request = $request;
        $this->model = Product::class;

        return $this->model::where('id', $this->request->id)
            ->with([
                'productImages' => function($q) {
                    $q->select('product_id', 'url');
                },
                'district' => function($q){
                    $q->select('id', 'name');
                },
                'province' => function($q){
                    $q->select('id', 'name');
                },
                'ward' => function($q){
                    $q->select('id', 'name');
                },
                'user'=> function($q){
                    $q->select('id', 'full_name', 'avatar');
                },
            ])
            ->withCount('userViews', 'userReport')
            ->firstOrFail();
    }

    public function adminGetListReport($request)
    {
        $this->request = $request;
        $this->model = UserReportProduct::class;

        return $this->model::select([
            'id',
            'full_name',
            'email',
            'tel',
            'is_read',
            'report_type',
        ])
        ->where('product_id', $this->request->id)
        ->orderBy('is_read', 'asc')
        ->paginate(PaginateEnum::PAGINATE_10->value);
    }

    public function adminGetDetailReport($request)
    {
        $this->request = $request;
        $this->model = UserReportProduct::class;

        $result = $this->model::findOrFail($this->request->id);
        $result->is_read = ReportStatusReadEnum::IS_READ->value;
        $result->save();

        return $result;
    }
}
