<?php

namespace App\Http\Requests;
use App\Enums\AllowPetEnum;
use App\Enums\SharedHouseEnum;
use App\Enums\TimeRuleEnum;
use App\Enums\UsedTypeEnum;
use App\Models\District;
use App\Models\Ward;

class CreateProductDraftRequest extends BaseRequest
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
            'title' => ['required', 'max:100'],
            'price' => ['required', 'integer', 'max:20000000'],
            'description' => ['nullable', 'max:5000'],
            'tel' => ['required', 'digits_between:0,20'],
            'detail_address' => ['nullable', 'max:200'],
            'lat' => ['nullable', 'numeric'],
            'long' => ['nullable', 'numeric'],
            'acreage' => ['nullable', 'numeric', 'max:999'],
            'bed_rooms' => ['nullable', 'numeric', 'max:5'],
            'toilet_rooms' => ['nullable', 'numeric', 'max:5'],
            'used_type' => ['nullable', 'in:' . implode(',', UsedTypeEnum::getKeys())],
            'is_shared_house' => ['nullable', 'in:' . implode(',', SharedHouseEnum::getKeys())],
            'time_rule' => ['nullable', 'in:' . implode(',', TimeRuleEnum::getKeys())],
            'is_allow_pet' => ['nullable', 'in:' . implode(',', AllowPetEnum::getKeys())],
            'product_images' => ['nullable'],
            'product_images.*' => ['image', 'mimes:jpg,jpeg,png', 'max:5000'],
        ];
    }
}
