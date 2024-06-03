<?php

namespace App\Http\Requests;
use App\Enums\ReportTypeEnum;

class UserReportProductRequest extends BaseRequest
{
    public function rules(): array
    {
        $this->request = request();
        $rules = [
            'user_id' => ['nullable', 'exists:users,id'],
            'product_id' => ['required', 'exists:products,id'],
            'full_name' => ['required', 'min:5', 'max:50'],
            'email' => [
                'required',
                'min:10',
                'max:100',
                'email',
                'exists:users,email',
            ],
            'tel' => [
                'required',
                'min:10',
                'max:50',
                'exists:users,tel',
            ],
            'report_type' => [
                'required',
                'in:' . implode(',', ReportTypeEnum::getKeys()),
            ],
            'description' => ['nullable', 'max:5000'],
        ];

        if ($this->request->report_type == ReportTypeEnum::OTHER->value) {
            $rules['description'] = ['required', 'max:5000'];
        }

        return $rules;
    }
}
