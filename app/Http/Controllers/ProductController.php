<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductDraftRequest;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\DeleteProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public ProductService $productService;

    public function __construct(
        ProductService $productService
    ) {
        $this->productService = $productService;
    }

    public function store(CreateProductRequest $request)
    {
        $result = $this->productService->store($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }
    
    public function storeDraft(CreateProductDraftRequest $request)
    {
        $result = $this->productService->storeDraft($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function update(UpdateProductRequest $request)
    {
        $result = $this->productService->update($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function list(Request $request)
    {
        $result = $this->productService->list($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function detail(Request $request)
    {
        $result = $this->productService->getDetailByAuth($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageNotfound();
    }

    public function delete(DeleteProductRequest $request)
    {
        $result = $this->productService->delete($request);

        if ($result) {
            return $this->responseMessageSuccess();
        }

        return $this->responseMessageBadrequest();
    }
}
