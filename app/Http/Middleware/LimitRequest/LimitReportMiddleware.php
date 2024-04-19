<?php

namespace App\Http\Middleware\LimitRequest;

use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\RateLimiter;

class LimitReportMiddleware
{
    use ResponseTrait;

    private $maxAttemp = 1; // Limit report
    private $decay = 172800; // 2 days, Wrong login waiting time (in seconds)
    private $request = null;

    public function getThrottleKey()
    {
        return $this->request->input('product_id') . '.' . $this->request->ip() . '.' . $this->request->path();
    }

    public function handle(Request $request, Closure $next): Response
    {
        $this->request = $request;
        $key = $this->getThrottleKey();

        $executed = RateLimiter::attempt($key, $this->maxAttemp, function() {}, $this->decay);

        if (!$executed) {
            return $this->responseMessageBadrequest("Bạn đã báo cáo bài viết này rồi !");
        }

        return $next($request);
    }
}
