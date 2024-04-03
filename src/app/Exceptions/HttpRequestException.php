<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HttpRequestException extends Exception
{
    use ApiResponse;

    public function render(): JsonResponse
    {
        return $this->errorResponse($this->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
