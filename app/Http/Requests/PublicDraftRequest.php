<?php

namespace App\Http\Requests;

use App\Enums\AllowPetEnum;
use App\Enums\SharedHouseEnum;
use App\Enums\TimeRuleEnum;
use App\Enums\UsedTypeEnum;
use App\Models\District;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Ward;

class PublicDraftRequest extends BaseRequest
{
    public function rules(): array
    {
        $this->request = request();
        $limitCheck = function($attr, $value, $fail) {
            $maxImages = config('image.product.max_images');
            $productImageNum = ProductImage::where('product_id', $this->request->product_id)->count();
            $delImagesNum = $this->request->del_product_images
                ? count(explode(',',  $this->request->del_product_images))
                : 0;
            $leftOver = $productImageNum - $delImagesNum;
            $newImagesCount = $this->request->product_images
                ? count($this->request->product_images)
                : 0;

            if ($leftOver + $newImagesCount > $maxImages) {
                return $fail("Chỉ được phép đăng tối đa $maxImages ảnh.");
            }
        };
        $rules = [
            'product_id' => [
                'required',
                'exists:products,id',
                function($attr, $value, $fail) {
                    $product = Product::find($this->request->product_id);

                    if ($product && $product->user_id != $this->request->user()->id) {
                        return $fail(__('validation.exists'));
                    }
                }
            ],
            'province_id' => [
                'required',
                'exists:provinces,id',
            ],
            'district_id' => [
                'required',
                'exists:districts,id',
                function($attr, $value, $fail) {
                    $provinceId = $this->request->province_id;
                    $district = District::find($this->request->district_id);
                    if ($district->province_id != $provinceId) {
                        return $fail(__('validation.not_correct'));
                    }
                }
            ],
            'ward_id' => [
                'required',
                'exists:wards,id',
                function($attr, $value, $fail) {
                    $districtId = $this->request->district_id;
                    $ward = Ward::find($this->request->ward_id);
                    if ($ward->district_id != $districtId) {
                        return $fail(__('validation.not_correct'));
                    }
                }
            ],
            'title' => ['required', 'min:10', 'max:50',],
            'price' => ['required', 'integer', 'min:500000', 'max:20000000'],
            'description' => ['required', 'max:5000'],
            'tel' => ['required', 'digits_between:10,20'],
            'detail_address' => ['required', 'min:20', 'max:200'],
            'lat' => ['required', 'numeric'],
            'long' => ['required', 'numeric'],
            'acreage' => ['required', 'numeric', 'min:5', 'max:999'],
            'bed_rooms' => ['required', 'numeric', 'min:1', 'max:5'],
            'toilet_rooms' => ['required', 'numeric', 'min:0', 'max:5'],
            'used_type' => ['required', 'in:' . implode(',', UsedTypeEnum::getKeys())],
            'is_shared_house' => ['required', 'in:' . implode(',', SharedHouseEnum::getKeys())],
            'time_rule' => ['required', 'in:' . implode(',', TimeRuleEnum::getKeys())],
            'is_allow_pet' => ['required', 'in:' . implode(',', AllowPetEnum::getKeys())],
            'del_product_images' => ['nullable'],
            'product_images' => [
                'nullable',
                $limitCheck
            ],
            'product_images.*' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:5000',
            ],
        ];

        // Handle required images, not allow delete all
        if ($this->request->product_images == '' || !$this->request->has('product_images')) {
            if ($this->request->del_product_images != '' && $this->request->has('del_product_images')) {
                $productImageNum = ProductImage::where('product_id', $this->request->product_id)->count();
                $delNum = ProductImage::whereIn('id', explode(',', $this->request->del_product_images))->count();

                if ($delNum >= $productImageNum) {
                    $rules['product_images'] = ['required'];
                }
            } else {
                $productImageNum = ProductImage::where('product_id', $this->request->product_id)->count();
                if ($productImageNum == 0) {
                    $rules['product_images'] = ['required'];
                }
            }
        }

        return $rules;
    }
}
