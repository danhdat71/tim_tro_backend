<?php

namespace App\Services;
use App\Models\District;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Support\Facades\Cache;

class LocationService
{
    public $province = null;
    public $district = null;
    public $ward = null;
    public $request = null;

    public function getSelectProvince()
    {
        return [
            'id as value',
            'name as label',
        ];
    }

    public function getSelectDistrict()
    {
        return [
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

    public function getProvinces()
    {
        $this->province = Province::class;

        return Cache::remember('provinces', config('cache.location.province'), function () {
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
                ->get();
        });
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
}
