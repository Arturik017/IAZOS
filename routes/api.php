<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SellerController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\SellerFollowController;
use App\Http\Controllers\Api\StoryController;
use App\Http\Controllers\Api\StoryInteractionController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\FinanceController;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Guest
    |--------------------------------------------------------------------------
    */
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::get('/catalog/tree', [CatalogController::class, 'tree']);
    Route::get('/catalog/categories/{category:slug}', [CatalogController::class, 'category']);
    Route::get('/catalog/categories/{category:slug}/filters', [CatalogController::class, 'filters']);
    Route::get('/catalog/brands', [CatalogController::class, 'brands']);
    Route::get('/catalog/brands/{brand}/products', [CatalogController::class, 'brandProducts']);

    Route::get('/sellers', [SellerController::class, 'index']);
    Route::get('/sellers/{user}', [SellerController::class, 'show']);
    Route::get('/stories', [StoryController::class, 'index']);
    Route::get('/sellers/{user}/stories', [StoryController::class, 'sellerStories']);

    /*
    |--------------------------------------------------------------------------
    | Cart (merge și guest)
    |--------------------------------------------------------------------------
    */
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::post('/cart/update', [CartController::class, 'update']);
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);

    /*
    |--------------------------------------------------------------------------
    | Authenticated user
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [ProfileController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/checkout', [CheckoutController::class, 'store']);
        Route::get('/messages', [MessageController::class, 'index']);
        Route::get('/messages/{conversation}', [MessageController::class, 'show']);
        Route::post('/messages/{conversation}', [MessageController::class, 'store']);
        Route::patch('/messages/{conversation}/{message}', [MessageController::class, 'update']);
        Route::delete('/messages/{conversation}/{message}', [MessageController::class, 'destroy']);
        Route::post('/messages/admin', [MessageController::class, 'startAdmin']);
        Route::post('/messages/seller/{user}', [MessageController::class, 'startSeller']);
        Route::post('/sellers/{user}/follow', [SellerFollowController::class, 'store']);
        Route::delete('/sellers/{user}/follow', [SellerFollowController::class, 'destroy']);
        Route::get('/me/followed-sellers/promos', [SellerFollowController::class, 'promos']);
        Route::post('/seller/stories', [StoryController::class, 'store']);
        Route::delete('/seller/stories/{story}', [StoryController::class, 'destroy']);
        Route::post('/stories/{story}/message', [StoryInteractionController::class, 'message']);
        Route::post('/stories/{story}/like', [StoryInteractionController::class, 'like']);
        Route::delete('/stories/{story}/like', [StoryInteractionController::class, 'unlike']);
        Route::get('/seller/finance/summary', [FinanceController::class, 'sellerSummary']);
        Route::get('/seller/finance/transactions', [FinanceController::class, 'sellerTransactions']);
        Route::get('/seller/payout-requests', [FinanceController::class, 'sellerPayoutRequests']);
        Route::post('/seller/payout-requests', [FinanceController::class, 'storeSellerPayoutRequest']);
        Route::get('/seller/order-items', [FinanceController::class, 'sellerOrderItems']);
        Route::patch('/seller/order-items/{item}/status', [FinanceController::class, 'updateSellerOrderItemStatus']);
        Route::get('/admin/finance/pending-reviews', [FinanceController::class, 'adminPendingReviews']);
        Route::post('/admin/finance/order-items/{item}/approve-release', [FinanceController::class, 'adminApproveRelease']);
    });

    Route::get('/locations', [LocationController::class, 'index']);
});
