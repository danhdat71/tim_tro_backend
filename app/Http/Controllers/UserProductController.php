<?php

namespace App\Http\Controllers;

use App\Enums\SyncStatusEnum;
use App\Http\Requests\UserReportProductRequest;
use App\Http\Requests\UserSavedProductRequest;
use App\Mail\NotificationAdminCheckProductReport;
use App\Services\NotificationService;
use App\Services\ProductService;
use App\Services\SendMailService;
use App\Services\UserProductService;
use Illuminate\Http\Request;

class UserProductController extends Controller
{
    public UserProductService $userProductService;
    public SendMailService $sendMailService;
    public NotificationService $notificationService;
    public ProductService $productService;

    public function __construct(
        UserProductService $userProductService,
        SendMailService $sendMailService,
        NotificationService $notificationService,
        ProductService $productService
    ) {
        $this->userProductService = $userProductService;
        $this->sendMailService = $sendMailService;
        $this->notificationService = $notificationService;
        $this->productService = $productService;
    }

    public function saveProduct(UserSavedProductRequest $request)
    {
        $result = $this->userProductService->saveProduct($request);

        if ($result) {
            $request['is_all'] = true;
            $listSavedIds = $this->userProductService->listSavedProducts($request);
            $detailProduct = $this->productService->getDetailById($request->product_id);
            if ($request->action ==  SyncStatusEnum::ATTACH->value && $request->user()->id != $detailProduct->user_id) {
                $this->notificationService->checkExistAndPush(
                    "Lượt yêu thích bài đăng {$detailProduct->title}",
                    "Thành viên {$request->user()->full_name} vừa thêm bài đăng của bạn vào danh sách yêu thích.",
                    $detailProduct->user_id,
                    "/hostels/{$detailProduct->slug}",
                );
            }

            return $this->responseDataSuccess($listSavedIds);
        }

        return $this->responseMessageBadrequest();
    }

    public function reportProduct(UserReportProductRequest $request)
    {
        $result = $this->userProductService->reportProduct($request);

        if ($result) {
            // Send notification to admin
            $this->sendMailService->sendMail(
                env('ADMIN_EMAIL'),
                NotificationAdminCheckProductReport::class,
                $request->full_name,
                $request->email,
                $request->tel,
                $result->id, //product_id,
                $result->user_id, //product user_id,
                $result->title,
                $result->posted_at,
                $request->report_type,
                $request->description
            );

            return $this->responseMessageSuccess();
        }

        return $this->responseMessageBadrequest();
    }

    public function listSavedProducts(Request $request)
    {
        $result = $this->userProductService->listSavedProducts($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function listViewedProduct(Request $request)
    {
        $result = $this->userProductService->listViewedProduct($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }
}
