<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserReportProductRequest;
use App\Http\Requests\UserSavedProductRequest;
use App\Mail\NotificationAdminCheckProductReport;
use App\Services\SendMailService;
use App\Services\UserProductService;
use Illuminate\Http\Request;

class UserProductController extends Controller
{
    public UserProductService $userProductService;
    public SendMailService $sendMailService;

    public function __construct(
        UserProductService $userProductService,
        SendMailService $sendMailService
    ) {
        $this->userProductService = $userProductService;
        $this->sendMailService = $sendMailService;
    }

    public function saveProduct(UserSavedProductRequest $request)
    {
        $result = $this->userProductService->saveProduct($request);

        if ($result) {
            return $this->responseMessageSuccess();
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
}
