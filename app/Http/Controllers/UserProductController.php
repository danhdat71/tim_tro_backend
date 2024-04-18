<?php

namespace App\Http\Controllers;

use App\Services\UserProductService;
use Illuminate\Http\Request;

class UserProductController extends Controller
{
    public UserProductService $userProductService;

    public function __construct(UserProductService $userProductService)
    {
        $this->userProductService = $userProductService;
    }

    public function saveProduct(Request $request)
    {
        $result = $this->userProductService->saveProduct($request);

        if ($result) {
            return $this->responseMessageSuccess();
        }

        return $this->responseMessageBadrequest();
    }
}
