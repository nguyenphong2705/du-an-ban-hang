<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Danh sách & Chi tiết sản phẩm mỹ phẩm
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Giỏ hàng (Laravel Session + Đồng bộ DB cho Khách hàng)
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Thanh toán, Mã giảm giá & Đặt hàng
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/checkout/coupon/apply', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon.apply');
Route::post('/checkout/coupon/remove', [CheckoutController::class, 'removeCoupon'])->name('checkout.coupon.remove');

// Trang Khách hàng tra cứu nhanh bằng SĐT (/my-orders)
Route::get('/my-orders', [CheckoutController::class, 'trackOrders'])->name('orders.track');

// =========================================================================
// 1. TRANG ĐĂNG NHẬP & TRUNG TÂM TÀI KHOẢN DÀNH CHO KHÁCH HÀNG (/login & /account)
// =========================================================================
Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
Route::post('/login', [CustomerAuthController::class, 'login'])->name('customer.login.submit');
Route::post('/register', [CustomerAuthController::class, 'register'])->name('customer.register');
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');
Route::get('/account', [CustomerAuthController::class, 'account'])->name('customer.account');
Route::post('/account/voucher/apply', [CustomerAuthController::class, 'applyVoucherToCart'])->name('customer.voucher.apply');

// =========================================================================
// 2. TRANG ĐĂNG NHẬP & QUẢN TRỊ VIÊN CỦA SHOP (/admin/login & /admin)
// =========================================================================
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
Route::post('/admin/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
Route::put('/admin/products/{product}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
Route::patch('/admin/products/{product}/stock', [AdminController::class, 'updateProductStock'])->name('admin.products.stock');
Route::patch('/admin/products/{product}/best-seller', [AdminController::class, 'toggleBestSeller'])->name('admin.products.best-seller');
Route::delete('/admin/products/{product}', [AdminController::class, 'destroyProduct'])->name('admin.products.destroy');

Route::patch('/admin/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.status');
Route::delete('/admin/orders/{order}', [AdminController::class, 'destroyOrder'])->name('admin.orders.destroy');

Route::post('/admin/coupons', [AdminController::class, 'storeCoupon'])->name('admin.coupons.store');
Route::patch('/admin/coupons/{coupon}/toggle', [AdminController::class, 'toggleCoupon'])->name('admin.coupons.toggle');
Route::delete('/admin/coupons/{coupon}', [AdminController::class, 'destroyCoupon'])->name('admin.coupons.destroy');

Route::post('/admin/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
Route::post('/admin/brands', [AdminController::class, 'storeBrand'])->name('admin.brands.store');
