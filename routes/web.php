<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Hệ thống Định tuyến Website Mỹ Phẩm Shine Beauty
|--------------------------------------------------------------------------
|
| Tệp cấu hình toàn bộ các tuyến đường (routes), chức năng và API nội bộ:
| 1. Cấu hình đa ngôn ngữ & Trang chủ.
| 2. Danh mục, tìm kiếm & Chi tiết sản phẩm mỹ phẩm.
| 3. Quản lý giỏ hàng mua sắm (Session & Database).
| 4. Quy trình thanh toán, mã giảm giá (Voucher) & Tra cứu đơn hàng.
| 5. Tài khoản & Xác thực dành cho Khách hàng (Đăng ký, Đăng nhập, Quên mật khẩu, Trang cá nhân).
| 6. Phân hệ Quản trị viên (Admin / Chủ Shop) & Bảng điều khiển kinh doanh.
| 7. Chuyển hướng tương thích ngược (Redirect 301).
|
*/

// =========================================================================
// 1. ĐA NGÔN NGỮ & TRANG CHỦ WEBSITE
// =========================================================================

/**
 * Chuyển đổi ngôn ngữ hiển thị giao diện.
 * - Phương thức: GET
 * - Đường dẫn: /ngon-ngu/{locale}
 * - Hỗ trợ: 'vi' (Tiếng Việt - mặc định), 'en' (Tiếng Anh)
 */
