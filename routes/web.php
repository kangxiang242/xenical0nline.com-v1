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
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public API routes (no device redirect)
Route::get('/area/city', [AreaController::class, 'getCity']);
Route::get('/area/county', [AreaController::class, 'getCounty']);
Route::get('/area/road', [AreaController::class, 'getRoad']);
Route::get('/area/shop', [AreaController::class, 'getShop']);
Route::get('/robots.txt', [ApiController::class, 'robots']);
Route::get('/sitemap.xml', [ApiController::class, 'sitemap']);
Route::post('/observer/store', [ObserverController::class, 'store'])->middleware('throttle:120,1');

// Frontend routes with device redirect（與 official 標準模板一致）
Route::middleware(['redirect.device'])->group(function () {
    Route::get('/', [IndexController::class, 'index']);

    Route::any('/check', [OrderController::class, 'check']);
    Route::get('/check/{no}', [OrderController::class, 'checking']);

    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
    Route::get('/news/{uri}', [NewsController::class, 'index'])->where('uri', '[a-z-]+');
    Route::get('/news/{uri}/{id}', [NewsController::class, 'show'])->where('uri', '[a-z-]+')->where('id', '[0-9]+')->name('news.show');
    Route::get('/news/{id}', [NewsController::class, 'showById'])->where('id', '[0-9]+');

    Route::get('/product', [ProductController::class, 'index']);
    Route::get('/product/{id}', [ProductController::class, 'show']);

    // 瘦身計算機（與 official 一致）
    Route::get('/compute', [PageController::class, 'evaluate']);
    Route::post('/compute', [PageController::class, 'evaluate'])
        ->middleware('throttle:6,1');

    // 本站特有：BMI / BMR / 體脂計算器
    Route::get('/bmi', [PageController::class, 'bmi'])->name('bmi');
    Route::post('/bmi', [PageController::class, 'bmi'])->middleware('throttle:6,1');
    Route::get('/bmr', [PageController::class, 'bmr'])->name('bmr');
    Route::post('/bmr', [PageController::class, 'bmr'])->middleware('throttle:6,1');
    Route::redirect('/bodyfat', '/body-fat', 301);

    Route::get('/body-fat', [PageController::class, 'bodyFat'])->name('body-fat');
    Route::post('/body-fat', [PageController::class, 'bodyFat'])->middleware('throttle:6,1');

    Route::get('faq', [PageController::class, 'faq']);
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

    Route::get('/area/city', [AreaController::class, 'getCity']);
    Route::get('/area/county', [AreaController::class, 'getCounty']);
    Route::get('/area/road', [AreaController::class, 'getRoad']);
    Route::get('/area/shop', [AreaController::class, 'getShop']);
    Route::get('/get711', [AreaController::class, 'get711']);

    Route::post('/api/comment/up', [ProductController::class, 'commentUp'])->middleware('throttle:30,1');
});

// wangEditor5 图片上传
Route::post('/ami3-17drt4-6ne634russ/wang-editor/upload', [\App\Http\Controllers\Admin\WangEditorUploadController::class, 'upload'])
    ->middleware(['web', 'auth']);
