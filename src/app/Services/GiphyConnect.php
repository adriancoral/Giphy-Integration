<?php

namespace App\Services;

use App\Exceptions\HttpRequestException;
use Exception;
use Illuminate\Support\Facades\Http;

class GiphyConnect
{
    /**
     * @param array $request
     * @param string $endpoint
     * @return array|mixed
     * @throws Exception
     */
    public function request(array $request, string $endpoint): mixed
    {
        try {
            return Http::get(
                $endpoint,
                collect(['api_key' => config('giphy.api_key')])
                    ->merge($request)->all()
            )->throw()->json();
        } catch (\Exception $exception) {
            throw new HttpRequestException($exception->getMessage());
        }

    }
}
