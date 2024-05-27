<?php

namespace App\Http\Requests;

class LeaveSystemRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'leave_reason' => ['nullable', 'string', 'max:255'],
        ];
    }
}
