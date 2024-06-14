<?php

namespace App\Http\Controllers;

use App\Services\AdsService;
use Illuminate\Http\Request;

class AdsController extends Controller
{
    public AdsService $adsService;

    public function __construct(AdsService $adsService)
    {
        $this->adsService = $adsService;
    }

    public function getPublicList(Request $request)
    {
        $result = $this->adsService->getPublicList($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }
        
        return $this->responseMessageBadrequest();
    }

    public function create(Request $request)
    {
        $result = $this->adsService->create($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }
        
        return $this->responseMessageBadrequest();
    }

    public function click(Request $request)
    {
        $result = $this->adsService->click($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }
        
        return $this->responseMessageBadrequest();
    }

    public function updateStatus(Request $request)
    {
        $result = $this->adsService->updateStatus($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }
        
        return $this->responseMessageBadrequest();
    }
}
