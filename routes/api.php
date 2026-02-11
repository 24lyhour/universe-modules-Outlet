<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'outlets', 'as' => 'outlet.'], function () {
    require __DIR__ . '/api/v1.php';
});
