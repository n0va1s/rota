<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TraceIdMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $traceId = $request->header('X-Trace-ID') ?? (string) Str::uuid();

        Log::withContext([
            'trace_id' => $traceId,
        ]);

        $response = $next($request);
        $response->headers->set('X-Trace-ID', $traceId);

        return $response;
    }
}
