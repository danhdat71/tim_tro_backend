<?php

namespace App\Traits;

trait NumberTrait
{
    public function generateOTP()
    {
        return str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }
}