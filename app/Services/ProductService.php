<?php

namespace App\Services;

use App\Enums\PaginateEnum;
use App\Enums\ProductStatusEnum;
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
                $this->model->{$value} = date('YmdHis') . "-" . Str::slug($this->request->title);
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

    public function getStoreDraftAttributes()
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
            'status',
        ];
    }

    public function getUpdateAttributes()
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
        ];
    }

    public function getSelectProductAttr()
    {
        return [
            'id',
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
            'status',
        ];
    }

    public function getSelectProductImagesAttr()
    {
        return [
            'id',
            'product_id',
            'url',
            'thumb_url',
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
        if ($this->request->has('product_images') && sizeof($this->request->product_images) > 0) {
            foreach ($this->request->product_images as $imageFile) {
                $this->productImage = new ProductImage;
                $this->storeProductImage($imageFile, $created->id);
            }
        }

        return $this->getDetailById($created->id);
    }

    public function storeDraft($request)
    {
        $this->request = $request;
        $this->request->status = ProductStatusEnum::DRAFT->value;
        $this->model = new Product;

        $created = $this->fillDataByFields($this->getStoreDraftAttributes());

        // Store images
        if ($this->request->has('product_images') && sizeof($this->request->product_images) > 0) {
            foreach ($this->request->product_images as $imageFile) {
                $this->productImage = new ProductImage;
                $this->storeProductImage($imageFile, $created->id);
            }
        }

        return $this->getDetailById($created->id);
    }

    public function getDetailById($id)
    {
        return Product::where('id', $id)
            ->select($this->getSelectProductAttr())
            ->with([
                'productImages' => function($q) {
                    $q->select($this->getSelectProductImagesAttr())->get();
                }
            ])
            ->first();
    }

    public function getDetailByAuth($request)
    {
        $this->request = $request;

        return Product::where('id', $this->request->product_id)
            ->where('user_id', $this->request->user()->id)
            ->select($this->getSelectProductAttr())
            ->with([
                'productImages' => function($q) {
                    $q->select($this->getSelectProductImagesAttr())->get();
                }
            ])
            ->first();
    }

    public function update($request)
    {
        $this->request = $request;
        $this->model = Product::find($request->product_id);

        //When user delete old images
        if ($this->request->del_product_images != '') {
            $oldImagesId = explode(',', $this->request->del_product_images);
            foreach ($oldImagesId as $oldImageId) {
                $oldImage = ProductImage::where('id', $oldImageId)
                    ->where('product_id', $this->model->id)
                    ->first();
                if ($oldImage) {
                    Storage::disk('public_path')
                        ->delete([$oldImage->url ?? '', $oldImage->thumb_url ?? '']);
                    $oldImage->delete();
                }
            }
        }
        // When user upload new images
        if ($this->request->has('product_images') && sizeof($this->request->product_images) > 0) {
            foreach ($this->request->product_images as $imageFile) {
                $this->productImage = new ProductImage;
                $this->storeProductImage($imageFile, $this->model->id);
            }
        }

        $result = $this->fillDataByFields($this->getUpdateAttributes());
        return $this->getDetailById($result->id);
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
                    return $q->select($this->getSelectProductImagesAttr())
                        ->orderBy('id', 'asc')
                        ->get();
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
