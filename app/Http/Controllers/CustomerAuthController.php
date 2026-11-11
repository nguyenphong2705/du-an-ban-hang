<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * Class CustomerAuthController
 *
 * Xử lý toàn bộ logic tài khoản dành riêng cho Khách hàng:
 * - Đăng nhập bằng Email hoặc Tên đăng nhập (username).
 * - Đăng ký tài khoản khách hàng mới và tự động đăng nhập.
 * - Đăng xuất và dọn dẹp phiên làm việc.
 * - Quên mật khẩu: gửi mã xác thực OTP 6 số qua Email/SMS và đặt lại mật khẩu mới.
 * - Trung tâm tài khoản khách hàng (/tai-khoan): xem lịch sử đơn hàng, trạng thái giao nhận, giỏ hàng đã lưu, sản phẩm đã mua và ví voucher cá nhân.
 */
class CustomerAuthController extends Controller
{
    /**
     * Hiển thị giao diện đăng nhập / đăng ký dành cho Khách hàng.
     *
     * Chức năng:
     * - Nếu khách hàng đã đăng nhập, tự động chuyển hướng đến Trung tâm tài khoản (/tai-khoan).
     * - Trả về view `customer.login` hỗ trợ cả 2 tab Đăng nhập và Đăng ký.
     *
     * @return View|RedirectResponse Giao diện đăng nhập hoặc chuyển hướng đến trang tài khoản
     */
    public function showLogin()
    {
        if (Auth::check() && !Auth::user()->is_admin) {
            return redirect()->route('customer.account');
        }

        return view('customer.login');
    }

    /**
     * Xử lý xác thực đăng nhập của Khách hàng.
     *
     * Quy trình xử lý:
     * 1. Xác thực dữ liệu đầu vào (hỗ trợ nhập Email hoặc Username).
     * 2. Tìm người dùng trong database và đối chiếu mật khẩu với Hash::check().
     * 3. Tạo lại Session ID để chống tấn công cố định phiên (Session Fixation).
     * 4. Tự động đồng bộ giỏ hàng đã lưu trước đây trong database ra session.
     *
     * @param Request $request Yêu cầu HTTP chứa 'login' (email/username), 'password', và 'remember'
     * @return RedirectResponse Chuyển hướng đến trang tài khoản hoặc quay lại kèm thông báo lỗi
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = trim($validated['login']);
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where('email', $loginInput)
            ->orWhere('username', $loginInput)
            ->first();

        if ($user && Hash::check($validated['password'], $user->password)) {
            Auth::login($user, $request->boolean('remember', true));
            $request->session()->regenerate();

            // Đồng bộ giỏ hàng đã lưu từ trước trong PostgreSQL ra Session
            CartController::syncUserCartToSession($user);

            return redirect()->route('customer.account')
                ->with('success', "Chào mừng {$user->name} ({$user->username}) đã quay trở lại! Giỏ hàng đã lưu trước đây của bạn đã được khôi phục.");
        }

        return back()->withErrors([
            'login' => 'Tên đăng nhập / Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('login', 'auth_tab');
    }

    /**
     * Đăng ký tài khoản khách hàng mới.
     *
     * Quy trình xử lý:
     * 1. Kiểm tra tính duy nhất của username và email.
     * 2. Mã hóa mật khẩu an toàn bằng bcrypt.
     * 3. Tạo bản ghi User với role = 'customer' và is_admin = false.
     * 4. Tự động đăng nhập và đồng bộ giỏ hàng hiện tại với tài khoản mới.
     *
     * @param Request $request Yêu cầu HTTP chứa 'name', 'username', 'email', 'phone', 'address', 'password'
     * @return RedirectResponse Chuyển hướng đến trang tài khoản
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => trim($validated['username']),
            'email' => trim($validated['email']),
            'phone' => $validated['phone'],
            'address' => $validated['address'] ?? 'TP. Hồ Chí Minh',
            'password' => Hash::make($validated['password']),
            'is_admin' => false,
            'role' => 'customer',
        ]);

        Auth::login($user, true);
        $request->session()->regenerate();
        CartController::syncUserCartToSession($user);

        return redirect()->route('customer.account')
            ->with('success', 'Đăng ký tài khoản khách hàng thành công!');
    }

    /**
     * Đăng xuất tài khoản khách hàng.
     *
     * @param Request $request Yêu cầu HTTP
     * @return RedirectResponse Chuyển hướng về trang đăng nhập khách hàng kèm thông báo
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login')
            ->with('success', 'Đã đăng xuất khỏi tài khoản khách hàng.');
    }

    /**
     * Hiển thị giao diện Quên mật khẩu & Nhập mã OTP.
     *
     * @return View Giao diện quên mật khẩu (customer.forgot-password)
     */
    public function showForgotPassword(): View
    {
        $resetState = session('password_reset_otp');
        return view('customer.forgot-password', compact('resetState'));
    }

