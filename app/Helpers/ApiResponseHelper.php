<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponseHelper
{
    /**
     * Generate a standardized API response.
     *
     * @param int $statusCode
     * @param mixed $data
     * @param string $message
     * @return JsonResponse
     */
    public static function apiResponse(int $statusCode, $data, string $message): JsonResponse
    {
        return response()->json([
            'statuscode' => $statusCode,
            'data' => $data,
            'message' => $message
        ], $statusCode);
    }
}
