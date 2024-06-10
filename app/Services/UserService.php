<?php

namespace App\Services;

use App\Enums\PaginateEnum;
use App\Enums\UserStatusEnum;
use App\Enums\UserTypeEnum;
use App\Models\User;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserService
{
    public $model = null;
    public $request = null;

    public function fillDataByFields($fiels = [])
    {
        foreach ($fiels as $value) {
            if ($this->request->has($value)) {
                if ($value == 'app_id') {
                    $this->model->{$value} = Str::slug($this->request->app_id);
                }
                else if ($value == 'password') {
                    $this->model->{$value} = Hash::make($this->request->password);
                }
                else {
                    $this->model->{$value} = $this->request->{$value};
                }
            }
        }

        $this->model->save();
        return $this->model;
    }

    public function updateUserInfoItemKeys()
    {
        return [
            'password',
            'app_id',
            'full_name',
            'tel',
            'gender',
            'birthday',
            'description',
        ];
    }

    public function getPublicProviderInfo()
    {
        return [
            'id',
            'app_id',
            'full_name',
            'avatar',
            'email',
            'tel',
            'gender',
            'birthday',
            'description',
        ];
    }

    public function getUser($userIdentifier)
    {
        if (filter_var($userIdentifier, FILTER_VALIDATE_EMAIL)) {
            $this->model = User::where('email', $userIdentifier)->first();
        } else {
            $this->model = User::where('tel', $userIdentifier)->first();
        }

        return $this->model;
    }

    public function setUserByAuth()
    {
        $this->model = $this->request->user();
    }

    public function mypageUserProvider($request)
    {
        $this->request = $request;
        $this->setUserByAuth();

        return $this->model;
    }

    public function publicProviderInfo($request)
    {
        $this->request = $request;
        $this->model = User::class;

        return $this->model::select($this->getPublicProviderInfo())
            ->where('app_id', $this->request->app_id)
            ->where('user_type', UserTypeEnum::PROVIDER->value)
            ->first();
    }

    public function updateUserAvatar($request)
    {
        $this->request = $request;
        $this->setUserByAuth();

        $avatarFolder = 'user_id_' . $this->model->id;
        $avatarFileName = 'avatar_' . date('ymdhis') . '.jpg';
        $userAvatarPath = "assets/imgs/$avatarFolder/";
        $avatarFullPath = $userAvatarPath . $avatarFileName;

        if (!Storage::disk('public_path')->exists($userAvatarPath)) {
            Storage::disk('public_path')->makeDirectory($userAvatarPath);
        }

        $file = $this->request->file('avatar');
        $img = Image::make($file);
        $img->orientate()->fit(config('image.user_avatar.width'), config('image.user_avatar.height'));
        $img->save($avatarFullPath, config('image.user_avatar.quality'));

        // Update data
        $this->model->avatar = $avatarFullPath;
        $this->model->save();

        return [
            'avatar' => $avatarFullPath,
        ];
    }

    public function updateUserItemInfo($request)
    {
        $this->request = $request;
        $this->setUserByAuth();

        return $this->fillDataByFields($this->updateUserInfoItemKeys());
    }

    public function adminGetListUsers($request)
    {
        $this->request = $request;
        $this->model = User::class;

        return $this->model::select([
            'id',
            'full_name',
            'avatar',
            'gender',
            'user_type',
            'status',
            'tel',
            'email',
            'created_at',
        ])
        ->when($this->request->keyword != "", function($q){
            $q->where('email', 'like', "%{$this->request->keyword}%");
            $q->orWhere('full_name', 'like', "%{$this->request->keyword}%");
            $q->orWhere('tel', 'like', "%{$this->request->keyword}%");
        })
        ->when($this->request->status != "", function($q){
            $q->where('status', $this->request->status);
        })
        ->when($this->request->user_type != "", function($q){
            $q->where('user_type', $this->request->user_type);
        })
        ->when($this->request->order_by != "", function($q){
            $orderByArr = explode('|', $this->request->order_by);
            $q->orderBy($orderByArr[0], $orderByArr[1]);
        })
        ->where('user_type', '<>', UserTypeEnum::ADMIN->value)
        ->paginate(PaginateEnum::PAGINATE_10->value);
    }

    public function adminGetDetailUser($request)
    {
        $this->request = $request;
        $this->model = User::class;

        return $this->model::select([
                'id',
                'full_name',
                'avatar',
                'app_id',
                'email',
                'tel',
                'gender',
                'user_type',
                'birthday',
                'description',
                'status',
                'leave_reason',
                'leave_at',
                'last_login_at',
                'created_at',
            ])
            ->where('id', $this->request->id)
            ->first();
    }

    public function adminUpdateUserStatus($request)
    {
        $this->request = $request;
        $this->model = User::where('id', $this->request->id)->firstOrFail();
        $this->model->status = $this->request->status;

        // Case leave
        if ($this->request->status == UserStatusEnum::LEAVE->value) {
            $this->model->leave_reason = $this->request->reason;
            $this->model->status_reason = null;
        }
        // Case active
        else if ($this->request->status == UserStatusEnum::ACTIVE->value) {
            $this->model->leave_reason = null;
            $this->model->status_reason = null;
        }
        // Case block
        else if ($this->request->status == UserStatusEnum::BLOCKED->value) {
            $this->model->leave_reason = null;
            $this->model->status_reason = $this->request->reason;
        }
        $this->model->save();

        // Logout user
        $isKickLogout = in_array($this->request->status, [
            UserStatusEnum::BLOCKED->value,
            UserStatusEnum::LEAVE->value,
        ]);
        if ( $isKickLogout ) {
            $this->model->tokens()->delete();
        }

        return $this->model;
    }
}
