<?php

namespace App\Http\Controllers;

use App\Services\LocationService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public LocationService $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function getProvinces(Request $request)
    {
        $result = $this->locationService->getProvinces($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function publicProvinces(Request $request)
    {
        $result = $this->locationService->publicProvinces($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function publicProvincesWithDistricts(Request $request)
    {
        $result = $this->locationService->provincesWithDistricts($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function getDistricts(Request $request)
    {
        $result = $this->locationService->getDistricts($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function getWards(Request $request)
    {
        $result = $this->locationService->getWards($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function publicWardsWithCountProducts(Request $request)
    {
        $result = $this->locationService->getWardsWithCountProducts($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function publicDistrictWithCountProducts(Request $request)
    {
        $result = $this->locationService->publicDistrictWithCountProducts($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }
}