Route::get('/ngon-ngu/{locale}', function (string $locale) {
    if (in_array($locale, ['vi', 'en'], true)) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

/**
 * Trang chủ website bán hàng.
 * - Phương thức: GET
 * - Đường dẫn: /
 * - Controller: HomeController@index
 * - Chức năng: Banner, danh mục, thương hiệu, top 6 bán chạy, lọc theo loại da (Skin Explorer).
 */
Route::get('/', [HomeController::class, 'index'])->name('home');


// =========================================================================
// 2. KHÁM PHÁ, TÌM KIẾM & CHI TIẾT SẢN PHẨM MỸ PHẨM
// =========================================================================

/**
 * Danh sách sản phẩm và bộ lọc đa tiêu chí.
 * - Phương thức: GET
 * - Đường dẫn: /san-pham
 * - Controller: ProductController@index
 * - Bộ lọc hỗ trợ: category, brand, skin_type, min_price, max_price, sort, search.
 * - Hỗ trợ: Trả về trang HTML đầy đủ, Partial HTML (AJAX), hoặc JSON REST API.
 */
Route::get('/san-pham', [ProductController::class, 'index'])->name('products.index');

/**
 * Chi tiết một sản phẩm mỹ phẩm cụ thể.
 * - Phương thức: GET
 * - Đường dẫn: /san-pham/{slug}
 * - Controller: ProductController@show
 * - Chức năng: Xem mô tả, hoạt chất/thành phần, hình ảnh, đánh giá và 4 sản phẩm liên quan.
 */
Route::get('/san-pham/{slug}', [ProductController::class, 'show'])->name('products.show');


// =========================================================================
// 3. QUẢN LÝ GIỎ HÀNG (LARAVEL SESSION + ĐỒNG BỘ DATABASE KHÁCH HÀNG)
// =========================================================================

/**
 * Thêm sản phẩm vào giỏ hàng (hoặc Mua ngay).
 * - Phương thức: POST
 * - Đường dẫn: /gio-hang/them
 * - Controller: CartController@add
 */
Route::post('/gio-hang/them', [CartController::class, 'add'])->name('cart.add');

/**
 * Cập nhật số lượng sản phẩm trong giỏ hàng.
 * - Phương thức: POST
 * - Đường dẫn: /gio-hang/cap-nhat
 * - Controller: CartController@update
 */
Route::post('/gio-hang/cap-nhat', [CartController::class, 'update'])->name('cart.update');

/**
 * Xóa một sản phẩm cụ thể ra khỏi giỏ hàng.
 * - Phương thức: POST
 * - Đường dẫn: /gio-hang/xoa
 * - Controller: CartController@remove
 */
Route::post('/gio-hang/xoa', [CartController::class, 'remove'])->name('cart.remove');

/**
 * Làm trống toàn bộ giỏ hàng hiện tại.
 * - Phương thức: POST
 * - Đường dẫn: /gio-hang/xoa-tat-ca
 * - Controller: CartController@clear
 */
Route::post('/gio-hang/xoa-tat-ca', [CartController::class, 'clear'])->name('cart.clear');


// =========================================================================
// 4. THANH TOÁN, MÃ GIẢM GIÁ (VOUCHER) & TRA CỨU ĐƠN HÀNG
// =========================================================================

/**
 * Hiển thị trang đặt hàng & thanh toán.
 * - Phương thức: GET
 * - Đường dẫn: /thanh-toan
 * - Controller: CheckoutController@index
 * - Chức năng: Tính phí ship, số tiền giảm voucher, bảo vệ chống IDOR khi xem lại đơn vừa đặt.
 */
Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('checkout.index');

/**
 * Xử lý xác nhận đặt đơn hàng mới.
 * - Phương thức: POST
 * - Đường dẫn: /thanh-toan
 * - Controller: CheckoutController@store
 * - Chức năng: Lưu đơn hàng, trừ tồn kho, gửi thông báo và dọn dẹp giỏ hàng.
 */
Route::post('/thanh-toan', [CheckoutController::class, 'store'])->name('checkout.store');

/**
 * Áp dụng mã giảm giá (Voucher) cho đơn hàng.
 * - Phương thức: POST
 * - Đường dẫn: /thanh-toan/ma-giam-gia/ap-dung
 * - Controller: CheckoutController@applyCoupon
 * - Quy tắc: Mỗi đơn hàng chỉ áp dụng tối đa 01 voucher hợp lệ.
 */
Route::post('/thanh-toan/ma-giam-gia/ap-dung', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon.apply');

/**
 * Gỡ bỏ mã giảm giá hiện tại khỏi đơn hàng.
 * - Phương thức: POST
 * - Đường dẫn: /thanh-toan/ma-giam-gia/go-bo
 * - Controller: CheckoutController@removeCoupon
 */
Route::post('/thanh-toan/ma-giam-gia/go-bo', [CheckoutController::class, 'removeCoupon'])->name('checkout.coupon.remove');

/**
 * Tra cứu nhanh trạng thái đơn hàng bằng số điện thoại khách hàng.
 * - Phương thức: GET
 * - Đường dẫn: /tra-cuu-don-hang
 * - Controller: CheckoutController@trackOrders
 * - Bảo mật: Chống SQL Wildcard Injection (% và _).
 */
Route::get('/tra-cuu-don-hang', [CheckoutController::class, 'trackOrders'])->name('orders.track');


// =========================================================================
// 5. TÀI KHOẢN & XÁC THỰC KHÁCH HÀNG (/dang-nhap & /tai-khoan)
// =========================================================================

/**
 * Hiển thị giao diện Đăng nhập / Đăng ký Khách hàng.
 * - Phương thức: GET
 * - Đường dẫn: /dang-nhap
 * - Controller: CustomerAuthController@showLogin
 */
Route::get('/dang-nhap', [CustomerAuthController::class, 'showLogin'])->name('customer.login');

/**
 * Xử lý đăng nhập tài khoản Khách hàng (hỗ trợ nhập Email hoặc Tên đăng nhập).
 * - Phương thức: POST
 * - Đường dẫn: /dang-nhap
 * - Controller: CustomerAuthController@login
 */
Route::post('/dang-nhap', [CustomerAuthController::class, 'login'])->name('customer.login.submit');

/**
 * Xử lý đăng ký tài khoản Khách hàng mới.
 * - Phương thức: POST
 * - Đường dẫn: /dang-ky
 * - Controller: CustomerAuthController@register
 */
Route::post('/dang-ky', [CustomerAuthController::class, 'register'])->name('customer.register');

/**
 * Đăng xuất tài khoản Khách hàng.
 * - Phương thức: POST
 * - Đường dẫn: /dang-xuat
 * - Controller: CustomerAuthController@logout
 */
Route::post('/dang-xuat', [CustomerAuthController::class, 'logout'])->name('customer.logout');

/**
 * Hiển thị giao diện Quên mật khẩu.
 * - Phương thức: GET
 * - Đường dẫn: /quen-mat-khau
 * - Controller: CustomerAuthController@showForgotPassword
 */
Route::get('/quen-mat-khau', [CustomerAuthController::class, 'showForgotPassword'])->name('customer.password.request');

/**
 * Gửi mã OTP xác thực 6 số qua Email / SMS để đặt lại mật khẩu.
 * - Phương thức: POST
 * - Đường dẫn: /quen-mat-khau/gui-ma
 * - Controller: CustomerAuthController@sendResetCode
 */
Route::post('/quen-mat-khau/gui-ma', [CustomerAuthController::class, 'sendResetCode'])->name('customer.password.send');

/**
 * Xác thực mã OTP và đặt lại mật khẩu mới cho tài khoản.
 * - Phương thức: POST
 * - Đường dẫn: /quen-mat-khau/dat-lai
 * - Controller: CustomerAuthController@resetPasswordWithCode
 */
Route::post('/quen-mat-khau/dat-lai', [CustomerAuthController::class, 'resetPasswordWithCode'])->name('customer.password.reset');

/**
 * Trung tâm tài khoản cá nhân dành riêng cho Khách hàng.
 * - Phương thức: GET
 * - Đường dẫn: /tai-khoan
 * - Controller: CustomerAuthController@account
 * - Chức năng: Theo dõi đơn hàng theo trạng thái, giỏ hàng đã lưu, sản phẩm đã mua, ví voucher.
 */
Route::get('/tai-khoan', [CustomerAuthController::class, 'account'])->name('customer.account');

/**
 * Áp dụng nhanh voucher từ trang cá nhân vào giỏ hàng.
 * - Phương thức: POST
 * - Đường dẫn: /tai-khoan/ma-giam-gia/ap-dung
 * - Controller: CustomerAuthController@applyVoucherToCart
 */
Route::post('/tai-khoan/ma-giam-gia/ap-dung', [CustomerAuthController::class, 'applyVoucherToCart'])->name('customer.voucher.apply');


// =========================================================================
// 6. PHÂN HỆ QUẢN TRỊ VIÊN CỦA SHOP (/quan-tri/dang-nhap & /quan-tri)
// =========================================================================

// --- Xác thực Quản trị viên ---
/**
 * Giao diện đăng nhập Quản trị viên.
 * - Phương thức: GET
 * - Đường dẫn: /quan-tri/dang-nhap
 * - Controller: AdminAuthController@showLogin
 */
Route::get('/quan-tri/dang-nhap', [AdminAuthController::class, 'showLogin'])->name('admin.login');

/**
 * Xử lý xác thực đăng nhập Quản trị viên (kiểm tra cờ is_admin = true).
 * - Phương thức: POST
 * - Đường dẫn: /quan-tri/dang-nhap
 * - Controller: AdminAuthController@login
 */
Route::post('/quan-tri/dang-nhap', [AdminAuthController::class, 'login'])->name('admin.login.submit');

/**
 * Đăng xuất khỏi phiên Quản trị viên.
 * - Phương thức: POST
 * - Đường dẫn: /quan-tri/dang-xuat
 * - Controller: AdminAuthController@logout
 */
Route::post('/quan-tri/dang-xuat', [AdminAuthController::class, 'logout'])->name('admin.logout');

// --- Bảng điều khiển & Các Tab Quản lý ---
/**
 * Bảng điều khiển Quản trị viên (tự động điều hướng theo query).
 * - Phương thức: GET
 * - Đường dẫn: /quan-tri
 * - Controller: AdminController@index
 */
Route::get('/quan-tri', [AdminController::class, 'index'])->name('admin.index');

/**
 * Tab Thống kê số liệu kinh doanh & Phân tích KPI, biểu đồ doanh thu.
 * - Phương thức: GET
 * - Đường dẫn: /quan-tri/thong-ke
 * - Controller: AdminController@analytics
 */
Route::get('/quan-tri/thong-ke', [AdminController::class, 'analytics'])->name('admin.analytics');

/**
 * Tab Quản lý danh sách đơn hàng của khách.
 * - Phương thức: GET
 * - Đường dẫn: /quan-tri/don-hang
 * - Controller: AdminController@orders
 */
Route::get('/quan-tri/don-hang', [AdminController::class, 'orders'])->name('admin.orders.index');

/**
 * Tab Quản lý danh mục & Kho sản phẩm mỹ phẩm.
 * - Phương thức: GET
 * - Đường dẫn: /quan-tri/san-pham
 * - Controller: AdminController@products
 */
Route::get('/quan-tri/san-pham', [AdminController::class, 'products'])->name('admin.products.index');

/**
 * Tab Quản lý Mã giảm giá (Voucher khuyến mãi).
 * - Phương thức: GET
 * - Đường dẫn: /quan-tri/ma-giam-gia
 * - Controller: AdminController@coupons
 */
Route::get('/quan-tri/ma-giam-gia', [AdminController::class, 'coupons'])->name('admin.coupons.index');

// --- REST API lọc sản phẩm trang Admin ---
/**
 * REST API lọc sản phẩm nhanh phục vụ AJAX cho trang Admin.
 * - Hỗ trợ cả 2 endpoint: /api/admin/products và /quan-tri/api/san-pham
 * - Controller: AdminController@filterProductsApi
 */
Route::get('/api/admin/products', [AdminController::class, 'filterProductsApi'])->name('admin.api.products');
Route::get('/quan-tri/api/san-pham', [AdminController::class, 'filterProductsApi'])->name('admin.api.products.vi');

// --- Quản lý Sản phẩm (CRUD) ---
/**
 * Thêm mới một sản phẩm mỹ phẩm.
 * - Phương thức: POST | Đường dẫn: /quan-tri/san-pham | Controller: AdminController@storeProduct
 */
Route::post('/quan-tri/san-pham', [AdminController::class, 'storeProduct'])->name('admin.products.store');

/**
 * Tải lên ảnh nhúng trong trình soạn thảo mô tả chi tiết sản phẩm.
 * - Phương thức: POST | Đường dẫn: /quan-tri/san-pham/upload-anh-mo-ta | Controller: AdminController@uploadEditorImage
 */
Route::post('/quan-tri/san-pham/upload-anh-mo-ta', [AdminController::class, 'uploadEditorImage'])->name('admin.products.upload-editor-image');

/**
 * Chuẩn hóa toàn bộ mã SKU (LMN-SP001...) và đồng bộ tồn kho 20 sản phẩm.
 * - Phương thức: POST | Đường dẫn: /quan-tri/san-pham/chuan-hoa | Controller: AdminController@standardizeProducts
 */
Route::post('/quan-tri/san-pham/chuan-hoa', [AdminController::class, 'standardizeProducts'])->name('admin.products.standardize');

/**
 * Cập nhật thông tin chi tiết của sản phẩm.
 * - Phương thức: PUT | Đường dẫn: /quan-tri/san-pham/{product} | Controller: AdminController@updateProduct
 */
Route::put('/quan-tri/san-pham/{product}', [AdminController::class, 'updateProduct'])->name('admin.products.update');

/**
 * Cập nhật nhanh số lượng tồn kho của sản phẩm.
 * - Phương thức: PATCH | Đường dẫn: /quan-tri/san-pham/{product}/ton-kho | Controller: AdminController@updateProductStock
 */
Route::patch('/quan-tri/san-pham/{product}/ton-kho', [AdminController::class, 'updateProductStock'])->name('admin.products.stock');

/**
 * Bật / tắt nhãn Bán chạy (Best Seller) của sản phẩm.
 * - Phương thức: PATCH | Đường dẫn: /quan-tri/san-pham/{product}/ban-chay | Controller: AdminController@toggleBestSeller
 */
Route::patch('/quan-tri/san-pham/{product}/ban-chay', [AdminController::class, 'toggleBestSeller'])->name('admin.products.best-seller');

/**
 * Xóa một sản phẩm và ảnh cục bộ liên quan khỏi hệ thống.
 * - Phương thức: DELETE | Đường dẫn: /quan-tri/san-pham/{product} | Controller: AdminController@destroyProduct
 */
Route::delete('/quan-tri/san-pham/{product}', [AdminController::class, 'destroyProduct'])->name('admin.products.destroy');

// --- Quản lý Đơn hàng (Cập nhật & Xóa) ---
/**
 * Cập nhật trạng thái xử lý của đơn hàng (PENDING, CONFIRMED, SHIPPING, COMPLETED, CANCELLED).
 * - Phương thức: PATCH | Đường dẫn: /quan-tri/don-hang/{order}/trang-thai | Controller: AdminController@updateOrderStatus
 */
Route::patch('/quan-tri/don-hang/{order}/trang-thai', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.status');

/**
 * Xóa một đơn hàng khỏi hệ thống quản trị.
 * - Phương thức: DELETE | Đường dẫn: /quan-tri/don-hang/{order} | Controller: AdminController@destroyOrder
 */
Route::delete('/quan-tri/don-hang/{order}', [AdminController::class, 'destroyOrder'])->name('admin.orders.destroy');

// --- Quản lý Mã giảm giá (CRUD) ---
/**
 * Tạo mới mã giảm giá (Voucher).
 * - Phương thức: POST | Đường dẫn: /quan-tri/ma-giam-gia | Controller: AdminController@storeCoupon
 */
Route::post('/quan-tri/ma-giam-gia', [AdminController::class, 'storeCoupon'])->name('admin.coupons.store');

/**
 * Bật hoặc tắt trạng thái hoạt động của mã giảm giá.
 * - Phương thức: PATCH | Đường dẫn: /quan-tri/ma-giam-gia/{coupon}/trang-thai | Controller: AdminController@toggleCoupon
 */
Route::patch('/quan-tri/ma-giam-gia/{coupon}/trang-thai', [AdminController::class, 'toggleCoupon'])->name('admin.coupons.toggle');

/**
 * Xóa một mã giảm giá khỏi hệ thống.
 * - Phương thức: DELETE | Đường dẫn: /quan-tri/ma-giam-gia/{coupon} | Controller: AdminController@destroyCoupon
 */
Route::delete('/quan-tri/ma-giam-gia/{coupon}', [AdminController::class, 'destroyCoupon'])->name('admin.coupons.destroy');

// --- Quản lý Danh mục & Thương hiệu đối tác ---
/**
 * Thêm mới một danh mục sản phẩm mỹ phẩm.
 * - Phương thức: POST | Đường dẫn: /quan-tri/danh-muc | Controller: AdminController@storeCategory
 */
Route::post('/quan-tri/danh-muc', [AdminController::class, 'storeCategory'])->name('admin.categories.store');

/**
 * Thêm mới một thương hiệu mỹ phẩm đối tác.
 * - Phương thức: POST | Đường dẫn: /quan-tri/thuong-hieu | Controller: AdminController@storeBrand
 */
Route::post('/quan-tri/thuong-hieu', [AdminController::class, 'storeBrand'])->name('admin.brands.store');


// =========================================================================
// 7. CHUYỂN HƯỚNG TỰ ĐỘNG CÁC ĐƯỜNG DẪN TIẾNG ANH CŨ SANG TIẾNG VIỆT MỚI (301)
// =========================================================================
Route::redirect('/products', '/san-pham', 301);
Route::get('/products/{slug}', fn (string $slug) => redirect()->route('products.show', $slug, 301));
Route::redirect('/checkout', '/thanh-toan', 301);
Route::redirect('/my-orders', '/tra-cuu-don-hang', 301);
Route::redirect('/login', '/dang-nhap', 301);
Route::redirect('/forgot-password', '/quen-mat-khau', 301);
Route::redirect('/account', '/tai-khoan', 301);
Route::redirect('/admin', '/quan-tri', 301);
Route::redirect('/admin/login', '/quan-tri/dang-nhap', 301);
