<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLoginMidleware
{
    /**
     * Handle an incoming request.
     *
     * This middleware was referenced in the Kernel but the class was missing.
     * To avoid a fatal ReflectionException, provide a minimal pass-through
     * implementation. Replace or extend this logic with your real login check.
     */
    public function handle(Request $request, Closure $next)
    {
        // TODO: implement actual login/check logic if needed.
        return $next($request);
    }
}
