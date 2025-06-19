<?php

namespace App\Services\Impl;

use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;

class JsonResponseService implements ResponseService
{
    protected $response;

    public function __construct()
    {
        $this->response = [
            'status' => true,
            'data' => [],
            'message' => 'success',
            'code' => 200,
        ];
    }

    public static function init()
    {
        return new self();
    }

    public function response(array $data = [], string $message = 'success', int $statusCode = 200): JsonResponse
    {
        $this->setData($data);
        $this->setMessage($message);

        return response()->json($this->response, $this->response['code']);
    }

    public function successResponse(array $data = [], string $message = 'success'): JsonResponse
    {
        $this->setData($data);
        $this->setMessage($message);

        return response()->json($this->response, $this->response['code']);
    }

    public function errorResponse(array $errors = [], string $message = 'Error', int $statusCode = 400): JsonResponse
    {
        $this->setStatusCode($statusCode);
        $this->setMessage($message);
        $this->setErrors($errors);

        return response()->json($this->response, $this->response['code']);
    }

    public function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        $this->setStatusCode(404);
        $this->setMessage($message);

        return response()->json($this->response, $this->response['code']);
    }

    public function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        $this->setStatusCode(401);
        $this->setMessage($message);

        return response()->json($this->response, $this->response['code']);
    }

    public function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        $this->setStatusCode(403);
        $this->setMessage($message);

        return response()->json($this->response, $this->response['code']);
    }

    public function internalServerErrorResponse(string $message = 'Internal server error'): JsonResponse
    {
        $this->setStatusCode(500);
        $this->setMessage($message);

        return response()->json($this->response, $this->response['code']);
    }

    private function setData($data)
    {
        $this->response['data'] = $data;
    }

    private function setMessage($message)
    {
        $this->response['message'] = $message;
    }

    private function setStatusCode($code)
    {
        $this->response['code'] = $code;
    }

    private function setErrors($errors)
    {
        $this->response['errors'] = $errors;
    }

    private function setStatus($status)
    {
        $this->response['status'] = $status;
    }
}
