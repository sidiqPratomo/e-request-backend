<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

interface ResponseService
{
    public static function init();

    public function response(array $data = [], string $message = 'success', int $statusCode = 200): JsonResponse;

    public function successResponse(array $data = [], string $message = 'success'): JsonResponse;

    public function errorResponse(array $errors = [], string $message = 'Error', int $statusCode = 400): JsonResponse;

    public function notFoundResponse(string $message = 'Resource not found'): JsonResponse;

    public function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse;

    public function forbiddenResponse(string $message = 'Forbidden'): JsonResponse;

    public function internalServerErrorResponse(string $message = 'Internal server error'): JsonResponse;
}
