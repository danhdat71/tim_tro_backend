<?php

namespace App\Services;
use App\Enums\BoxLimitEnum;
use App\Enums\PaginateEnum;
use App\Models\District;
use App\Models\Product;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Support\Facades\Cache;

class LocationService
{
    public $province = null;
    public $district = null;
    public $ward = null;
    public $product = null;
    public $request = null;

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

    public function getSelectWard()
    {
        return [
            'id as value',
            'name as label',
            'district_id',
        ];
    }

    public function getProvinces($request)
    {
        $this->province = Province::class;
        $this->request = $request;

        return Cache::remember('provinces:' . implode(',', $this->request->all()), config('cache.location.province'), function () {
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
                ->when($this->request->limit != '', function($q) {
                    $q->limit($this->request->limit);
                })
                ->get();
        });
    }

    public function publicProvinces($request)
    {
        $this->province = Province::class;
        $this->request = $request;

        return Cache::remember(
            'public_provinces:' . implode(',', $this->request->all()),
            config('cache.public_location.province'),
            function () {
                return $this->province::select($this->getSelectProvince())
                    ->withCount('products')
                    ->when($this->request->limit != '', function($q) {
                        $q->limit($this->request->limit);
                    })
                    ->orderBy('products_count', 'desc')
                    ->get();
            }
        );
    }

    public function provincesWithDistricts($request)
    {
        $this->province = Province::class;
        $this->request = $request;

        return Cache::remember(
            'public_provinces_district_count:' . implode(',', $this->request->all()),
            config('cache.public_location.province'),
            function () {
                return $this->province::select($this->getSelectProvince())->with([
                        'districts' => function($q) {
                            $q->select(['id', 'province_id', 'name as label', 'id as value']);
                            $q->withCount('products');
                        }
                    ])
                    ->orderByRaw("
                        CASE id
                            WHEN 50 THEN 1
                            WHEN 1 THEN 2
                            WHEN 59 THEN 3
                            ELSE 4
                        END
                    ")
                    ->limit(BoxLimitEnum::PROVINCES->value)
                    ->get();
            }
        );
    }

    public function getDistricts($request)
    {
        $this->district = District::class;
        $this->request = $request;
        $provinceId = $this->request->province_id;
        
        return Cache::remember("districts?province_id=$provinceId", config('cache.location.district'), function() use($provinceId) {
            return $this->district::select($this->getSelectDistrict())
                ->where('province_id', $provinceId)
                ->orderBy('id', 'asc')
                ->get();
        });
    }

    public function getWards($request)
    {
        $this->ward = Ward::class;
        $this->request = $request;
        $districtId = $this->request->district_id;

        return Cache::remember("wards?district_id=$districtId", config('cache.location.ward'), function() use($districtId) {
            return $this->ward::select($this->getSelectWard())
                ->where('district_id', $districtId)
                ->orderBy('id', 'asc')
                ->get();
        });
    }

    public function getWardsWithCountProducts($request)
    {
        $this->province = Province::class;
        $this->district = District::class;
        $this->ward = Ward::class;
        $this->product = Product::class;
        $this->request = $request;

        $district = Cache::remember('cheaper_district:' . $this->request->district_id, config('cache.location.district'), function() {
            return $this->district::select(['id', 'name', 'province_id'])
                ->where('id', $this->request->district_id)
                ->first();
        });

        $province = Cache::remember('cheaper_province:' . $district->province_id, config('cache.location.province'), function() use($district) {
            return $this->province::select(['id', 'name'])
                ->where('id', $district->province_id)
                ->first();
        });

        $wards = Cache::remember('cheaper_ward:' . $this->request->district_id, config('cache.location.province'), function() {
            return $this->ward::select(['id', 'name'])
                ->where('district_id', $this->request->district_id)
                ->get();
        });

        $resultWards = collect([]);
        foreach($wards as $ward) {
            $cheaperProduct = $this->product::where('ward_id', $ward->id)
                ->where('price', '<=', $this->request->current_price - 100000)
                ->orderBy('price', 'DESC')
                ->first();

            if ($cheaperProduct) {
                $cheaperProductPrice = $cheaperProduct->price ?? 0;
                $count = $this->product::where('price', $cheaperProductPrice)->where('ward_id', $ward->id)->count();
                $ward->price = $cheaperProductPrice;
                $ward->count = $count;
                $resultWards->push($ward);
            }
        }

        return [
            'province' => $province,
            'district' => $district,
            'wards' => $resultWards,
        ];
    }

    public function publicDistrictWithCountProducts($request)
    {
        $this->district = District::class;
        $this->request = $request;

        return Cache::remember(
            'public_district:' . implode(',', $this->request->all()),
            config('cache.public_location.district'),
            function () {
                return $this->district::select($this->getSelectDistrict())
                    ->withCount('products')
                    ->orderBy('products_count', 'desc')
                    ->where('province_id', $this->request->province_id)
                    ->limit(PaginateEnum::PAGINATE_20->value)
                    ->get();
            }
        );
    }
}
