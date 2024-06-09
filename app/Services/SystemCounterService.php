<?php

namespace App\Services;

use App\Enums\ProductStatusEnum;
use App\Enums\UsedTypeEnum;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;

class SystemCounterService
{
    public const CACHE_KEY = 'system_counter';

    public function index()
    {
        return Cache::remember(self::CACHE_KEY, config('cache.system_count'), function() {
            return [
                'hostel_count' => Product::where('status', ProductStatusEnum::REALITY->value)->where('used_type', UsedTypeEnum::HOSTEL->value)->count(),
                'full_house_count' => Product::where('status', ProductStatusEnum::REALITY->value)->where('used_type', UsedTypeEnum::FULL_HOUSE->value)->count(),
                'apartment_count' => Product::where('status', ProductStatusEnum::REALITY->value)->where('used_type', UsedTypeEnum::APARTMENT->value)->count(),
                'together_count' => Product::where('status', ProductStatusEnum::REALITY->value)->where('used_type', UsedTypeEnum::TOGETHER->value)->count(),
            ];
        });
    }

    public function forgetCache()
    {
        Cache::forget(self::CACHE_KEY);
    }
}
