<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ExecutionTimeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);
        $duration = round((microtime(true) - $start) * 1000, 2); // ms
        $response->headers->set('X-Execution-Time', $duration . ' ms');
        return $response;
    }
}
