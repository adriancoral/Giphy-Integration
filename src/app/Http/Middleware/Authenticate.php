<?php

namespace App\Http\Middleware;

use App\Exceptions\AuthorizationException;
use App\Traits\ApiResponse;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    use ApiResponse;

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     * @param Request $request
     * @return null
     * @throws AuthorizationException
     */
    protected function redirectTo(Request $request): null
    {
        throw new AuthorizationException();
    }
}
