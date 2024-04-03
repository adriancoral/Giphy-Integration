<?php

namespace App\Http\Controllers;

use App\Http\Requests\FavoriteAddRequest;
use App\Models\Favorite;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class FavoriteController extends Controller
{
    use ApiResponse;

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->successfulResponse(auth()->user()->favorites);
    }

    /**
     * @param FavoriteAddRequest $request
     * @return JsonResponse
     */
    public function add(FavoriteAddRequest $request): JsonResponse
    {
        $favorite = Favorite::updateOrCreate(
            ['user_id' => auth()->user()->id, 'gif_id' => $request->input('gif_id')],
            ['alias' => $request->input('alias')]
        );

        //$favorite = auth()->user()->favorites()->create($request->validated());
        return $this->successfulResponse($favorite);
    }
}
