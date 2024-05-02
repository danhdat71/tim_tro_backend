<?php

namespace App\Http\Middleware\LimitRequest;

use App\Models\UserReportProduct;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\RateLimiter;

class LimitReportMiddleware
{
    use ResponseTrait;

    private $request = null;

    public function handle(Request $request, Closure $next): Response
    {
        $this->request = $request;

        $count = UserReportProduct::where('product_id', $this->request->product_id)
            ->where('email', $this->request->email)
            ->where('tel', $this->request->tel)
            ->count();

        if ($count > 0) {
            return $this->responseMessageBadrequest("Bạn đã báo cáo bài viết này rồi !");
        }

        return $next($request);
    }
}
