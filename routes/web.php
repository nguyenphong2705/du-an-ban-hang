<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Chuyển đổi ngôn ngữ (Mặc định: Tiếng Việt 'vi', Hỗ trợ: Tiếng Anh 'en')
Route::get('/ngon-ngu/{locale}', function (string $locale) {
    if (in_array($locale, ['vi', 'en'], true)) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Danh sách & Chi tiết sản phẩm mỹ phẩm
Route::get('/san-pham', [ProductController::class, 'index'])->name('products.index');
Route::get('/san-pham/{slug}', [ProductController::class, 'show'])->name('products.show');

// Giỏ hàng (Laravel Session + Đồng bộ DB cho Khách hàng)
Route::post('/gio-hang/them', [CartController::class, 'add'])->name('cart.add');
Route::post('/gio-hang/cap-nhat', [CartController::class, 'update'])->name('cart.update');
Route::post('/gio-hang/xoa', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/gio-hang/xoa-tat-ca', [CartController::class, 'clear'])->name('cart.clear');

// Thanh toán, Mã giảm giá & Đặt hàng
Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/thanh-toan', [CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/thanh-toan/ma-giam-gia/ap-dung', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon.apply');
Route::post('/thanh-toan/ma-giam-gia/go-bo', [CheckoutController::class, 'removeCoupon'])->name('checkout.coupon.remove');

// Trang Khách hàng tra cứu nhanh bằng SĐT (/tra-cuu-don-hang)
Route::get('/tra-cuu-don-hang', [CheckoutController::class, 'trackOrders'])->name('orders.track');

// =========================================================================
// 1. TRANG ĐĂNG NHẬP & TRUNG TÂM TÀI KHOẢN DÀNH CHO KHÁCH HÀNG (/dang-nhap & /tai-khoan)
// =========================================================================
Route::get('/dang-nhap', [CustomerAuthController::class, 'showLogin'])->name('customer.login');
Route::post('/dang-nhap', [CustomerAuthController::class, 'login'])->name('customer.login.submit');
Route::post('/dang-ky', [CustomerAuthController::class, 'register'])->name('customer.register');
Route::post('/dang-xuat', [CustomerAuthController::class, 'logout'])->name('customer.logout');
Route::get('/quen-mat-khau', [CustomerAuthController::class, 'showForgotPassword'])->name('customer.password.request');
Route::post('/quen-mat-khau/gui-ma', [CustomerAuthController::class, 'sendResetCode'])->name('customer.password.send');
Route::post('/quen-mat-khau/dat-lai', [CustomerAuthController::class, 'resetPasswordWithCode'])->name('customer.password.reset');
Route::get('/tai-khoan', [CustomerAuthController::class, 'account'])->name('customer.account');
Route::post('/tai-khoan/ma-giam-gia/ap-dung', [CustomerAuthController::class, 'applyVoucherToCart'])->name('customer.voucher.apply');

// =========================================================================
// 2. TRANG ĐĂNG NHẬP & QUẢN TRỊ VIÊN CỦA SHOP (/quan-tri/dang-nhap & /quan-tri)
// =========================================================================
Route::get('/quan-tri/dang-nhap', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/quan-tri/dang-nhap', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/quan-tri/dang-xuat', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::get('/quan-tri', [AdminController::class, 'index'])->name('admin.index');
Route::get('/quan-tri/thong-ke', [AdminController::class, 'analytics'])->name('admin.analytics');
Route::get('/quan-tri/don-hang', [AdminController::class, 'orders'])->name('admin.orders.index');
Route::get('/quan-tri/san-pham', [AdminController::class, 'products'])->name('admin.products.index');
Route::get('/quan-tri/ma-giam-gia', [AdminController::class, 'coupons'])->name('admin.coupons.index');

// REST API lọc sản phẩm cho trang Admin (hỗ trợ category_filter & stock_filter)
Route::get('/api/admin/products', [AdminController::class, 'filterProductsApi'])->name('admin.api.products');
Route::get('/quan-tri/api/san-pham', [AdminController::class, 'filterProductsApi'])->name('admin.api.products.vi');

Route::post('/quan-tri/san-pham', [AdminController::class, 'storeProduct'])->name('admin.products.store');
Route::post('/quan-tri/san-pham/upload-anh-mo-ta', [AdminController::class, 'uploadEditorImage'])->name('admin.products.upload-editor-image');
Route::post('/quan-tri/san-pham/chuan-hoa', [AdminController::class, 'standardizeProducts'])->name('admin.products.standardize');
Route::put('/quan-tri/san-pham/{product}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
Route::patch('/quan-tri/san-pham/{product}/ton-kho', [AdminController::class, 'updateProductStock'])->name('admin.products.stock');
Route::patch('/quan-tri/san-pham/{product}/ban-chay', [AdminController::class, 'toggleBestSeller'])->name('admin.products.best-seller');
Route::delete('/quan-tri/san-pham/{product}', [AdminController::class, 'destroyProduct'])->name('admin.products.destroy');

Route::patch('/quan-tri/don-hang/{order}/trang-thai', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.status');
Route::delete('/quan-tri/don-hang/{order}', [AdminController::class, 'destroyOrder'])->name('admin.orders.destroy');

Route::post('/quan-tri/ma-giam-gia', [AdminController::class, 'storeCoupon'])->name('admin.coupons.store');
Route::patch('/quan-tri/ma-giam-gia/{coupon}/trang-thai', [AdminController::class, 'toggleCoupon'])->name('admin.coupons.toggle');
Route::delete('/quan-tri/ma-giam-gia/{coupon}', [AdminController::class, 'destroyCoupon'])->name('admin.coupons.destroy');

Route::post('/quan-tri/danh-muc', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
Route::post('/quan-tri/thuong-hieu', [AdminController::class, 'storeBrand'])->name('admin.brands.store');

// Chuyển hướng tự động các đường dẫn tiếng Anh cũ sang đường dẫn Tiếng Việt mới
Route::redirect('/products', '/san-pham', 301);
Route::get('/products/{slug}', fn (string $slug) => redirect()->route('products.show', $slug, 301));
Route::redirect('/checkout', '/thanh-toan', 301);
Route::redirect('/my-orders', '/tra-cuu-don-hang', 301);
Route::redirect('/login', '/dang-nhap', 301);
Route::redirect('/forgot-password', '/quen-mat-khau', 301);
Route::redirect('/account', '/tai-khoan', 301);
Route::redirect('/admin', '/quan-tri', 301);
Route::redirect('/admin/login', '/quan-tri/dang-nhap', 301);
