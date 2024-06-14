<?php

namespace App\Services;

use App\Enums\AdsStatusEnum;
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

        return $this->model::select([
            'id',
            'img_url',
            'link',
        ])
        ->where('status', AdsStatusEnum::SHOW->value)
        ->where('expired_at', '>=', Carbon::now())
        ->when($request->type != '', function($q){
            $q->where('type', $this->request->type);
        })
        ->get();
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
