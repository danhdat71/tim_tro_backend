<?php

namespace Database\Seeders;

use App\Enums\UserGenderEnum;
use App\Enums\UserStatusEnum;
use App\Enums\UserTypeEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminData = [
            'full_name' => env('ADMIN_FULL_NAME'),
            'app_id' => date('YmdHis') . '-' . Str::slug(env('ADMIN_FULL_NAME')),
            'email' => env('ADMIN_EMAIL'),
            'tel' => env('ADMIN_TEL'),
            'user_type' => UserTypeEnum::ADMIN->value,
            'status' => UserStatusEnum::ACTIVE->value,
            'password' => Hash::make(env('ADMIN_PASSWORD', '123123123')),
        ];

        User::create($adminData);
    }
}
