<?php

namespace App\Http\Requests;
use App\Enums\AllowPetEnum;
use App\Enums\SharedHouseEnum;
use App\Enums\TimeRuleEnum;
use App\Enums\UsedTypeEnum;
use App\Models\District;
use App\Models\Ward;

class CreateProductRequest extends BaseRequest
{
    public function rules(): array
    {
        $this->request = request();
        return [
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
            'toilet_rooms' => ['required', 'numeric', 'min:1', 'max:5'],
            'used_type' => ['required', 'in:' . implode(',', UsedTypeEnum::getKeys())],
            'is_shared_house' => ['required', 'in:' . implode(',', SharedHouseEnum::getKeys())],
            'time_rule' => ['required', 'in:' . implode(',', TimeRuleEnum::getKeys())],
            'is_allow_pet' => ['required', 'in:' . implode(',', AllowPetEnum::getKeys())],
            'product_images' => ['required'],
            'product_images.*' => ['image', 'mimes:jpg,jpeg,png'],
        ];
    }
}
