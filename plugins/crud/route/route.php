<?php

use think\facade\Route;



Route::group("api",function (){
    Route::any(":controller/:action","api.:controller/:action");
})->middleware([]);

Route::group("admin",function (){
    Route::any(":controller/:action","admin.:controller/:action");
})->middleware([]);