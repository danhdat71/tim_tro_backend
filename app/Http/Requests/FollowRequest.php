<?php
namespace App\Http\Requests;

use App\Enums\SyncStatusEnum;
use App\Enums\UserTypeEnum;
use App\Models\User;

class FollowRequest extends BaseRequest
{
    public function rules(): array
    {
        $this->request = request();
        return [
            'action' => [
                'required',
                'in:' . implode(',', SyncStatusEnum::getKeys()),
            ],
            'follower_receive_id' => [
                'required',
                'exists:users,id',
                function ($attr, $value, $fail) {
                    $isExist = User::where('id', $value)
                        ->where('user_type', UserTypeEnum::PROVIDER->value)
                        ->exists();

                    if (!$isExist) {
                        return $fail(__('validation.exists'));
                    }
                }
            ],
        ];
    }
}
