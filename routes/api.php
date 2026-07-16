<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\BuyerMessageController;

Route::prefix('buyer-message')->group(function () {
    Route::get('/box-buyers', [BuyerMessageController::class, 'boxBuyers']);
    Route::get('/next-message', [BuyerMessageController::class, 'nextMessage']);
    Route::post('/increment-buyer', [BuyerMessageController::class, 'incrementBuyer']);
    Route::post('/increment-user-count', [BuyerMessageController::class, 'incrementUserCount']);
});

Route::prefix('buyermessage')->group(function () {
    Route::get('/nextMessage', [BuyerMessageController::class, 'nextMessage']);
});

