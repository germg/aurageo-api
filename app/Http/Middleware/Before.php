<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class Before
{
    public function handle($request, Closure $next)
    {
        if ($request->getMethod() == "OPTIONS") {
            return response(Response::HTTP_OK);
        }

        return $next($request);
    }
}