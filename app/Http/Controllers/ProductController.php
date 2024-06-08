<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductDraftRequest;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\DeleteProductRequest;
use App\Http\Requests\PublicDraftRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\NotificationService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public ProductService $productService;
    public NotificationService $notificationService;

    public function __construct(
        ProductService $productService,
        NotificationService $notificationService
    ) {
        $this->productService = $productService;
        $this->notificationService = $notificationService;
    }

    public function store(CreateProductRequest $request)
    {
        if ($request->check) {
            return $this->responseMessageSuccess('Checked!');
        }

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

    public function publicDraft(PublicDraftRequest $request)
    {
        if ($request->check) {
            return $this->responseMessageSuccess('Checked!');
        }

        $result = $this->productService->publicDraft($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function update(UpdateProductRequest $request)
    {
        if ($request->check) {
            return $this->responseMessageSuccess('Checked!');
        }

        $result = $this->productService->update($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function listByAuth(Request $request)
    {
        $result = $this->productService->listByAuth($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function publicList(Request $request)
    {
        $result = $this->productService->publicList($request);

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

    public function publicDetail(Request $request)
    {
        $result = $this->productService->publicDetail($request);

        if ($result) {
            // Send notification to product provider
            if ($request->user()?->id != $result->user_id) {
                $viewedUser = $request->user();
                $notificationDesc = $viewedUser
                    ? "Thành viên {$viewedUser->full_name} vừa xem bài đăng của bạn"
                    : 'Vừa có thành viên ẩn danh xem bài đăng của bạn.';
                $this->notificationService->checkExistAndPush(
                    'Lượt xem bài đăng: ' . $result->title,
                    $notificationDesc,
                    $result->user_id
                );
            }
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageNotfound();
    }

    public function getPriceWithProductCount()
    {
        $result = $this->productService->getPriceWithProductCount();

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }
}
