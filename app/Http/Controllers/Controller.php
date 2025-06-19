<?php

namespace App\Http\Controllers;

use App\Services\ResponseService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    protected $response;

    public function __construct(ResponseService $response)
    {
        $this->response = $response;
    }

    public function validatorErrors($validators): JsonResponse
    {
        return $this
            ->response
            ->errorResponse(
                $validators->errors()->toArray(),
                $validators->errors()->first()
            );
    }

    public function generalError(Exception $e): JsonResponse
    {
        return $this
            ->response
            ->errorResponse(
                [],
                $e->getMessage(),
                500
            );
    }
}
