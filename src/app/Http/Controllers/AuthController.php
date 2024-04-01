<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * @param UserLoginRequest $request
     * @return JsonResponse
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        if (Auth::attempt($request->validated())) {
            return $this->successfulResponse([
                'token_type' => 'bearer',
                'token' => Auth::user()->createToken('appToken')->accessToken,
                'id' => Auth::user()->id,
            ]);
        }
        return $this->errorResponse('Authentication fail', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param UserRegisterRequest $request
     * @return JsonResponse
     */
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());
        return $this->successfulResponse([
            'token_type' => 'bearer',
            'token' => $user->createToken('appToken')->accessToken,
            'id' => $user->id,
        ]);
    }

    public function me()
    {
        return $this->successfulResponse(auth()->user());
    }
}
