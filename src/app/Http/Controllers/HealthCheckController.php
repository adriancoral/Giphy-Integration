<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HealthCheckController extends Controller
{
    use ApiResponse;
    /**
     * @return JsonResponse
     */
    public function healthcheck(): JsonResponse
    {
        try {
            if (DB::connection()->getPdo()) {
                return $this->successfulResponse(['message' => 'ok']);
            }
            return $this->errorResponse(['message' => 'fail'], 500);
        } catch (Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()], 500);
        }
    }
}
