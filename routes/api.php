<?php

use Illuminate\Support\Facades\Route;

Route::post('/subscribe', [\App\Http\Controllers\SubscribeController::class, 'store']);
