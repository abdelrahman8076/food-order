<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\DeliveryLocationController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\AnalyticsController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/category/{slug}', [MenuController::class, 'category'])->name('menu.category');
Route::get('/menu/item/{slug}', [MenuController::class, 'show'])->name('menu.item');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{item}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon.apply');
Route::post('/checkout/coupon/remove', [CheckoutController::class, 'removeCoupon'])->name('checkout.coupon.remove');

Route::get('/order/track', [OrderTrackingController::class, 'show'])->name('order.track');
Route::get('/order/confirmation/{order}', [OrderTrackingController::class, 'confirmation'])->name('order.confirmation');
Route::get('/order/{order}/status', [OrderTrackingController::class, 'status'])->name('order.status');

Route::middleware('auth')->group(function () {
    Route::get('/my-orders', [OrderTrackingController::class, 'myOrders'])->name('my-orders');
});

// Auth (simple login for admin)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin dashboard
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('items', ItemController::class)->except(['show']);
    Route::resource('coupons', CouponController::class)->except(['show']);
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/notifications', [OrderController::class, 'notifications'])->name('orders.notifications');
    Route::get('orders/board', [OrderController::class, 'board'])->name('orders.board');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('orders/{order}/advance', [OrderController::class, 'advance'])->name('orders.advance');
    Route::get('settings', [OrderController::class, 'settings'])->name('settings');
    Route::post('settings', [OrderController::class, 'updateSettings'])->name('settings.update');
    Route::get('delivery-locations', [DeliveryLocationController::class, 'index'])->name('delivery-locations.index');
    Route::post('delivery-locations/cities', [DeliveryLocationController::class, 'storeCity'])->name('delivery-locations.cities.store');
    Route::delete('delivery-locations/cities/{city}', [DeliveryLocationController::class, 'destroyCity'])->name('delivery-locations.cities.destroy');
    Route::post('delivery-locations/areas', [DeliveryLocationController::class, 'storeArea'])->name('delivery-locations.areas.store');
    Route::patch('delivery-locations/areas/{area}', [DeliveryLocationController::class, 'updateArea'])->name('delivery-locations.areas.update');
    Route::delete('delivery-locations/areas/{area}', [DeliveryLocationController::class, 'destroyArea'])->name('delivery-locations.areas.destroy');
    Route::patch('orders/{order}/delivery-fee', [OrderController::class, 'updateDeliveryFee'])->name('orders.delivery-fee');
});
