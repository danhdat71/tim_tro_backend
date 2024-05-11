<?php

namespace App\Console\Commands;

use App\Enums\ProductStatusEnum;
use App\Enums\UserStatusEnum;
use App\Enums\UserTypeEnum;
use App\Jobs\NotiListNewProductsJob;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotiListNewProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:noti-list-new-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notification to finders list new products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $subject = 'Tổng hợp tin đăng tháng ' . $now->format('m');

        $listFinders = DB::table('users')->select(['id', 'full_name', 'avatar', 'email'])
            ->where('user_type', UserTypeEnum::FINDER->value)
            ->where('status', UserStatusEnum::ACTIVE)
            ->get();

        foreach ($listFinders as $finder) {
            // Get viewed province_id max count
            $listFinderViewedProvinceId = DB::table('users_viewed_products')
                ->selectRaw('products.province_id, COUNT(province_id) as viewed_province_num')
                ->join('products', 'products.id', '=', 'users_viewed_products.product_id')
                ->groupBy('province_id')
                ->orderBy('viewed_province_num', 'DESC')
                ->limit(2)
                ->pluck('province_id');
            
            $listNewProducts = DB::table('products')
                ->select([
                    'products.id',
                    'products.slug',
                    'products.title',
                    'products.price',
                    'products.posted_at',
                    'product_images.thumb_url',
                    'provinces.name as province_name',
                    'districts.name as district_name',
                    'wards.name as ward_name',
                ])
                ->leftJoin('product_images', 'product_images.product_id', '=', 'products.id')
                ->join('provinces', 'provinces.id', '=', 'products.province_id')
                ->join('districts', 'districts.id', '=', 'products.district_id')
                ->join('wards', 'wards.id', '=', 'products.ward_id')
                ->where('products.status', ProductStatusEnum::REALITY->value)
                ->whereIn('products.province_id', $listFinderViewedProvinceId)
                ->groupBy('products.id')
                ->orderBy('posted_at', 'DESC')
                ->limit(10)
                ->get()
                ->toArray();

            dispatch(new NotiListNewProductsJob($subject, $finder, $listNewProducts));
        }
    }
}
