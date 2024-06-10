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
        $result = $this->adsService->getPublicList();

        if ($result) {
            return $this->responseDataSuccess($result);
        }
        
        return $this->responseMessageBadrequest();
    }
}
