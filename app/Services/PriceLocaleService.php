<?php

namespace App\Services;

use App\Enums\PaginateEnum;
use App\Enums\ProductStatusEnum;
use App\Models\District;
use App\Models\Province;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PriceLocaleService
{
    public $province = null;
    public $district = null;
    public $request = null;

    public function index($request)
    {
        $result = collect();
        // Current month-year
        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');
        // Prvious month-year
        $previousMonth = Carbon::now()->subMonths(1)->format('m');
        $previousYear = Carbon::now()->subMonths(1)->format('Y');

        if ($request->province_id == '') {
            $provinces = $this->getProvinces($request);
            foreach ($provinces as $province) {
                $provinceId = $province->id;
    
                // Calculator current month
                $currentAvg = DB::table('products')->selectRaw('SUM(price) / SUM(acreage) as avg_acreage')
                    ->where('status', ProductStatusEnum::REALITY->value)
                    ->where('province_id', $provinceId)
                    ->whereMonth('posted_at', $currentMonth)
                    ->whereYear('posted_at', $currentYear)
                    ->first()->avg_acreage ?? 0;
    
                // Calculator previous month
                $previousAvg = DB::table('products')->selectRaw('SUM(price) / SUM(acreage) as avg_acreage')
                    ->where('status', ProductStatusEnum::REALITY->value)
                    ->where('province_id', $provinceId)
                    ->whereMonth('posted_at', $previousMonth)
                    ->whereYear('posted_at', $previousYear)
                    ->first()->avg_acreage ?? 0;
    
                $isGrowUp = $previousAvg < $currentAvg;
    
                if ($isGrowUp == true) {
                    $difference = $currentAvg - $previousAvg;
                } else {
                    $difference = $previousAvg - $currentAvg;
                }
    
                $itemData = [
                    'province_id' => $provinceId,
                    'province_label' => $province->label,
                    'current_avg' => number_format(round($currentAvg)),
                    'previous_avg' => number_format(round($previousAvg)),
                    'is_grow_up' => $isGrowUp,
                    'diffirence' => number_format(round($difference)),
                ];
    
                $result->push($itemData);
            }
        } else {

        }

        return $result;
    }

    public function getSelectProvince()
    {
        return [
            'id as value',
            'name as label',
            'id',
        ];
    }

    public function getSelectDistrict()
    {
        return [
            'id',
            'id as value',
            'name as label',
            'province_id',
        ];
    }

    public function getProvinces($request)
    {
        $this->province = Province::class;
        $this->request = $request;

        return Cache::remember('price_locale_provinces:' . implode(',', $this->request->all()), config('cache.location.province'), function () {
            return $this->province::select($this->getSelectProvince())
                ->orderByRaw("
                    CASE id
                        WHEN 50 THEN 1
                        WHEN 1 THEN 2
                        WHEN 59 THEN 3
                        WHEN 51 THEN 4
                        WHEN 52 THEN 5
                        WHEN 48 THEN 6
                        WHEN 32 THEN 7
                        WHEN 31 THEN 8
                        ELSE 9
                    END
                ")
                ->limit(1)
                ->get();
        });
    }

    public function getDistricts($request)
    {
        $this->district = District::class;
        $this->request = $request;
        $provinceId = $this->request->province_id;
        
        return Cache::remember("price_locale_districts?province_id=$provinceId", config('cache.location.district'), function() use($provinceId) {
            return $this->district::select($this->getSelectDistrict())
                ->where('province_id', $provinceId)
                ->orderBy('id', 'asc')
                ->get();
        });
    }
}
