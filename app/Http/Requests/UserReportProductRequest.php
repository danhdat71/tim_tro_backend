<?php

namespace App\Http\Requests;
use App\Enums\ReportTypeEnum;

class UserReportProductRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'exists:users,id'],
            'product_id' => ['required', 'exists:products,id'],
            'full_name' => ['required', 'min:5', 'max:50'],
            'email' => [
                'required',
                'min:10',
                'max:100',
                'email',
            ],
            'tel' => [
                'required',
                'min:10',
                'max:50',
                'unique:users,tel',
            ],
            'report_type' => [
                'required',
                'in:' . implode(',', ReportTypeEnum::getKeys()),
            ],
            'description' => ['required', 'max:5000'],
        ];
    }
}