<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait LogContext
{
    /**
     * Enrich the current logger context with request and user details.
     */
    protected function logWithContext(string $level, string $message, array $context = []): void
    {
        $request = request();

        $mergedContext = array_merge([
            'user_id' => auth()->id(),
            'ip' => $request?->ip(),
            'route_name' => $request?->route()?->getName(),
        ], $context);

        Log::log($level, $message, $mergedContext);
    }
}
