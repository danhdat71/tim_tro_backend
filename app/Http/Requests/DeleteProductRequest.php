<?php

namespace App\Http\Requests;
use App\Models\Product;

class DeleteProductRequest extends BaseRequest
{
    public function rules(): array
    {
        $this->request = request();
        return [
            'product_id' => [
                'required',
                'exists:products,id',
                function($attr, $value, $fail) {
                    $product = Product::find($this->request->product_id);
                    
                    if ($product && $product->user_id != $this->request->user()->id) {
                        return $fail(__('validation.exists'));
                    }
                }
            ]
        ];
    }
}
