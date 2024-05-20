<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PriceLocaleService;

class PriceLocaleController extends Controller
{
    public PriceLocaleService $priceLocaleService;

    public function __construct(PriceLocaleService $priceLocaleService)
    {
        $this->priceLocaleService = $priceLocaleService;
    }

    public function index(Request $request)
    {
        $result = $this->priceLocaleService->index($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }
}
