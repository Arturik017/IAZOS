<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LocalitiesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;

use App\Http\Controllers\Admin\RefundController as AdminRefundController;


/*
|--------------------------------------------------------------------------
| Public pages
|--------------------------------------------------------------------------
*/
Route::view('/despre-noi', 'shop.about')->name('about');
Route::view('/termeni-si-conditii', 'shop.terms')->name('terms');

Route::get('/', [ShopController::class, 'index'])->name('home');
Route::get('/product/{product}', [ShopController::class, 'show'])->name('product.show');
Route::get('/category/{category:slug}', [ShopController::class, 'category'])->name('category.show');
Route::get('/subcategory/{category:slug}', [ShopController::class, 'subcategory'])->name('subcategory.show');

Route::get('/search', [ShopController::class, 'search'])->name('search');

Route::get('/pay/maib/receipt', [\App\Http\Controllers\PaymentController::class, 'receipt'])
    ->name('pay.maib.receipt');


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
| Checkout (doar user logat -> altfel redirect la register)
|--------------------------------------------------------------------------
*/
// Route::middleware(['checkout.auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
// });

/*
|--------------------------------------------------------------------------
| Localities API (UI helper)
|--------------------------------------------------------------------------
*/
// Route::get('/api/localities', [LocalitiesController::class, 'byDistrict'])->name('api.localities');

/*
|--------------------------------------------------------------------------
| User Orders
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
});


/*
|--------------------------------------------------------------------------
| Auth routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
require __DIR__ . '/profile.php';

Route::middleware(['auth'])->get('/dashboard', function () {
    return redirect()->route('home');
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| MAIB
|--------------------------------------------------------------------------
*/




    // MAIB Payments (public, deoarece MAIB redirecționează + trimite callback din exterior)
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

    // Products
    Route::get('/products', [AdminProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{id}', [AdminProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');

    // Categories
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');

    // Banners
    Route::get('/banners', [AdminBannerController::class, 'index'])->name('admin.banners.index');
    Route::get('/banners/create', [AdminBannerController::class, 'create'])->name('admin.banners.create');
    Route::post('/banners', [AdminBannerController::class, 'store'])->name('admin.banners.store');
    Route::delete('/banners/{banner}', [AdminBannerController::class, 'destroy'])->name('admin.banners.destroy');

    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');

    // IMPORTANT: PATCH (ca blade-ul tau foloseste @method('PATCH'))
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.status');

    Route::post('/orders/{order}/refund', [AdminRefundController::class, 'store'])
    ->name('admin.orders.refund');
    
    Route::post('/admin/orders/{order}/maib-refresh', [App\Http\Controllers\Admin\OrderController::class, 'maibRefresh'])
    ->name('admin.orders.maib_refresh');

});
