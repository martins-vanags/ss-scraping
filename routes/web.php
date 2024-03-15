<?php

use App\Spiders\CarSpider;
use Illuminate\Support\Facades\Route;
use RoachPHP\Roach;

Route::get('/dd', function () {
    Roach::startSpider(CarSpider::class);


});
