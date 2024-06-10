<?php

namespace App\Services;

use App\Enums\PaginateEnum;
use App\Enums\ProductStatusEnum;
use App\Jobs\SendNotiProductPolicyNgJob;
use App\Mail\NotiFollowerProviderCreateProductMail;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\UserViewedProduct;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use App\Services\SendMailService;

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

    public function getPublicDraftAttributes()
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
            'status',
            'posted_at',
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
        $hash = date('YmdHis') . Str::random(10);
        $userFolder = 'user_id_' . $this->request->user()->id;
        $imageFileName = 'image_' . $hash . '.jpg';
        $imageFileNameThumb = 'thumb_image_' . $hash . '.jpg';
        $userImagePath = "assets/imgs/$userFolder/product_$productId/";
        $imageFullPath = $userImagePath . $imageFileName;
        $imageFullPathThumb = $userImagePath . $imageFileNameThumb;
        if (!Storage::disk('public_path')->exists($userImagePath)) {
            Storage::disk('public_path')->makeDirectory($userImagePath);
        }

        $img = Image::make($imageFile);
        $img->save($imageFullPath, 100);
        $img = Image::make($imageFile);
        $img->orientate()->fit(config('image.product.thumb.width'), config('image.product.thumb.height'));
        $img->save($imageFullPathThumb, config('image.product.thumb.quality'));

        return [
            'url' => $imageFullPath,
            'thumb_url' => $imageFullPathThumb,
            'product_id' => $productId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }

    public function store($request)
    {
        $this->request = $request;
        $this->request->posted_at = Carbon::now()->toDateTimeString();
        $this->model = new Product;
        $this->productImage = new ProductImage;
        $created = $this->fillDataByFields($this->getStoreAttributes());

        // Store images
        $imageData = [];
        if ($this->request->has('product_images') && sizeof($this->request->product_images) > 0) {
            foreach ($this->request->product_images as $imageFile) {
                $imageData[] = $this->storeProductImage($imageFile, $created->id);
            }
        }
        $this->productImage->insert($imageData);

        $detail = $this->getDetailById($created->id);

        // Send notification to following finders
        $followers = $this->request->user()->followers()->get();
        foreach ($followers as $follower) {
            SendMailService::sendMail(
                $follower->email,
                NotiFollowerProviderCreateProductMail::class,
                $detail->title,
                $follower->full_name,
                $detail,
                $detail->user
            );
        }

        return $detail;
    }

    public function storeDraft($request)
    {
        $this->request = $request;
        $this->request->status = ProductStatusEnum::DRAFT->value;
        $this->model = new Product;
        $this->productImage = new ProductImage;

        $created = $this->fillDataByFields($this->getStoreDraftAttributes());

        // Store images
        $imageData = [];
        if ($this->request->has('product_images') && sizeof($this->request->product_images) > 0) {
            foreach ($this->request->product_images as $imageFile) {
                $imageData[] = $this->storeProductImage($imageFile, $created->id);
            }
        }
        $this->productImage->insert($imageData);

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

        return Product::where('slug', $this->request->slug)
            ->where('user_id', $this->request->user()->id)
            ->select($this->getSelectProductAttr())
            ->with([
                'productImages' => function($q) {
                    $q->select($this->getSelectProductImagesAttr())->get();
                },
                'province',
                'district',
                'ward',
            ])
            ->first();
    }

    public function update($request)
    {
        $this->request = $request;
        $this->model = Product::find($request->product_id);
        $this->productImage = new ProductImage;

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
        $imageData = [];
        if ($this->request->has('product_images') && sizeof($this->request->product_images) > 0) {
            foreach ($this->request->product_images as $imageFile) {
                $imageData[] = $this->storeProductImage($imageFile, $this->model->id);
            }
        }
        $this->productImage->insert($imageData);

        $result = $this->fillDataByFields($this->getUpdateAttributes());
        return $this->getDetailById($result->id);
    }

    public function publicDraft($request)
    {
        $this->request = $request;
        $this->request->status = ProductStatusEnum::REALITY->value;
        $this->request->posted_at = Carbon::now()->toDateTimeString();
        $this->model = Product::find($request->product_id);
        $this->productImage = new ProductImage;

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
        $imageData = [];
        if ($this->request->has('product_images') && sizeof($this->request->product_images) > 0) {
            foreach ($this->request->product_images as $imageFile) {
                $imageData[] = $this->storeProductImage($imageFile, $this->model->id);
            }
        }
        $this->productImage->insert($imageData);

        $result = $this->fillDataByFields($this->getPublicDraftAttributes());
        $detail = $this->getDetailById($result->id);

        // Send notification to following finders
        $followers = $this->request->user()->followers()->get();
        foreach ($followers as $follower) {
            SendMailService::sendMail(
                $follower->email,
                NotiFollowerProviderCreateProductMail::class,
                $detail->title,
                $follower->full_name,
                $detail,
                $detail->user
            );
        }

        return $detail;
    }

    public function listByAuth($request)
    {
        $this->request = $request;
        $this->model = Product::class;

        $products = $this->model::select([
                'id',
                'title',
                'price',
                'slug',
                'status',
                'detail_address',
                DB::raw('COUNT(users_viewed_products.created_at) as user_viewed_product_count'),
            ])
            ->with([
                'productImages' => function($q) {
                    return $q->select($this->getSelectProductImagesAttr())
                        ->orderBy('id', 'asc')
                        ->get();
                },
            ])
            ->leftJoin('users_viewed_products', 'users_viewed_products.product_id', 'products.id')
            ->groupBy(['products.id'])
            ->orderBy('products.created_at', 'desc')
            ->where('status', $this->request->status)
            ->where('products.user_id', $this->request->user()->id ?? null)
            ->paginate(PaginateEnum::PAGINATE_10->value);

        return [
            'list' => $products,
            'draft_count' => Product::where('user_id', $this->request->user()->id ?? null)
                ->where('status', ProductStatusEnum::DRAFT->value)->count(),
            'total_count' => Product::where('user_id', $this->request->user()->id ?? null)
                ->where('status', ProductStatusEnum::REALITY->value)->count()
        ];
    }

    public function publicList($request)
    {
        $this->request = $request;
        $this->model = Product::class;

        return $this->model::select($this->getSelectPublicProductAttr())
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
            ->when($this->request->keyword != '', function($q) {
                $q->search($this->request->keyword);
            })
            ->when($this->request->province_id != '', function($q) {
                $q->where('province_id', $this->request->province_id);
            })
            ->when($this->request->district_id != '', function($q) {
                $q->where('district_id', $this->request->district_id);
            })
            ->when($this->request->ward_id != '', function($q) {
                $q->where('ward_id', $this->request->ward_id);
            })
            ->when($this->request->price_range != '', function($q) {
                $priceRange = explode(',', $this->request->price_range);
                $q->where('price', '>=', $priceRange[0]);
                $q->where('price', '<=', $priceRange[1]);
            })
            ->when($this->request->acreage != '', function($q) {
                $acreage = explode(',', $this->request->acreage);
                $q->where('acreage', '>=', $acreage[0]);
                $q->where('acreage', '<=', $acreage[1]);
            })
            ->when($this->request->used_type != '', function($q) {
                $usedType = explode(',', $this->request->used_type);
                $q->whereIn('used_type', $usedType);
            })
            ->when($this->request->bed_rooms != '', function($q) {
                $q->where('bed_rooms', $this->request->bed_rooms);
            })
            ->when($this->request->toilet_rooms != '', function($q) {
                $q->where('toilet_rooms', $this->request->toilet_rooms);
            })
            ->when($this->request->is_allow_pet != '', function($q) {
                $q->where('is_allow_pet', $this->request->is_allow_pet);
            })
            ->when($this->request->order_by != '', function($q) {
                $orderBy = explode('|', $this->request->order_by);
                $q->orderBy($orderBy[0], $orderBy[1]);
            })
            ->when($this->request->without_id != '', function($q) {
                $q->where('id', '<>', $this->request->without_id);
            })
            ->orderBy('posted_at', 'desc')
            ->where('status', ProductStatusEnum::REALITY->value)
            ->paginate($this->request->limit ?? PaginateEnum::PAGINATE_20->value);
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
    
    public function publicDetail($request)
    {
        $this->request = $request;
        $this->model = Product::class;

        $product = $this->model::select([
            'id',
            'slug',
            'title',
            'user_id',
            'price',
            'acreage',
            'tel',
            'detail_address',
            'is_shared_house',
            'used_type',
            'bed_rooms',
            'toilet_rooms',
            'time_rule',
            'is_allow_pet',
            'description',
            'lat',
            'long',
            'posted_at',
            'district_id',
            'province_id',
        ])
        ->where('slug', $this->request->slug)
        ->where('status', ProductStatusEnum::REALITY->value)
        ->with([
            'user' => function($q) {
                $q->select(['id', 'app_id', 'full_name', 'avatar', 'created_at']);
            },
            'productImages' => function($q) {
                $q->select(['id', 'product_id', 'url', 'thumb_url']);
            }
        ])
        ->first();

        // Store viewed users
        if ($product) {
            UserViewedProduct::updateOrCreate([
                'user_id' => $this->request->user()->id ?? null,
                'guest_ip' => $this->request->ip(),
                'product_id' => $product->id
            ]);
        }

        return $product;
    }

    public function publicProviderProducts($request)
    {
        $this->model = Product::class;
        $this->request = $request;

        return $this->model::select($this->getSelectPublicProductAttr())
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
            ->leftJoin('users', 'users.id', '=', 'products.user_id')
            ->where('users.app_id', $this->request->app_id)
            ->where('products.status', ProductStatusEnum::REALITY->value)
            ->with([
                'productImages' => function($q) {
                    $q->select(['id', 'product_id', 'thumb_url']);
                }
            ])
            ->orderBy('posted_at', 'DESC')
            ->paginate(PaginateEnum::PAGINATE_10->value);
    }

    public function productCountByPriceRange($min, $max)
    {
        $this->model = Product::class;
        return $this->model::where('price', '>=', $min)->where('price', '<=', $max)->count();
    }

    public function getPriceWithProductCount()
    {
        return [
            0 => [
                'label' => 'Dưới 1 triệu',
                'id' => '500000,1000000',
                'products_count' => $this->productCountByPriceRange(500000, 1000000),
            ],
            1 => [
                'label' => 'Từ 1 - 2 triệu',
                'id' => '1000000,2000000',
                'products_count' => $this->productCountByPriceRange(1000000, 2000000),
            ],
            2 => [
                'label' => 'Từ 2 - 4 triệu',
                'id' => '2000000,4000000',
                'products_count' => $this->productCountByPriceRange(2000000, 4000000),
            ],
            3 => [
                'label' => 'Từ 4 - 6 triệu',
                'id' => '4000000,6000000',
                'products_count' => $this->productCountByPriceRange(4000000, 6000000),
            ],
            4 => [
                'label' => 'Từ 6 - 8 triệu',
                'id' => '6000000,8000000',
                'products_count' => $this->productCountByPriceRange(6000000, 8000000),
            ],
            5 => [
                'label' => 'Từ 8 - 10 triệu',
                'id' => '8000000,10000000',
                'products_count' => $this->productCountByPriceRange(8000000, 10000000),
            ],
            6 => [
                'label' => 'Từ 10 - 12 triệu',
                'id' => '10000000,12000000',
                'products_count' => $this->productCountByPriceRange(10000000, 12000000),
            ],
            7 => [
                'label' => 'Trên 12 triệu',
                'id' => '12000000,20000000',
                'products_count' => $this->productCountByPriceRange(12000000, 20000000),
            ],
        ];
    }

    public function adminGetListProducts($request)
    {
        $this->model = Product::class;
        $this->request = $request;

        return $this->model::select([
            'products.id',
            'title',
            'price',
            'acreage',
            'bed_rooms',
            'toilet_rooms',
            'ward_id',
            'district_id',
            'province_id',
        ])
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
        ->withCount('userReport')
        ->when($this->request->keyword != '', function($q) {
            $q->where('title', 'like', "%{$this->request->keyword}%");
        })
        ->when($this->request->province_id != '', function($q) {
            $q->where('province_id', $this->request->province_id);
        })
        ->when($this->request->price_range != '', function($q) {
            $priceRange = explode(',', $this->request->price_range);
            $q->where('price', '>=', $priceRange[0]);
            $q->where('price', '<=', $priceRange[1]);
        })
        ->when($this->request->order_by != '', function($q) {
            $orderBy = explode('|', $this->request->order_by);
            $q->orderBy($orderBy[0], $orderBy[1]);
        })
        ->when($this->request->status != '', function($q) {
            $q->where('status', $this->request->status);
        })
        ->paginate($this->request->limit ?? PaginateEnum::PAGINATE_10->value);
    }
    
    public function adminGetDetailProduct($request)
    {
        $this->request = $request;
        $this->model = Product::class;

        return $this->model::select([
            'id',
            'slug',
            'title',
            'user_id',
            'price',
            'acreage',
            'tel',
            'detail_address',
            'is_shared_house',
            'used_type',
            'bed_rooms',
            'toilet_rooms',
            'time_rule',
            'is_allow_pet',
            'description',
            'lat',
            'long',
            'posted_at',
            'district_id',
            'province_id',
            'ward_id',
        ])
        ->where('id', $this->request->id)
        ->with([
            'user' => function($q) {
                $q->select(['id', 'app_id', 'full_name', 'avatar', 'created_at']);
            },
            'productImages' => function($q) {
                $q->select(['id', 'product_id', 'url', 'thumb_url']);
            },
            'province' => function($q) {
                $q->select('id', 'name');
            },
            'district' => function($q) {
                $q->select('id', 'name');
            },
            'ward' => function($q) {
                $q->select('id', 'name');
            },
        ])
        ->withCount('userReport')
        ->firstOrFail();
    }

    public function adminUpdateProductStatus($request)
    {
        $this->request = $request;
        $this->model = Product::where('id', $this->request->id)->firstOrFail();
        $this->model->status = $this->request->status;
        $this->model->save();

        // Case block
        if ($this->request->status == ProductStatusEnum::BLOCKED->value) {
            $productAuth = $this->model->user;
            dispatch(new SendNotiProductPolicyNgJob($this->model, $productAuth));
        }

        return $this->model;
    }
}
