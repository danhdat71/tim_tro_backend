<?php

namespace App\Services;

use App\Models\User;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserService
{
    public $model = null;
    public $request = null;

    public function fillDataByFields($fiels = [])
    {
        foreach ($fiels as $value) {
            if ($this->request->has($value) && $this->request->{$value} != '' && $this->request->{$value} != null) {
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
        $img->fit(config('image.user_avatar.width'), config('image.user_avatar.height'));
        $img->save($avatarFullPath, config('user_avatar.quality'));

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
}
