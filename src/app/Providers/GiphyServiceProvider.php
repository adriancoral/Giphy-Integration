<?php

namespace App\Providers;

use App\Services\GiphyConnect;
use Illuminate\Support\ServiceProvider;

class GiphyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('giphy', function () {
            return new GiphyConnect();
        });
    }
}
