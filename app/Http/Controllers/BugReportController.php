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
            return $this->responseMessageSuccess();
        }

        return $this->responseMessageBadrequest();
    }

    public function getList()
    {
        
    }
}
