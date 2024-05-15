<?php

namespace App\Http\Middleware\LimitRequest;

use App\Models\BugReport;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class LimitBugReportRequest
{
    use ResponseTrait;

    private $request = null;
    private $limitReport = 5;

    public function handle(Request $request, Closure $next): Response
    {
        $this->request = $request;

        $count = BugReport::where('ip_address', $this->request->ip())
            ->where('email', $this->request->email)
            ->where('created_at', '>=', Carbon::now()->format('Y-m-d 00:00:00'))
            ->where('created_at', '<=', Carbon::now()->format('Y-m-d 23:59:59'))
            ->count();

        if ($count > $this->limitReport) {
            return $this->responseMessageBadrequest("Giới hạn báo cáo !");
        }

        return $next($request);
    }
}
