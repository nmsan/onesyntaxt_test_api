<?php

use Illuminate\Support\Facades\Route;

Route::post('/subscribe', [\App\Http\Controllers\SubscribeController::class, 'store']);
Route::post('/post/{website_id}', [\App\Http\Controllers\PostController::class, 'store']);
Route::put('/post/{post_id}', [\App\Http\Controllers\PostController::class, 'update']);
