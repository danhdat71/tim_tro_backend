<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public $model = null;

    public function getUser($userIdentifier)
    {
        if (filter_var($userIdentifier, FILTER_VALIDATE_EMAIL)) {
            $this->model = User::where('email', $userIdentifier)->first();
        } else {
            $this->model = User::where('tel', $userIdentifier)->first();
        }

        return $this->model;
    }
}