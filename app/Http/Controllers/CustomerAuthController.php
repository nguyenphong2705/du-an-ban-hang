<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomerAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && !Auth::user()->is_admin) {
            return redirect()->route('customer.account');
        }

        return view('customer.login');
    }

    public function login(Request $request)
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

    public function register(Request $request)
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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login')
            ->with('success', 'Đã đăng xuất khỏi tài khoản khách hàng.');
    }

    public function showForgotPassword()
    {
        $resetState = session('password_reset_otp');
        return view('customer.forgot-password', compact('resetState'));
    }

    public function sendResetCode(Request $request)
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

    public function resetPasswordWithCode(Request $request)
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

    public function applyVoucherToCart(Request $request)
    {
        $code = strtoupper(trim($request->input('coupon_code', '')));
        $coupon = Coupon::where('code', $code)->where('is_active', true)->first();

        if (!$coupon || $coupon->isExpired()) {
            return back()->with('error', "Mã voucher {$code} không khả dụng.");
        }

        session()->put('applied_coupon', [
            'code' => $coupon->code,
            'discount_percent' => $coupon->discount_percent,
        ]);

        return redirect()->route('checkout.index')
            ->with('success', "Đã lưu và áp dụng mã giảm giá {$coupon->code} (-{$coupon->discount_percent}%) vào giỏ hàng của bạn!");
    }
}
