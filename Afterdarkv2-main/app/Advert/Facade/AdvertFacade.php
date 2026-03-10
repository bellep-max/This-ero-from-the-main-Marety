<?php

namespace App\Advert\Facade;

use Illuminate\Support\Facades\Facade;

class AdvertFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'advert';
    }
}
