<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\NewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('news', NewsController::class);
    Route::apiResource('comments', CommentController::class)->only(['store', 'destroy']);
    Route::post('likes', 'LikeController@store');
    Route::delete('likes', 'LikeController@destroy');
});

//Route::post('/webhook/upay', [UpayWebhookController::class, 'handle']);

