<?php

use App\Http\Controllers\Web\ApiController;
use App\Http\Controllers\Web\AreaController;
use App\Http\Controllers\Web\IndexController;
use App\Http\Controllers\Web\MessageController;
use App\Http\Controllers\Web\NewsController;
use App\Http\Controllers\Web\ObserverController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (converted from old site, FQCN)
|--------------------------------------------------------------------------
*/

// Public API routes (no device redirect)
Route::get('/area/city', [AreaController::class, 'getCity']);
Route::get('/area/county', [AreaController::class, 'getCounty']);
Route::get('/area/road', [AreaController::class, 'getRoad']);
Route::get('/area/shop', [AreaController::class, 'getShop']);
Route::get('/robots.txt', [ApiController::class, 'robots']);
Route::get('/sitemap.xml', [ApiController::class, 'sitemap']);
Route::post('/observer/store', [ObserverController::class, 'store']);
Route::get('/area/city', [AreaController::class, 'getCity']);
Route::get('/area/county', [AreaController::class, 'getCounty']);
Route::get('/area/road', [AreaController::class, 'getRoad']);
Route::get('/area/shop', [AreaController::class, 'getShop']);
Route::get('/get711', [AreaController::class, 'get711']);

// Frontend routes with device redirect
Route::middleware(['redirect.device'])->group(function () {
    Route::get('/', [IndexController::class, 'index']);
    Route::any('/check', [OrderController::class, 'check']);
    Route::get('/check/{no}', [OrderController::class, 'checking']);
    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{uri}', [NewsController::class, 'index']);
    Route::get('/news/{uri}/{id}', [NewsController::class, 'show']);
    Route::get('/news/{id}', [NewsController::class, 'showById']);
    Route::get('/product', [ProductController::class, 'index']);
    Route::get('/product/{id}', [ProductController::class, 'show']);
    Route::get('/bmi', [PageController::class, 'evaluate']);
    Route::post('/bmi', [PageController::class, 'evaluate']);
    Route::get('/bmr', [PageController::class, 'bmr']);
    Route::post('/bmr', [PageController::class, 'bmr']);
    Route::get('/body-fat', [PageController::class, 'bodyFat']);
    Route::post('/body-fat', [PageController::class, 'bodyFat']);
    Route::get('about', [PageController::class, 'about']);
    Route::get('guide', [PageController::class, 'guide']);
    Route::get('payment-delivery', [PageController::class, 'paymentDelivery']);
    Route::get('after-sales', [PageController::class, 'afterSales']);
    Route::get('privacy', [PageController::class, 'privacy']);
    Route::get('/checkout/{id}', [OrderController::class, 'checkout']);
    Route::post('/order', [OrderController::class, 'store']);
    Route::get('/message', [MessageController::class, 'index']);
    Route::post('/message', [MessageController::class, 'store']);
    Route::get('/area', [AreaController::class, 'get']);
    Route::post('/api/comment/up', [ProductController::class, 'commentUp']);
});
