<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Auditrail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $log = [
            'username' => Auth::check() ? Auth()->user()->username : 'Guest',
            'ip' => $request->ip(),
            'method' => $request->method(),
            'uri' => $request->fullUrl(),
            'date' => now()->format('Y-m-d H:i:s'),
            'params' => json_encode($request->all()),
        ];

        $logContent = json_encode($log);

        $filename = storage_path('logs/auditrail-' . now()->format('Y-m-d')) . '.log';

        file_put_contents($filename, $logContent . PHP_EOL, FILE_APPEND);

        return $response;
    }
}
