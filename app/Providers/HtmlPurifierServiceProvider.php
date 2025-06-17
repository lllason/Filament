<?php
namespace App\Providers; // 或你自己包的 namespace

use Illuminate\Support\ServiceProvider;
use HTMLPurifier;
use HTMLPurifier_Config;

class HtmlPurifierServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('htmlpurifier', function ($app) {
            $config = HTMLPurifier_Config::createDefault();
            // 你可以在這裡調整設定，例如允許哪些標籤、屬性等
            return new HTMLPurifier($config);
        });
    }
}