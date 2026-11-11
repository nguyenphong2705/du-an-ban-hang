<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
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
                $this->applyRuntimeGmailConfig();
                Mail::mailer('smtp')->raw(
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
        return $this->renderCustomerAccountSection($request, 'overview');
    }

    /**
     * Trang riêng: Đổi Thông Tin & Xác Thực Email (/tai-khoan/thong-tin).
     */
    public function profile(Request $request)
    {
        return $this->renderCustomerAccountSection($request, 'profile');
    }

    /**
     * Trang riêng: Sổ Địa Chỉ Nhận Hàng (/tai-khoan/dia-chi).
     */
    public function addresses(Request $request)
    {
        return $this->renderCustomerAccountSection($request, 'addresses');
    }

    /**
     * Trang riêng: Giỏ Hàng Đã Lưu (/tai-khoan/gio-hang).
     */
    public function savedCartPage(Request $request)
    {
        return $this->renderCustomerAccountSection($request, 'cart');
    }

    /**
     * Trang riêng: Đơn Hàng Của Tôi (/tai-khoan/don-hang).
     */
    public function orders(Request $request)
    {
        return $this->renderCustomerAccountSection($request, 'orders');
    }

    /**
     * Trang riêng: Kho Voucher Ưu Đãi (/tai-khoan/voucher).
     */
    public function vouchers(Request $request)
    {
        return $this->renderCustomerAccountSection($request, 'vouchers');
    }

    /**
     * Hiển thị từng trang danh mục riêng biệt trong phần Tài khoản khách hàng.
     */
    private function renderCustomerAccountSection(Request $request, string $activeSection): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('customer.login')
                ->with('error', 'Vui lòng đăng nhập tài khoản khách hàng để theo dõi đơn hàng, giỏ hàng & voucher.');
        }

        $user = Auth::user();
        $user->ensureAddressesInitialized();

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

        // Sổ địa chỉ đã lưu của khách hàng
        $addresses = $user->addresses()->get();

        // Danh sách sản phẩm yêu thích của khách hàng
        $wishlistProducts = $user->wishlistProducts()
            ->with(['brand', 'category'])
            ->get();

        $smtpConfig = $this->applyRuntimeGmailConfig();
        $gmailSmtpConfigured = !empty($smtpConfig['username']) && !empty($smtpConfig['password']);
        $gmailSmtpSender = $smtpConfig['username'] ?? '';

        return view('customer.account', compact(
            'user',
            'activeSection',
            'savedCart',
            'savedCartTotal',
            'allMyOrders',
            'myOrders',
            'statusFilter',
            'purchasedProducts',
            'featuredProducts',
            'activeCoupons',
            'appliedCoupon',
            'addresses',
            'wishlistProducts',
            'gmailSmtpConfigured',
            'gmailSmtpSender'
        ));
    }

    /**
     * Cập nhật thông tin cá nhân của khách hàng: Họ tên, SĐT, Email (có xác nhận Email).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('customer.login');
        }

        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(0|\+84)[0-9]{8,10}$/'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'email_confirmation' => ['required', 'email', 'same:email'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'phone.required' => 'Vui lòng nhập số điện thoại liên hệ.',
            'phone.regex' => 'Số điện thoại không hợp lệ (VD: 0909123456).',
            'email.required' => 'Vui lòng nhập địa chỉ Email.',
            'email.email' => 'Định dạng Email không hợp lệ.',
            'email.unique' => 'Địa chỉ Email này đã được sử dụng bởi tài khoản khác.',
            'email_confirmation.required' => 'Vui lòng nhập lại Email để xác nhận.',
            'email_confirmation.same' => 'Xác nhận Email không khớp với Email đã nhập.',
        ]);

        $newEmail = trim($validated['email']);
        $emailChanged = strcasecmp($newEmail, (string) $user->email) !== 0;

        $updateData = [
            'name' => trim($validated['name']),
            'phone' => preg_replace('/[^0-9+]/', '', $validated['phone']),
            'email' => $newEmail,
        ];

        if ($emailChanged) {
            $updateData['email_verified_at'] = null;
        }

        $user->update($updateData);

        if ($emailChanged) {
            $this->dispatchEmailVerificationCode($user);

            return redirect()->route('customer.profile')
                ->with('success', "Đã cập nhật thông tin cá nhân! Mã xác thực 6 số đã được gửi về hộp thư Gmail ({$user->email}). Vui lòng mở Hộp thư đến Gmail bên dưới để lấy mã xác nhận.");
        }

        return redirect()->route('customer.profile')
            ->with('success', 'Đã cập nhật thông tin tài khoản (Họ tên, Số điện thoại & Email) thành công!');
    }

    /**
     * Gửi mã OTP 6 chữ số trực tiếp về hộp thư Gmail của khách hàng.
     */
    public function sendEmailVerificationOtp(Request $request): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('customer.login');
        }

        /** @var User $user */
        $user = Auth::user();
        $this->dispatchEmailVerificationCode($user);

        return redirect()->route('customer.profile')
            ->with('success', "Đã gửi mã xác thực 6 số về hộp thư Gmail ({$user->email}). Vui lòng bấm \"Mở Hộp Thư Đến Gmail\" bên dưới để xem thư và lấy mã 6 số!");
    }

    /**
     * Hiển thị giao diện Hộp thư đến Gmail của khách hàng chứa thư mã xác thực OTP 6 số.
     */
    public function showGmailInbox(Request $request): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('customer.login');
        }

        /** @var User $user */
        $user = Auth::user();
        $otpSession = session('email_verification_otp');

        if (!$otpSession || (int) ($otpSession['user_id'] ?? 0) !== (int) $user->id) {
            $this->dispatchEmailVerificationCode($user);
            $otpSession = session('email_verification_otp');
        }

        $email = $otpSession['email'] ?? $user->email;

        return view('customer.gmail-inbox', compact('user', 'email', 'otpSession'));
    }

    /**
     * Lưu cấu hình Gmail SMTP (MAIL_USERNAME & Google App Password 16 ký tự) và gửi ngay mã OTP về Gmail.
     */
    public function configureGmailSmtp(Request $request): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('customer.login');
        }

        $validated = $request->validate([
            'smtp_email' => ['required', 'email', 'max:255'],
            'smtp_app_password' => ['required', 'string', 'min:8', 'max:100'],
        ], [
            'smtp_email.required' => 'Vui lòng nhập địa chỉ Gmail dùng để gửi mã (MAIL_USERNAME).',
            'smtp_email.email' => 'Địa chỉ Gmail không hợp lệ.',
            'smtp_app_password.required' => 'Vui lòng nhập Mật khẩu ứng dụng Google 16 ký tự (App Password).',
        ]);

        $smtpEmail = trim($validated['smtp_email']);
        // Loại bỏ khoảng trắng trong mật khẩu ứng dụng Google (VD: "abcd efgh ijkl mnop" -> "abcdefghijklmnop")
        $smtpAppPassword = preg_replace('/\s+/', '', trim($validated['smtp_app_password']));

        $configPath = storage_path('app/gmail_smtp.json');
        @file_put_contents($configPath, json_encode([
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'username' => $smtpEmail,
            'password' => $smtpAppPassword,
            'from_address' => $smtpEmail,
            'from_name' => 'Shine Beauty',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->persistMailEnvVariables($smtpEmail, $smtpAppPassword);

        /** @var User $user */
        $user = Auth::user();
        $result = $this->dispatchEmailVerificationCode($user);

        if (!$result['sent']) {
            return redirect()->route('customer.profile')
                ->with('error', "Đã lưu cấu hình nhưng gửi thử tới Gmail ({$user->email}) thất bại: {$result['error']}");
        }

        return redirect()->route('customer.profile')
            ->with('success', "Đã kết nối Gmail SMTP ({$smtpEmail}) và gửi mã xác thực 6 số về hộp thư Gmail ({$user->email}) thành công! Vui lòng kiểm tra hộp thư Gmail của bạn.");
    }

    /**
     * Xác thực mã OTP 6 chữ số để đánh dấu Email đã được xác nhận (email_verified_at).
     */
    public function verifyEmailOtp(Request $request): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('customer.login');
        }

        $validated = $request->validate([
            'email_otp' => ['required', 'string', 'size:6'],
        ], [
            'email_otp.required' => 'Vui lòng nhập mã xác nhận Email 6 số.',
            'email_otp.size' => 'Mã xác nhận Email phải gồm đúng 6 chữ số.',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $otpState = session('email_verification_otp');

        if (
            !$otpState
            || (int) ($otpState['user_id'] ?? 0) !== (int) $user->id
            || now()->timestamp > (int) ($otpState['expires_at'] ?? 0)
        ) {
            session()->forget('email_verification_otp');
            return redirect()->route('customer.profile')
                ->with('error', 'Mã xác nhận Email đã hết hạn hoặc chưa được yêu cầu. Vui lòng bấm "Gửi mã xác nhận về Gmail".');
        }

        if (trim($validated['email_otp']) !== (string) $otpState['code']) {
            return redirect()->route('customer.profile')
                ->with('error', 'Mã xác nhận Email không chính xác. Vui lòng kiểm tra lại hộp thư Gmail của bạn.');
        }

        $user->update([
            'email_verified_at' => now(),
        ]);
        session()->forget('email_verification_otp');

        return redirect()->route('customer.profile')
            ->with('success', "Xác nhận địa chỉ Email ({$user->email}) thành công! ✓");
    }

    /**
     * Đọc cấu hình Gmail SMTP trực tiếp từ .env và storage/app/gmail_smtp.json
     * rồi nạp vào runtime config của Laravel Mailer mà không cần khởi động lại Docker.
     */
    private function applyRuntimeGmailConfig(): array
    {
        $envVars = [];
        $envFile = base_path('.env');
        if (is_file($envFile) && is_readable($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed === '' || str_starts_with($trimmed, '#') || !str_contains($trimmed, '=')) {
                    continue;
                }
                [$key, $val] = explode('=', $trimmed, 2);
                $key = trim($key);
                $val = trim($val);
                if (
                    (str_starts_with($val, '"') && str_ends_with($val, '"'))
                    || (str_starts_with($val, "'") && str_ends_with($val, "'"))
                ) {
                    $val = substr($val, 1, -1);
                }
                $envVars[$key] = $val;
            }
        }

        $host = $envVars['MAIL_HOST'] ?? env('MAIL_HOST', 'smtp.gmail.com');
        $port = (int) ($envVars['MAIL_PORT'] ?? env('MAIL_PORT', 587));
        $username = trim((string) ($envVars['MAIL_USERNAME'] ?? env('MAIL_USERNAME', '')));
        $password = preg_replace('/\s+/', '', trim((string) ($envVars['MAIL_PASSWORD'] ?? env('MAIL_PASSWORD', ''))));
        $fromAddress = trim((string) ($envVars['MAIL_FROM_ADDRESS'] ?? env('MAIL_FROM_ADDRESS', '')));
        $fromName = trim((string) ($envVars['MAIL_FROM_NAME'] ?? env('MAIL_FROM_NAME', 'Shine Beauty')));

        $jsonPath = storage_path('app/gmail_smtp.json');
        if (is_file($jsonPath) && is_readable($jsonPath)) {
            $saved = json_decode((string) file_get_contents($jsonPath), true);
            if (is_array($saved)) {
                if ($username === '' && !empty($saved['username'])) {
                    $username = trim((string) $saved['username']);
                }
                if ($password === '' && !empty($saved['password'])) {
                    $password = preg_replace('/\s+/', '', trim((string) $saved['password']));
                }
            }
        }

        if ($fromAddress === '' || $fromAddress === 'no-reply@shinebeauty.vn' || $fromAddress === 'hello@example.com') {
            if ($username !== '') {
                $fromAddress = $username;
            }
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => $host ?: 'smtp.gmail.com',
            'mail.mailers.smtp.port' => $port ?: 587,
            'mail.mailers.smtp.username' => $username,
            'mail.mailers.smtp.password' => $password,
            'mail.mailers.smtp.timeout' => 15,
            'mail.from.address' => $fromAddress ?: 'no-reply@shinebeauty.vn',
            'mail.from.name' => $fromName ?: 'Shine Beauty',
        ]);

        try {
            Mail::purge('smtp');
        } catch (\Throwable $e) {
            // Ignore purge error if mailer not yet resolved
        }

        return [
            'host' => $host ?: 'smtp.gmail.com',
            'port' => $port ?: 587,
            'username' => $username,
            'password' => $password,
            'from_address' => $fromAddress ?: $username,
            'from_name' => $fromName ?: 'Shine Beauty',
        ];
    }

    /**
     * Lưu MAIL_USERNAME và MAIL_PASSWORD vào file .env.
     */
    private function persistMailEnvVariables(string $smtpEmail, string $smtpAppPassword): void
    {
        $envFile = base_path('.env');
        if (!is_file($envFile) || !is_writable($envFile)) {
            return;
        }

        $content = (string) file_get_contents($envFile);
        $replacements = [
            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => 'smtp.gmail.com',
            'MAIL_PORT' => '587',
            'MAIL_USERNAME' => $smtpEmail,
            'MAIL_PASSWORD' => $smtpAppPassword,
            'MAIL_ENCRYPTION' => 'tls',
            'MAIL_FROM_ADDRESS' => '"' . $smtpEmail . '"',
            'MAIL_FROM_NAME' => '"Shine Beauty"',
        ];

        foreach ($replacements as $key => $value) {
            $pattern = "/^{$key}=.*$/m";
            if (preg_match($pattern, $content)) {
                $content = (string) preg_replace($pattern, "{$key}={$value}", $content);
            } else {
                $content = rtrim($content) . "\n{$key}={$value}\n";
            }
        }

        @file_put_contents($envFile, $content);
    }

    /**
     * Sinh mã OTP 6 chữ số và gửi email HTML trực tiếp tới hộp thư Gmail của khách hàng.
     *
     * @return array{code: string, sent: bool, smtp_sent: bool, error: string|null}
     */
    private function dispatchEmailVerificationCode(User $user): array
    {
        $code = (string) random_int(100000, 999999);
        $smtpConfig = $this->applyRuntimeGmailConfig();

        $escapedName = e($user->name);
        $escapedEmail = e($user->email);
        $htmlBody = <<<HTML
        <div style="font-family: 'Helvetica Neue', Arial, sans-serif; max-width: 560px; margin: 0 auto; background-color: #FAF9F6; padding: 32px 24px; border-radius: 20px; border: 1px solid #E2ECE7; color: #292524;">
            <div style="text-align: center; margin-bottom: 24px;">
                <span style="display: inline-block; font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #6B8E7B; background: #E8F2EC; padding: 6px 14px; border-radius: 999px;">
                    Shine Beauty • Xác Thực Tài Khoản
                </span>
                <h2 style="margin: 12px 0 4px; font-size: 22px; color: #1C1917;">Mã Xác Thực Địa Chỉ Email</h2>
                <p style="margin: 0; font-size: 13px; color: #78716C;">Gửi tới hộp thư: <strong>{$escapedEmail}</strong></p>
            </div>

            <div style="background-color: #FFFFFF; border-radius: 16px; padding: 24px; border: 1px solid #E7E5E4; text-align: center;">
                <p style="margin: 0 0 14px; font-size: 14px; color: #44403C; line-height: 1.6;">
                    Xin chào <strong>{$escapedName}</strong>,<br>
                    Bạn vừa yêu cầu xác thực địa chỉ Email cho tài khoản khách hàng tại <strong>Shine Beauty</strong>. Đây là mã xác nhận 6 chữ số của bạn:
                </p>

                <div style="margin: 20px auto; display: inline-block; background-color: #FDF5F2; border: 2px dashed #C85A32; border-radius: 14px; padding: 14px 32px;">
                    <span style="font-family: 'Courier New', monospace; font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #B04A25;">
                        {$code}
                    </span>
                </div>

                <p style="margin: 14px 0 0; font-size: 12px; color: #78716C;">
                    ⏳ Mã xác thực có hiệu lực trong vòng <strong>15 phút</strong>. Vui lòng không chia sẻ mã này cho bất kỳ ai.
                </p>
            </div>

            <p style="margin: 20px 0 0; text-align: center; font-size: 12px; color: #A8A29E;">
                © Shine Beauty Cosmetics — Chăm sóc làn da Việt chính hãng.
            </p>
        </div>
        HTML;

        $smtpSent = false;
        $smtpError = null;

        if (!empty($smtpConfig['username']) && !empty($smtpConfig['password'])) {
            try {
                Mail::mailer('smtp')->html($htmlBody, function ($message) use ($user, $code) {
                    $message->to($user->email, $user->name)
                        ->subject("[Shine Beauty] Mã xác thực Email của bạn: {$code}");
                });
                $smtpSent = true;
            } catch (\Throwable $e) {
                Log::warning('Send email verification OTP via Gmail SMTP failed: ' . $e->getMessage());
                $smtpError = 'Không thể kết nối Gmail SMTP (' . $e->getMessage() . '). Hãy kiểm tra lại địa chỉ Gmail và Mật khẩu ứng dụng Google 16 ký tự (App Password).';
            }
        } else {
            // Ghi nhận thư vào hệ thống Hộp thư Gmail nội bộ & Log Mailer khi chưa cấu hình App Password ngoài
            try {
                Mail::mailer('log')->html($htmlBody, function ($message) use ($user, $code) {
                    $message->to($user->email, $user->name)
                        ->subject("[Shine Beauty] Mã xác thực Email của bạn: {$code}");
                });
            } catch (\Throwable $e) {
                // Ignore log mailer error
            }
        }

        session([
            'email_verification_otp' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'code' => $code,
                'sent_at' => now()->format('H:i d/m/Y'),
                'expires_at' => now()->addMinutes(15)->timestamp,
                'mail_sent' => true,
                'smtp_sent' => $smtpSent,
                'mail_error' => $smtpError,
            ],
        ]);

        return [
            'code' => $code,
            'sent' => $smtpError === null,
            'smtp_sent' => $smtpSent,
            'error' => $smtpError,
        ];
    }

    /**
     * Thêm địa chỉ nhận hàng mới vào Sổ địa chỉ của khách hàng.
     */
    public function storeAddress(Request $request): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('customer.login');
        }

        /** @var User $user */
        $user = Auth::user();
        $user->ensureAddressesInitialized();

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'recipient_phone' => ['nullable', 'string', 'max:30'],
            'street_address' => ['required', 'string', 'max:300'],
            'ward' => ['required', 'string', 'max:150'],
            'district' => ['required', 'string', 'max:150'],
            'province' => ['required', 'string', 'max:150'],
            'is_default' => ['nullable', 'boolean'],
        ], [
            'street_address.required' => 'Vui lòng nhập số nhà, tên đường.',
            'ward.required' => 'Vui lòng nhập Phường/Xã.',
            'district.required' => 'Vui lòng nhập Quận/Huyện.',
            'province.required' => 'Vui lòng nhập Tỉnh/Thành phố.',
        ]);

        $parts = array_filter([
            trim($validated['street_address']),
            trim($validated['ward']),
            trim($validated['district']),
            trim($validated['province']),
        ]);
        $fullAddress = implode(', ', $parts);

        $isDefault = $request->boolean('is_default') || $user->addresses()->count() === 0;

        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
            $user->update(['address' => $fullAddress]);
        }

        $user->addresses()->create([
            'label' => trim($validated['label']),
            'recipient_name' => trim((string) ($validated['recipient_name'] ?? '')) ?: $user->name,
            'recipient_phone' => trim((string) ($validated['recipient_phone'] ?? '')) ?: $user->phone,
            'street_address' => trim($validated['street_address']),
            'ward' => trim($validated['ward']),
            'district' => trim($validated['district']),
            'province' => trim($validated['province']),
            'address' => $fullAddress,
            'is_default' => $isDefault,
        ]);

        return redirect()->route('customer.addresses')
            ->with('success', 'Đã thêm địa chỉ nhận hàng mới vào Sổ địa chỉ của bạn!');
    }

    /**
     * Cập nhật một địa chỉ nhận hàng trong Sổ địa chỉ.
     */
    public function updateAddress(Request $request, UserAddress $address): RedirectResponse
    {
        if (!Auth::check() || (int) $address->user_id !== (int) Auth::id()) {
            abort(403);
        }

        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'recipient_phone' => ['nullable', 'string', 'max:30'],
            'street_address' => ['required', 'string', 'max:300'],
            'ward' => ['required', 'string', 'max:150'],
            'district' => ['required', 'string', 'max:150'],
            'province' => ['required', 'string', 'max:150'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $parts = array_filter([
            trim($validated['street_address']),
            trim($validated['ward']),
            trim($validated['district']),
            trim($validated['province']),
        ]);
        $fullAddress = implode(', ', $parts);

        $makeDefault = $request->boolean('is_default') || $address->is_default;

        if ($makeDefault) {
            $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            $user->update(['address' => $fullAddress]);
        }

        $address->update([
            'label' => trim($validated['label']),
            'recipient_name' => trim((string) ($validated['recipient_name'] ?? '')) ?: $user->name,
            'recipient_phone' => trim((string) ($validated['recipient_phone'] ?? '')) ?: $user->phone,
            'street_address' => trim($validated['street_address']),
            'ward' => trim($validated['ward']),
            'district' => trim($validated['district']),
            'province' => trim($validated['province']),
            'address' => $fullAddress,
            'is_default' => $makeDefault,
        ]);

        return redirect()->route('customer.addresses')
            ->with('success', "Đã cập nhật địa chỉ \"{$address->label}\" thành công!");
    }

    /**
     * Đặt một địa chỉ làm Địa chỉ Mặc Định của khách hàng.
     */
    public function setDefaultAddress(Request $request, UserAddress $address): RedirectResponse|JsonResponse
    {
        if (!Auth::check() || (int) $address->user_id !== (int) Auth::id()) {
            abort(403);
        }

        /** @var User $user */
        $user = Auth::user();

        $user->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);
        $user->update(['address' => $address->address]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'ok',
                'message' => "Đã đặt \"{$address->label}\" làm địa chỉ nhận hàng mặc định!",
                'address' => $address,
            ]);
        }

        if ($request->boolean('from_checkout')) {
            return redirect()->route('checkout.index')
                ->with('success', "Đã chọn và đặt \"{$address->label}\" ({$address->address}) làm địa chỉ mặc định!");
        }

        return redirect()->route('customer.addresses')
            ->with('success', "Đã đặt \"{$address->label}\" làm địa chỉ nhận hàng mặc định!");
    }

    /**
     * Xóa một địa chỉ khỏi Sổ địa chỉ của khách hàng.
     */
    public function destroyAddress(Request $request, UserAddress $address): RedirectResponse
    {
        if (!Auth::check() || (int) $address->user_id !== (int) Auth::id()) {
            abort(403);
        }

        /** @var User $user */
        $user = Auth::user();

        if ($user->addresses()->count() <= 1) {
            return redirect()->route('customer.addresses')
                ->with('error', 'Bạn cần giữ lại ít nhất 1 địa chỉ mặc định trong sổ địa chỉ.');
        }

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $nextAddress = $user->addresses()->first();
            if ($nextAddress) {
                $nextAddress->update(['is_default' => true]);
                $user->update(['address' => $nextAddress->address]);
            }
        }

        return redirect()->route('customer.addresses')
            ->with('success', 'Đã xóa địa chỉ khỏi sổ địa chỉ.');
    }

    /**
     * Trang Danh mục mới: Yêu Thích (/tai-khoan/yeu-thich) trong phần tài khoản của khách hàng.
     */
    public function wishlist(Request $request): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('customer.login')
                ->with('error', 'Vui lòng đăng nhập tài khoản khách hàng để xem danh mục sản phẩm Yêu Thích.');
        }

        /** @var User $user */
        $user = Auth::user();
        $user->ensureAddressesInitialized();

        $wishlistProducts = $user->wishlistProducts()
            ->with(['brand', 'category'])
            ->get();

        $savedCart = session('cart', []);
        $savedCartTotal = collect($savedCart)->sum(fn ($i) => $i['price'] * $i['quantity']);
        $ordersCount = Order::where('user_id', $user->id)->orWhere('phone', $user->phone)->count();
        $couponsCount = Coupon::where('is_active', true)->count();

        return view('customer.wishlist', compact(
            'user',
            'wishlistProducts',
            'savedCart',
            'savedCartTotal',
            'ordersCount',
            'couponsCount'
        ));
    }

    /**
     * Bật / tắt trạng thái Yêu thích sản phẩm (Nút trái tim).
     */
    public function toggleWishlist(Request $request): RedirectResponse|JsonResponse
    {
        if (!Auth::check()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'unauthenticated',
                    'message' => 'Vui lòng đăng nhập tài khoản để thêm sản phẩm vào danh mục Yêu Thích.',
                ], 401);
            }

            return redirect()->route('customer.login')
                ->with('error', 'Vui lòng đăng nhập tài khoản để thêm sản phẩm vào danh mục Yêu Thích.');
        }

        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $userId = (int) Auth::id();
        $productId = (int) $validated['product_id'];
        $product = Product::find($productId);

        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $favorited = false;
            $message = "Đã bỏ \"{$product?->name}\" khỏi danh mục Yêu Thích.";
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
            $favorited = true;
            $message = "Đã thêm \"{$product?->name}\" vào danh mục Yêu Thích! ❤️";
        }

        $wishlistCount = Wishlist::where('user_id', $userId)->count();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'ok',
                'favorited' => $favorited,
                'count' => $wishlistCount,
                'product_id' => $productId,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
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
