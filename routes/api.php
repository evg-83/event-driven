<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\NewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('news', NewsController::class);
    Route::apiResource('comments', CommentController::class);
//    Route::apiResource('comments', CommentController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::post('likes', [LikeController::class, 'store']);
    Route::delete('likes', [LikeController::class, 'destroy']);

    Route::post('logout', [AuthController::class, 'logout']);
});

//Route::post('/webhook/upay', [UpayWebhookController::class, 'handle']);

