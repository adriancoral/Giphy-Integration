<?php

namespace App\Enums;

enum GiphyApi: string
{
    case search = 'http://api.giphy.com/v1/gifs/search';
    case gifs = 'http://api.giphy.com/v1/gifs';
}
