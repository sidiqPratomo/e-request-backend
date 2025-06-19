<?php

namespace App\Http\Middleware;

use App\Services\Impl\JsonResponseService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class CheckModelMiddleware
{
    protected $response;

    public function __construct(JsonResponseService $response)
    {
        $this->response = $response;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $collection = $request->segment(3);

        if (! $this->fileModelCheck($collection)) {
            return $this->response->notFoundResponse();
        }

        return $next($request);
    }

    private function fileModelCheck($collection): bool
    {
        if (Schema::hasTable($collection)) {
            return true;
        }

        return false;
    }
}
