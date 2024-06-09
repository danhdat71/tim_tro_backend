<?php

namespace App\Http\Controllers;

use App\Services\SystemCounterService;
use Illuminate\Http\Request;

class PublicCounterController extends Controller
{
    public SystemCounterService $systemCounterService;

    public function __construct(SystemCounterService $systemCounterService)
    {
        $this->systemCounterService = $systemCounterService;
    }

    public function index()
    {
        $result = $this->systemCounterService->index();

        if ($result) {
            return $this->responseDataSuccess($result);
        }

        return $this->responseMessageBadrequest();
    }
}
