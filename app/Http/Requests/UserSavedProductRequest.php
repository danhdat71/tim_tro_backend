<?php

namespace App\Http\Requests;
use App\Enums\ProductStatusEnum;
use App\Enums\SyncStatusEnum;
use App\Models\Product;

class UserSavedProductRequest extends BaseRequest
{
    public function rules(): array
    {
        $this->request = request();
        return [
            'product_id' => [
                'required',
                function($attr, $value, $fail) {
                    $isExist = Product::where('id', $value)
                        ->where('status', ProductStatusEnum::REALITY->value)
                        ->exists();

                    if (!$isExist) {
                        return $fail(__('validation.exists'));
                    }
                }
            ],
            'action' => [
                'required',
                'in:' . implode(',', SyncStatusEnum::getKeys()),
            ],
        ];
    }
}
