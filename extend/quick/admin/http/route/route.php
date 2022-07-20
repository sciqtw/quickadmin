<?php
declare (strict_types=1);


use quick\admin\http\middleware\DispatchAssetsQuickEvent;
use quick\admin\Quick;
use think\facade\Route;
use quick\admin\http\middleware\BootToolsMiddleware;
use quick\admin\http\middleware\AppModuleRunMiddleware;
use quick\admin\http\middleware\DispatchServingQuickEvent;

Route::get('captcha/[:config]', '\\think\\captcha\\CaptchaController@index');

Route::group('quick', function () {


    Route::get("style/<name>", function () {
        return Quick::loadStyles();
    });

    Route::get("script/<name>", function () {
        return Quick::loadScript();
    });

//    Route::get("index", '\quick\admin\http\controller\QuickController::index');


})->middleware([
//    ServeQuickMiddleware::class,
    DispatchServingQuickEvent::class,
    DispatchAssetsQuickEvent::class,
    AppModuleRunMiddleware::class,
]);



Route::group('resource', function () {


    Route::rule('<resource>/<action>/<func?>', '\quick\admin\http\controller\ResourceController::execute');

})->middleware(array_merge([

//    AdminAuthMiddleware::class,
    AppModuleRunMiddleware::class,
    BootToolsMiddleware::class,
    DispatchServingQuickEvent::class
],config('quick.middleware',[])));
