<?php 

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait {
    protected function successResponse($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
            'errors'  => null
        ], $statusCode);
    }

    protected function errorResponse(string $message = 'Error Occurred', int $statusCode = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'data'    => null,
            'errors'  => $errors
        ], $statusCode);
    }

    protected function validationErrorResponse($errors, string $message = 'Validation Errors'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }

    protected function unauthorizedResponse(string $message = 'Unauthorized access'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }
}