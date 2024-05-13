<?php

namespace App\Http\Requests;

class StoreBugReportRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'min:5', 'max:50'],
            'email' => [
                'required',
                'min:10',
                'max:100',
                'email',
            ],
            'description' => ['required', 'string', 'max:5000'],
            'bug_report_images' => [
                'nullable',
                'array',
                'max:6',
            ],
            'bug_report_images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5000'],
        ];
    }
}
