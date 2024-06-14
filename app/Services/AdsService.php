<?php

namespace App\Services;

use App\Enums\AdsStatusEnum;
use App\Enums\AdsTypeEnum;
use App\Enums\PaginateEnum;
use App\Models\Ads;
use App\Models\AdsAccess;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class AdsService
{
    public $model = null;
    public $request = null;

    public function getPublicListAttr()
    {
        return [
            'id',
            'img_url',
            'link',
        ];
    }

    public function getCreateAttr()
    {
        return [
            'img_url',
            'organization',
            'link',
            'type',
            'status',
            'expired_at',
        ];
    }

    public function getClickAttr()
    {
        return [
            'ads_id',
            'user_id',
            'guest_ip',
        ];
    }

    public function fillDataByFields($fiels = [])
    {
        foreach ($fiels as $value) {
            $this->model->{$value} = $this->request->{$value};
        }

        $this->model->save();
        return $this->model;
    }

    public function getPublicList($request)
    {
        $this->model = Ads::class;
        $this->request = $request;

        $sideLeft = $this->model::select($this->getPublicListAttr())
            ->where('status', AdsStatusEnum::SHOW->value)
            ->where('expired_at', '>=', Carbon::now())
            ->where('type', AdsTypeEnum::SIDE_LEFT->value)
            ->inRandomOrder()
            ->first();
        
        $sideRight = $this->model::select($this->getPublicListAttr())
            ->where('status', AdsStatusEnum::SHOW->value)
            ->where('expired_at', '>=', Carbon::now())
            ->where('type', AdsTypeEnum::SIDE_RIGHT->value)
            ->inRandomOrder()
            ->first();

        $topHead = $this->model::select($this->getPublicListAttr())
            ->where('status', AdsStatusEnum::SHOW->value)
            ->where('expired_at', '>=', Carbon::now())
            ->where('type', AdsTypeEnum::TOP_HEAD->value)
            ->inRandomOrder()
            ->limit(PaginateEnum::PAGINATE_10->value)
            ->get();

        return [
            'side_left' => $sideLeft,
            'side_right' => $sideRight,
            'top_head' => $topHead,
        ];
    }

    public function storeAdsImage($imageFile)
    {
        $hash = date('YmdHis') . Str::random(10);
        $imageFileName = 'image_' . $hash . '.jpg';
        $imagePath = "assets/imgs/ads/";
        $imageFullPath = $imagePath . $imageFileName;
        if (!Storage::disk('public_path')->exists($imagePath)) {
            Storage::disk('public_path')->makeDirectory($imagePath);
        }

        $img = Image::make($imageFile);
        $img->orientate()->save($imageFullPath, 100);

        return $imageFullPath;
    }

    public function create($request)
    {
        $this->model = new Ads;
        $this->request = $request;

        // Store ads image
        $this->request->img_url = $this->storeAdsImage($this->request->file('img_url'));
        return $this->fillDataByFields($this->getCreateAttr());
    }

    public function click($request)
    {
        $this->request = $request;
        $this->model = new AdsAccess;

        if ($user = Auth::user()) {
            $this->request->user_id = $user->id;
        } else {
            $this->request->guest_ip = $this->request->ip();
        }

        return $this->fillDataByFields($this->getClickAttr());
    }

    public function updateStatus($request)
    {
        $this->request = $request;
        $this->model = Ads::find($request->id);
        $this->model->status = $this->request->status;
        $this->model->save();

        return $this->model;
    }
}
