<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationException extends Exception
{
    use ApiResponse;

    public function render(): JsonResponse
    {
        return $this->errorResponse('unauthorized_user', Response::HTTP_UNAUTHORIZED);
    }
}
