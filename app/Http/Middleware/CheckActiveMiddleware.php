<?php

namespace App\Http\Middleware;

use App\Enums\UserStatusEnum;
use App\Enums\UserTypeEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Traits\ResponseTrait;

class CheckActiveMiddleware
{
    use ResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user() && Auth::user()->status == UserStatusEnum::ACTIVE->value) {
            return $next($request);
        }

        return $this->responseMessageUnAuthorization("Tài khoản không tồn tại");
    }
}
