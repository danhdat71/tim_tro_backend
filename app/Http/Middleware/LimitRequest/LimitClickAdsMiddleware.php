<?php

namespace App\Http\Middleware\LimitRequest;

use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\RateLimiter;

class LimitClickAdsMiddleware
{
    use ResponseTrait;

    private $maxAttemp = 3; // Limit click
    private $decay = 2000; // Wrong login waiting time (in seconds)
    private $request = null;

    public function getThrottleKey()
    {
        return $this->request->input('ads_id') . '.' . $this->request->ip() . '.' . $this->request->path();
    }

    public function handle(Request $request, Closure $next): Response
    {
        $this->request = $request;
        $key = $this->getThrottleKey();

        $executed = RateLimiter::attempt($key, $this->maxAttemp, function() {}, $this->decay);

        if (!$executed) {
            $seconds = RateLimiter::availableIn($key);
            return $this->responseMessageBadrequest("Vui lòng thử lại sau $seconds giây");
        }

        return $next($request);
    }
}
