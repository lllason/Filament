<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class HtmlPurifier extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'htmlpurifier';
    }
}