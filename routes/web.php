<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Danh sách & Chi tiết sản phẩm mỹ phẩm
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Giỏ hàng (Laravel Session)
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Thanh toán, Mã giảm giá & Đặt hàng
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/checkout/coupon/apply', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon.apply');
Route::post('/checkout/coupon/remove', [CheckoutController::class, 'removeCoupon'])->name('checkout.coupon.remove');

// Trang Khách hàng tự tra cứu đơn mua bằng SĐT (/my-orders)
Route::get('/my-orders', [CheckoutController::class, 'trackOrders'])->name('orders.track');

// Đăng nhập / Đăng xuất dành riêng cho Chủ Shop (Admin Authentication)
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Hệ thống Quản trị Back-Office dành cho Chủ Shop (/admin)
Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::post('/admin/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
Route::put('/admin/products/{product}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
Route::delete('/admin/products/{product}', [AdminController::class, 'destroyProduct'])->name('admin.products.destroy');

Route::patch('/admin/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.status');
Route::delete('/admin/orders/{order}', [AdminController::class, 'destroyOrder'])->name('admin.orders.destroy');

Route::post('/admin/coupons', [AdminController::class, 'storeCoupon'])->name('admin.coupons.store');
Route::patch('/admin/coupons/{coupon}/toggle', [AdminController::class, 'toggleCoupon'])->name('admin.coupons.toggle');
Route::delete('/admin/coupons/{coupon}', [AdminController::class, 'destroyCoupon'])->name('admin.coupons.destroy');

Route::post('/admin/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
Route::post('/admin/brands', [AdminController::class, 'storeBrand'])->name('admin.brands.store');
