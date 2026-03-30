<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LocalitiesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProductQuestionController;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\RefundController as AdminRefundController;
use App\Http\Controllers\Admin\SellerApplicationAdminController;

use App\Http\Controllers\SellerApplicationController;

use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;

use App\Http\Controllers\Seller\ProfileController as SellerProfileController;
use App\Http\Controllers\SellerPublicController;

use App\Http\Controllers\SellerReviewController;

/*
|--------------------------------------------------------------------------
| Public pages
|--------------------------------------------------------------------------
*/

Route::get('/sellers', [SellerPublicController::class, 'index'])
    ->name('sellers.index');

Route::get('/sellers/{user}', [SellerPublicController::class, 'show'])
    ->name('seller.public.show');

Route::view('/despre-noi', 'shop.about')->name('about');
Route::view('/termeni-si-conditii', 'shop.terms')->name('terms');

Route::get('/', [ShopController::class, 'index'])->name('home');
Route::get('/product/{product}', [ShopController::class, 'show'])->name('product.show');
Route::get('/category/{category:slug}', [ShopController::class, 'category'])->name('category.show');
Route::get('/subcategory/{category:slug}', [ShopController::class, 'subcategory'])->name('subcategory.show');

Route::get('/search', [ShopController::class, 'search'])->name('search');

/*
|--------------------------------------------------------------------------
| Product questions / reviews
|--------------------------------------------------------------------------
*/
Route::post('/products/{product}/questions', [ProductQuestionController::class, 'store'])
    ->name('products.questions.store');

Route::middleware('auth')->group(function () {
    Route::post('/products/{product}/review', [ProductReviewController::class, 'store'])
        ->name('products.review.store');
});

/*
|--------------------------------------------------------------------------
| Become seller
|--------------------------------------------------------------------------
*/
Route::get('/become-seller', [SellerApplicationController::class, 'create'])
    ->name('seller.application.create');

Route::post('/become-seller', [SellerApplicationController::class, 'store'])
    ->name('seller.application.store');

/*
|--------------------------------------------------------------------------
| Cart
|--------------------------------------------------------------------------
*/
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/buy/{product}', [CartController::class, 'buyNow'])->name('cart.buy');
Route::post('/cart/update/{product}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

/*
|--------------------------------------------------------------------------
| Checkout
|--------------------------------------------------------------------------
*/
Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

/*
|--------------------------------------------------------------------------
| User Orders
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
require __DIR__ . '/profile.php';

Route::middleware(['auth'])->get('/dashboard', function () {
    return redirect()->route('home');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/questions/{question}/answer', [ProductQuestionController::class, 'answer'])
        ->name('products.questions.answer');
});

Route::middleware('auth')->group(function () {
    Route::post('/sellers/{user}/review', [SellerReviewController::class, 'store'])
        ->name('seller.reviews.store');
});

/*
|--------------------------------------------------------------------------
| MAIB
|--------------------------------------------------------------------------
*/
Route::get('/pay/maib/receipt', [PaymentController::class, 'receipt'])->name('pay.maib.receipt');
Route::get('/pay/maib/ok', [PaymentController::class, 'ok'])->name('pay.maib.ok');
Route::get('/pay/maib/fail', [PaymentController::class, 'fail'])->name('pay.maib.fail');
Route::post('/pay/maib/callback', [PaymentController::class, 'callback'])->name('pay.maib.callback');

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::get('/seller-applications', [SellerApplicationAdminController::class, 'index'])
        ->name('admin.seller_applications.index');

    Route::post('/seller-applications/{id}/approve', [SellerApplicationAdminController::class, 'approve'])
        ->name('admin.seller_applications.approve');

    Route::post('/seller-applications/{id}/reject', [SellerApplicationAdminController::class, 'reject'])
        ->name('admin.seller_applications.reject');

    Route::get('/products', [AdminProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{id}', [AdminProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::post('/products/{id}/approve', [AdminProductController::class, 'approve'])->name('admin.products.approve');

    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');

    Route::get('/banners', [AdminBannerController::class, 'index'])->name('admin.banners.index');
    Route::get('/banners/create', [AdminBannerController::class, 'create'])->name('admin.banners.create');
    Route::post('/banners', [AdminBannerController::class, 'store'])->name('admin.banners.store');
    Route::delete('/banners/{banner}', [AdminBannerController::class, 'destroy'])->name('admin.banners.destroy');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.status');
    Route::post('/orders/{order}/refund', [AdminRefundController::class, 'store'])->name('admin.orders.refund');
    Route::post('/orders/{order}/maib-refresh', [AdminOrderController::class, 'maibRefresh'])->name('admin.orders.maib_refresh');
});

/*
|--------------------------------------------------------------------------
| Seller
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])
    ->prefix('seller')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('seller.dashboard');

        Route::get('/products', [SellerProductController::class, 'index'])
            ->name('seller.products.index');

        Route::get('/products/create', [SellerProductController::class, 'create'])
            ->name('seller.products.create');

        Route::post('/products', [SellerProductController::class, 'store'])
            ->name('seller.products.store');

        Route::get('/products/{id}/edit', [SellerProductController::class, 'edit'])
            ->name('seller.products.edit');

        Route::put('/products/{id}', [SellerProductController::class, 'update'])
            ->name('seller.products.update');

        Route::delete('/products/{id}', [SellerProductController::class, 'destroy'])
            ->name('seller.products.destroy');

        Route::get('/orders', [SellerOrderController::class, 'index'])
            ->name('seller.orders.index');

        Route::get('/orders/{id}', [SellerOrderController::class, 'show'])
            ->name('seller.orders.show');

        Route::patch('/orders/{orderId}/items/{itemId}/status', [SellerOrderController::class, 'updateItemStatus'])
            ->name('seller.orders.items.status');

        Route::get('/profile', [SellerProfileController::class, 'edit'])
            ->name('seller.profile.edit');

        Route::put('/profile', [SellerProfileController::class, 'update'])
            ->name('seller.profile.update');

        Route::post('/products/ai-banner-preview', [SellerProductController::class, 'generateBannerPreview'])
            ->name('seller.products.ai_banner_preview');

        Route::delete('/products/ai-banner-preview', [SellerProductController::class, 'deleteBannerPreview'])
            ->name('seller.products.ai_banner_preview.delete');

        Route::middleware('auth')->group(function () {
            Route::post('/questions/{question}/answer', [ProductQuestionController::class, 'answer'])
                ->name('products.questions.answer');
        });
    });