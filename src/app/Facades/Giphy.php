<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool request(array $request, string $endpoint)
 *
 * @see GiphyConnect
 */
class Giphy extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'giphy';
    }
}
