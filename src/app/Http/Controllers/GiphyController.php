<?php

namespace App\Http\Controllers;

use App\Enums\GiphyApi;
use App\Facades\Giphy;
use App\Http\Requests\GiphyGifsRequest;
use App\Http\Requests\GiphySearchRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class GiphyController extends Controller
{
    use ApiResponse;

    /**
     * @param GiphySearchRequest $searchRequest
     * @return JsonResponse
     */
    public function search(GiphySearchRequest $searchRequest): JsonResponse
    {
        return $this->successfulResponse(
            Giphy::request($searchRequest->validated(), GiphyApi::search->value)
        );
    }

    /**
     * @param GiphyGifsRequest $gifsRequest
     * @return JsonResponse
     */
    public function gifs(GiphyGifsRequest $gifsRequest): JsonResponse
    {
        return $this->successfulResponse(
            Giphy::request($gifsRequest->validated(), GiphyApi::gifs->value)
        );
    }
}
