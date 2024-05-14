<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBugReportRequest;
use App\Services\BugReportService;
use Illuminate\Http\Request;

class BugReportController extends Controller
{
    public BugReportService $bugReportService;

    public function __construct(BugReportService $bugReportService)
    {
        $this->bugReportService = $bugReportService;
    }

    public function store(StoreBugReportRequest $request)
    {
        $result = $this->bugReportService->store($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function getList(Request $request)
    {
        $result = $this->bugReportService->getList($request);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }

    public function getDetail(Request $request)
    {
        $result = $this->bugReportService->getDetail($request->id);

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageNotfound();
    }

    public function status(Request $request)
    {
        $result = $this->bugReportService->status($request);

        if ($result) {
            return $this->responseMessageSuccess();
        }

        return $this->responseMessageBadrequest();
    }
}
