<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * @param $data
     * @param array $headers
     * @param int $options
     * @return JsonResponse
     */
    protected function successfulResponse($data, array $headers = [], int $options = 0): JsonResponse
    {
        return response()->json(['success' => true, 'payload' => $data], 200, $headers, $options);
    }

    /**
     * @param $message
     * @param $code
     * @return JsonResponse
     */
    protected function errorResponse($message, $code): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message], $code);
    }
}
