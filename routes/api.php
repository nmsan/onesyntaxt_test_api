<?php

use Illuminate\Support\Facades\Route;

Route::get('/websites', [\App\Http\Controllers\WebsiteController::class, 'index']);
Route::post('/website', [\App\Http\Controllers\WebsiteController::class, 'store']);
Route::get('/website/{website_id}/posts', [\App\Http\Controllers\PostController::class, 'index']);
Route::post('/subscribe', [\App\Http\Controllers\SubscribeController::class, 'store']);
Route::post('/post/{website_id}', [\App\Http\Controllers\PostController::class, 'store']);
Route::put('/post/{post_id}', [\App\Http\Controllers\PostController::class, 'update']);
