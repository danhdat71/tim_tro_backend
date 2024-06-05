<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserProductService;
use Illuminate\Http\Request;

class AdminProductReportController extends Controller
{
    public UserProductService $userProductService;

    public function __construct(UserProductService $userProductService)
    {
        $this->userProductService = $userProductService;
    }

    public function getList(Request $request)
    {
        $result = $this->userProductService->adminGetListBugReport($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function getDetailProductReport(Request $request)
    {
        $result = $this->userProductService->adminGetDetailProductReport($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function getListReport(Request $request)
    {
        $result = $this->userProductService->adminGetListReport($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function getDetailReport(Request $request)
    {
        $result = $this->userProductService->adminGetDetailReport($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }
}