    /**
     * Tạo và gửi mã xác thực OTP 6 số qua Email/SMS để khôi phục mật khẩu.
     *
     * Chức năng:
     * - Tìm kiếm tài khoản khách hàng theo Email, Số điện thoại hoặc Username.
     * - Sinh mã số ngẫu nhiên 6 chữ số với thời hạn 10 phút.
     * - Gửi email qua Laravel Mailer và ghi nhận log hệ thống SMS.
     * - Lưu trạng thái OTP vào Session để phục vụ bước xác nhận.
     *
     * @param Request $request Yêu cầu HTTP chứa 'identifier' và 'channel'
     * @return RedirectResponse Chuyển hướng đến màn hình nhập mã OTP
     */
    public function sendResetCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'channel' => ['nullable', 'in:both,email,phone'],
        ]);

        $identifier = trim($validated['identifier']);
        $channel = $validated['channel'] ?? 'both';

        $user = User::where('is_admin', false)
            ->where(function ($q) use ($identifier) {
                $q->where('email', $identifier)
                    ->orWhere('phone', $identifier)
                    ->orWhere('username', $identifier);
            })
            ->first();

        if (!$user) {
            return back()->withErrors([
                'identifier' => 'Không tìm thấy tài khoản khách hàng với Email, Số điện thoại hoặc Tên đăng nhập này.',
            ])->withInput();
        }

        $otpCode = (string) rand(100000, 999999);

        // Gửi mã xác thực qua Email (Laravel Mailer) & ghi log SMS Gateway
        if (in_array($channel, ['both', 'email'], true) && !empty($user->email)) {
            try {
                Mail::raw(
                    "Xin chào {$user->name},\n\nMã xác thực đặt lại mật khẩu Shine Beauty của bạn là: {$otpCode}\nMã có hiệu lực trong 10 phút.\n\nTrân trọng,\nShine Beauty",
                    function ($message) use ($user) {
                        $message->to($user->email)
                            ->subject('[Shine Beauty] Mã xác thực khôi phục mật khẩu');
                    }
                );
            } catch (\Throwable $e) {
                Log::warning('Send reset email failed: ' . $e->getMessage());
            }
        }

        if (in_array($channel, ['both', 'phone'], true) && !empty($user->phone)) {
            Log::info("[SMS OTP Shine Beauty] Gửi tới SĐT {$user->phone}: Mã khôi phục mật khẩu là {$otpCode}");
        }

        session([
            'password_reset_otp' => [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'channel' => $channel,
                'code' => $otpCode,
                'expires_at' => now()->addMinutes(10)->timestamp,
            ],
        ]);

        return redirect()->route('customer.password.request')
            ->with('otp_sent', "Đã gửi mã xác thực 6 số về Email ({$user->email}) và Số điện thoại ({$user->phone}) của bạn!");
    }

    /**
     * Xác thực mã OTP và cập nhật mật khẩu mới cho tài khoản.
     *
     * Chức năng:
     * - Kiểm tra tính hợp lệ và thời hạn hiệu lực (10 phút) của mã OTP trong session.
     * - Đối chiếu mã OTP người dùng nhập vào.
     * - Cập nhật mật khẩu mới đã mã hóa bằng Hash::make().
     * - Xóa trạng thái session OTP và điều hướng người dùng tới trang đăng nhập.
     *
     * @param Request $request Yêu cầu HTTP chứa 'otp_code' và 'password' (có xác nhận 'password_confirmation')
     * @return RedirectResponse Chuyển hướng về trang đăng nhập hoặc báo lỗi
     */
    public function resetPasswordWithCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'otp_code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'otp_code.required' => 'Vui lòng nhập mã xác thực 6 chữ số.',
            'otp_code.size' => 'Mã xác thực gồm đúng 6 chữ số.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $resetState = session('password_reset_otp');

        if (!$resetState || now()->timestamp > ($resetState['expires_at'] ?? 0)) {
            session()->forget('password_reset_otp');
            return redirect()->route('customer.password.request')
                ->withErrors(['otp_code' => 'Mã xác thực đã hết hạn hoặc không tồn tại. Vui lòng yêu cầu gửi lại mã mới.']);
        }

        if (trim($validated['otp_code']) !== (string) $resetState['code']) {
            return back()->withErrors([
                'otp_code' => 'Mã xác thực không chính xác. Vui lòng kiểm tra lại tin nhắn SMS hoặc Email.',
            ]);
        }

        $user = User::where('is_admin', false)->find($resetState['user_id']);
        if (!$user) {
            session()->forget('password_reset_otp');
            return redirect()->route('customer.password.request')
                ->withErrors(['identifier' => 'Tài khoản không tồn tại.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        session()->forget('password_reset_otp');

        return redirect()->route('customer.login')
            ->with('success', "Đã tạo mật khẩu mới thành công cho tài khoản {$user->username}! Vui lòng đăng nhập.");
    }

    /**
     * Trung tâm tài khoản cá nhân của Khách hàng (/tai-khoan).
     *
     * Chức năng:
     * - Lấy toàn bộ lịch sử đơn hàng của khách theo user_id hoặc số điện thoại.
     * - Lọc đơn hàng theo trạng thái (chờ xác nhận, đang xử lý, đang giao, đã giao, đã hủy).
     * - Đồng bộ và tính toán tổng giá trị giỏ hàng đã lưu.
     * - Hiển thị danh sách sản phẩm khách hàng đã từng mua để mua lại nhanh.
     * - Gợi ý top sản phẩm nổi bật và danh sách các mã giảm giá (voucher) khả dụng.
     *
     * @param Request $request Yêu cầu HTTP chứa bộ lọc 'status'
     * @return View|RedirectResponse Giao diện trang tài khoản (customer.account)
     */
    public function account(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('customer.login')
                ->with('error', 'Vui lòng đăng nhập tài khoản khách hàng để theo dõi đơn hàng, giỏ hàng & voucher.');
        }

        $user = Auth::user();

        // Đồng bộ giỏ hàng đã lưu trong DB với session
        $savedCart = CartController::syncUserCartToSession($user);
        $savedCartTotal = collect($savedCart)->sum(fn ($i) => $i['price'] * $i['quantity']);

        // Lấy danh sách đơn hàng của khách hàng (theo user_id hoặc số điện thoại)
        $statusFilter = $request->query('status', 'ALL');
        $ordersQuery = Order::with('items.product.brand')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                if (!empty($user->phone)) {
                    $q->orWhere('phone', $user->phone);
                }
            })
            ->latest();

        $allMyOrders = (clone $ordersQuery)->get();

        if ($statusFilter !== 'ALL') {
            $ordersQuery->where('status', $statusFilter);
        }
        $myOrders = $ordersQuery->get();

        // Lấy danh sách các sản phẩm khách hàng đã từng mua trước đây
        $purchasedProductIds = $allMyOrders->flatMap(fn ($o) => $o->items->pluck('product_id'))->unique()->values();
        $purchasedProducts = Product::with(['brand', 'category'])
            ->whereIn('id', $purchasedProductIds)
            ->get();

        // Lấy danh sách tất cả sản phẩm nổi bật để khách khám phá nhanh
        $featuredProducts = Product::with(['brand', 'category'])
            ->where('stock', '>', 0)
            ->latest()
            ->take(6)
            ->get();

        // Lấy danh sách các Voucher hiện có của Shop dành cho khách hàng
        $activeCoupons = Coupon::where('is_active', true)
            ->orderByDesc('discount_percent')
            ->get();

        $appliedCoupon = session('applied_coupon');

        return view('customer.account', compact(
            'user',
            'savedCart',
            'savedCartTotal',
            'allMyOrders',
            'myOrders',
            'statusFilter',
            'purchasedProducts',
            'featuredProducts',
            'activeCoupons',
            'appliedCoupon'
        ));
    }

    /**
     * Áp dụng nhanh mã giảm giá từ Trung tâm tài khoản vào giỏ hàng.
     *
     * Chức năng:
     * - Kiểm tra tính hợp lệ và thời hạn của voucher.
     * - Lưu mã giảm giá vào session và chuyển thẳng đến trang thanh toán.
     *
     * @param Request $request Yêu cầu HTTP chứa 'coupon_code'
     * @return RedirectResponse Chuyển hướng đến trang thanh toán (/thanh-toan)
     */
    public function applyVoucherToCart(Request $request): RedirectResponse
    {
        $code = strtoupper(trim((string) $request->input('coupon_code', '')));
        $existingCoupon = session()->get('applied_coupon');

        if (!empty($existingCoupon['code'])) {
            if ($existingCoupon['code'] === $code) {
                return redirect()->route('checkout.index')
                    ->with('success', "Mã giảm giá {$code} đang được áp dụng trong giỏ hàng của bạn.");
            }

            return back()->with(
                'error',
                "Mỗi đơn hàng chỉ được áp dụng 01 mã giảm giá (hiện đang dùng mã {$existingCoupon['code']}). Vui lòng gỡ mã cũ trước khi áp dụng mã {$code}."
            );
        }

        $coupon = Coupon::where('code', $code)->where('is_active', true)->first();

        if (!$coupon || $coupon->isExpired()) {
            return back()->with('error', "Mã voucher {$code} không khả dụng.");
        }

        $cart = session()->get('cart', []);
        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        if ($subtotal > 0 && $subtotal < ($coupon->min_order_amount ?? 0)) {
            return back()->with(
                'error',
                "Mã giảm giá {$coupon->code} (-{$coupon->discount_percent}%) yêu cầu giá trị đơn hàng tối thiểu " . number_format($coupon->min_order_amount, 0, ',', '.') . '₫.'
            );
        }

        session()->put('applied_coupon', [
            'code' => $coupon->code,
            'discount_percent' => $coupon->discount_percent,
        ]);

        return redirect()->route('checkout.index')
            ->with('success', "Đã lưu và áp dụng mã giảm giá {$coupon->code} (-{$coupon->discount_percent}%) vào giỏ hàng của bạn!");
    }
}
