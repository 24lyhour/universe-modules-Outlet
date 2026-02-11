<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'as' => 'v1.', 'middleware' => ['auth:sanctum']], function () {
    require __DIR__ . '/api/v1.php';
});
