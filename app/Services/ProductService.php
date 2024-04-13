<?php

namespace App\Services;

use App\Enums\PaginateEnum;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProductService
{
    public $request = null;
    public $model = null;
    public $productImage = null;

    public function fillDataByFields($fiels = [])
    {
        foreach ($fiels as $value) {
            if ($value == 'slug') {
                $this->model->{$value} = Str::slug($this->request->title) . "_" . date('YmdHis');
            }
            else if ($value == 'user_id') {
                $this->model->{$value} = $this->request->user()->id;
            }
            else {
                $this->model->{$value} = $this->request->{$value};
            }
        }

        $this->model->save();
        return $this->model;
    }

    public function getStoreAttributes()
    {
        return [
            'title',
            'slug',
            'province_id',
            'district_id',
            'ward_id',
            'price',
            'description',
            'tel',
            'detail_address',
            'lat',
            'long',
            'acreage',
            'bed_rooms',
            'toilet_rooms',
            'used_type',
            'is_shared_house',
            'time_rule',
            'is_allow_pet',
            'user_id',
            'posted_at',
        ];
    }

    public function storeProductImage($imageFile, $productId)
    {
        $userFolder = 'user_id_' . $this->request->user()->id;
        $imageFileName = 'image_' . date('ymdhis') . '.jpg';
        $imageFileNameThumb = 'thumb_image_' . date('ymdhis') . '.jpg';
        $userImagePath = "assets/imgs/$userFolder/product_$productId/";
        $imageFullPath = $userImagePath . $imageFileName;
        $imageFullPathThumb = $userImagePath . $imageFileNameThumb;
        if (!Storage::disk('public_path')->exists($userImagePath)) {
            Storage::disk('public_path')->makeDirectory($userImagePath);
        }

        $img = Image::make($imageFile);
        $img->save($imageFullPath, 100);
        $thumbnailImage = $img;
        $thumbnailImage->fit(config('image.product.thumb.width'), config('image.product.thumb.height'));
        $thumbnailImage->save($imageFullPathThumb, config('image.product.thumb.quality'));

        // Update data
        $this->productImage->url = $imageFullPath;
        $this->productImage->thumb_url = $imageFullPathThumb;
        $this->productImage->product_id = $productId;
        $this->productImage->save();
    }

    public function store($request)
    {
        $this->request = $request;
        $this->model = new Product;
        
        $created = $this->fillDataByFields($this->getStoreAttributes());

        // Store images
        if (sizeof($this->request->product_images) > 0) {
            foreach ($this->request->product_images as $imageFile) {
                $this->productImage = new ProductImage;
                $this->storeProductImage($imageFile, $created->id);
            }
        }

        return $created;
    }

    public function list($request)
    {
        $this->request = $request;
        $this->model = Product::class;

        $products = $this->model::select([
                'id',
                'title',
                'price'
            ])
            ->with([
                'productImages' => function($q) {
                    return $q->select([
                        'id',
                        'url',
                        'thumb_url',
                        'product_id',
                    ])->orderBy('id', 'asc')->get();
                },
            ])
            ->withCount(['userViews'])
            ->orderBy('created_at', 'asc')
            ->where('status', $this->request->status)
            ->paginate(PaginateEnum::PROVIDER_PRODUCT->value);

        return $products;
    }

    public function delete($request)
    {
        $this->request = $request;
        $this->model = Product::find($request->product_id);

        $userFolder = "user_id_{$this->request->user()->id}";
        $productFolder = "product_{$this->model->id}";

        //Remove product folder images
        Storage::disk('public_path')->deleteDirectory("assets/imgs/$userFolder/$productFolder");
        //Remove data
        $this->model->userViews()->delete();
        $this->model->productImages()->delete();
        $this->model->delete();

        return true;
    }
}
