<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Trả về HTTP 200 OK
     */
    protected function success(mixed $data = null, string $message = 'Thành công', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Trả về HTTP 201 Created
     */
    protected function created(mixed $data = null, string $message = 'Tạo mới thành công'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    /**
     * Trả về lỗi Validation (HTTP 422)
     */
    protected function validationError(mixed $errors, string $message = 'Dữ liệu không hợp lệ'): JsonResponse
    {
        return response()->json([
            'status' => 'fail',
            'message' => $message,
            'data' => $errors
        ], 422);
    }

    /**
     * Trả về lỗi Server/Client (HTTP 4xx, 5xx)
     */
    protected function error(string $message, int $statusCode = 500, mixed $details = null): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($details) {
            $response['details'] = $details;
        }

        return response()->json($response, $statusCode);
    }
}
