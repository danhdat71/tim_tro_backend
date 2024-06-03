<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    public ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getList(Request $request)
    {
        $result = $this->productService->adminGetListProducts($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }
}
