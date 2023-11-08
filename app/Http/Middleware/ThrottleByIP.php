<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ThrottleByIP
{
    public function handle($request, Closure $next)
    {
        $maxAttempts = 1;
        $decaySeconds = 60;

        $key = 'throttle_' . $request->ip();

        if (app(RateLimiter::class)->tooManyAttempts($key, $maxAttempts)) {
            throw new HttpException(Response::HTTP_TOO_MANY_REQUESTS,'You have made too many requests. Please try again later.');
        }
        app(RateLimiter::class)->hit($key, $decaySeconds);

        return $next($request);
    }
}
