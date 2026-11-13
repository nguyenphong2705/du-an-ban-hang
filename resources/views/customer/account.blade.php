@extends('layouts.app')

@php
    $currentTab = $activeSection ?? 'overview';
    $pageTitles = [
        'overview'  => 'Tổng Quan Tài Khoản Khách Hàng | Shine Beauty',
        'profile'   => 'Đổi Thông Tin Cá Nhân & Xác Thực Email | Shine Beauty',
        'addresses' => 'Sổ Địa Chỉ Nhận Hàng Mặc Định | Shine Beauty',
        'cart'      => 'Giỏ Hàng Đã Lưu Của Tôi | Shine Beauty',
        'orders'    => 'Lịch Sử Đơn Hàng Đã Đặt | Shine Beauty',
        'vouchers'  => 'Kho Mã Giảm Giá & Voucher Ưu Đãi | Shine Beauty',
    ];
@endphp

@section('title', $pageTitles[$currentTab] ?? 'Trung Tâm Tài Khoản Khách Hàng | Shine Beauty')
@section('meta_robots', 'noindex, nofollow')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
    <!-- Customer Header Banner -->
    <div class="bg-white rounded-3xl border border-sage-100 p-6 sm:p-8 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-sage-500 to-sage-300 text-white flex items-center justify-center font-serif text-2xl font-bold shadow-md shadow-sage-200">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[11px] font-bold uppercase tracking-widest bg-sage-50 text-sage-600 border border-sage-200 px-2.5 py-0.5 rounded-full">
                        Khách Hàng Thân Thiết
                    </span>
                    <span class="text-xs font-mono text-stone-500">@<span>{{ $user->username }}</span></span>
                    @if($user->email_verified_at)
                        <span class="text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                            ✓ Email đã xác nhận
                        </span>
                    @else
                        <span class="text-[11px] font-bold bg-terracotta-50 text-terracotta-700 border border-terracotta-200 px-2.5 py-0.5 rounded-full">
                            ⏳ Email chưa xác nhận
                        </span>
                    @endif
                </div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-stone-800">{{ $user->name }}</h1>
                <p class="text-xs text-stone-500">
                    📧 {{ $user->email }} • 📞 {{ $user->phone ?? 'Chưa cập nhật' }} • 📍 Mặc định: {{ $user->address ?? 'TP. Hồ Chí Minh' }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('customer.wishlist') }}" class="text-xs font-semibold bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-4 py-2.5 rounded-full transition">
                ❤️ Danh Mục Yêu Thích (<span data-wishlist-count>{{ $wishlistProducts->count() }}</span>)
            </a>
            <a href="{{ route('products.index') }}" class="text-xs font-semibold bg-sage-50 hover:bg-sage-100 text-sage-700 border border-sage-200 px-4 py-2.5 rounded-full transition">
                🛍️ Tiếp tục mua sắm
            </a>
            <form action="{{ route('customer.logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-xs font-semibold bg-stone-900 hover:bg-red-600 text-white px-4 py-2.5 rounded-full transition cursor-pointer">
                    Đăng xuất tài khoản
                </button>
            </form>
        </div>
    </div>

    @php
        $currentTab = $activeSection ?? 'overview';
    @endphp

    <!-- Thanh Danh Mục Trong Phần Tài Khoản Khách Hàng (Mỗi danh mục là 1 trang riêng biệt) -->
    <nav class="bg-white rounded-2xl border border-sage-100 p-2 shadow-2xs flex flex-wrap items-center gap-2 text-xs font-bold">
        <a href="{{ route('customer.account') }}"
            class="px-4 py-2.5 rounded-xl transition {{ $currentTab === 'overview' ? 'bg-sage-500 text-white shadow-xs' : 'text-stone-600 hover:bg-sage-50 hover:text-sage-700' }}">
            👤 Tổng Quan Tài Khoản
        </a>
        <a href="{{ route('customer.profile') }}"
            class="px-4 py-2.5 rounded-xl transition {{ $currentTab === 'profile' ? 'bg-sage-500 text-white shadow-xs' : 'text-stone-600 hover:bg-sage-50 hover:text-sage-700' }}">
            ✏️ Đổi Thông Tin & Email
        </a>
        <a href="{{ route('customer.addresses') }}"
            class="px-4 py-2.5 rounded-xl transition {{ $currentTab === 'addresses' ? 'bg-sage-500 text-white shadow-xs' : 'text-stone-600 hover:bg-sage-50 hover:text-sage-700' }}">
            📍 Sổ Địa Chỉ ({{ $addresses->count() }})
        </a>
        <a href="{{ route('customer.wishlist') }}"
            class="px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-500 text-red-600 hover:text-white border border-red-200 flex items-center gap-1.5 transition">
            <span>❤️ Yêu Thích</span>
            <span data-wishlist-count class="bg-red-500 text-white px-2 py-0.5 rounded-full text-[11px]">{{ $wishlistProducts->count() }}</span>
        </a>
        <a href="{{ route('customer.cart') }}"
            class="px-4 py-2.5 rounded-xl transition {{ $currentTab === 'cart' ? 'bg-sage-500 text-white shadow-xs' : 'text-stone-600 hover:bg-sage-50 hover:text-sage-700' }}">
            🛒 Giỏ Hàng ({{ collect($savedCart)->sum('quantity') }})
        </a>
        <a href="{{ route('customer.orders') }}"
            class="px-4 py-2.5 rounded-xl transition {{ $currentTab === 'orders' ? 'bg-sage-500 text-white shadow-xs' : 'text-stone-600 hover:bg-sage-50 hover:text-sage-700' }}">
            📦 Đơn Hàng ({{ $allMyOrders->count() }})
        </a>
        <a href="{{ route('customer.vouchers') }}"
            class="px-4 py-2.5 rounded-xl transition {{ $currentTab === 'vouchers' ? 'bg-terracotta-600 text-white shadow-xs' : 'text-stone-600 hover:bg-terracotta-50 hover:text-terracotta-700' }}">
            🎟️ Kho Voucher ({{ $activeCoupons->count() }})
        </a>
    </nav>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm font-medium">
            ⚠ {{ $errors->first() }}
        </div>
    @endif

    @if($currentTab === 'overview')
    <!-- Quick Summary Cards (Chỉ hiển thị ở trang Tổng Quan Tài Khoản) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <a href="{{ route('customer.profile') }}" class="bg-white p-5 rounded-3xl border border-sage-100 shadow-xs hover:border-sage-300 transition space-y-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Hồ Sơ & Xác Thực Email</span>
            <p class="text-xl font-bold text-stone-800 truncate">{{ $user->name }}</p>
            <p class="text-xs {{ $user->email_verified_at ? 'text-emerald-600' : 'text-terracotta-600' }} font-medium">
                {{ $user->email_verified_at ? '✓ Email đã xác thực OTP' : '⏳ Bấm để xác thực Email qua Gmail →' }}
            </p>
        </a>

        <a href="{{ route('customer.addresses') }}" class="bg-white p-5 rounded-3xl border border-sage-100 shadow-xs hover:border-sage-300 transition space-y-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Sổ Địa Chỉ Nhận Hàng</span>
            <p class="text-2xl font-bold text-stone-800">{{ $addresses->count() }} địa chỉ</p>
            <p class="text-xs text-sage-600 font-medium">Mở trang quản lý & chọn địa chỉ mặc định →</p>
        </a>

        <a href="{{ route('customer.wishlist') }}" class="bg-white p-5 rounded-3xl border border-red-100 shadow-xs hover:border-red-300 transition space-y-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-red-400">Danh Mục Mới: Yêu Thích</span>
            <p class="text-2xl font-bold text-red-500">❤️ <span data-wishlist-count>{{ $wishlistProducts->count() }}</span> sản phẩm</p>
            <p class="text-xs text-stone-500">Xem trang sản phẩm đã thả tim →</p>
        </a>

        <a href="{{ route('customer.cart') }}" class="bg-white p-5 rounded-3xl border border-sage-100 shadow-xs hover:border-sage-300 transition space-y-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Giỏ Hàng Đã Lưu</span>
            <p class="text-2xl font-bold text-sage-600">{{ collect($savedCart)->sum('quantity') }} sản phẩm</p>
            <p class="text-xs text-stone-500">Tạm tính: {{ number_format($savedCartTotal, 0, ',', '.') }}₫</p>
        </a>

        <a href="{{ route('customer.orders') }}" class="bg-white p-5 rounded-3xl border border-sage-100 shadow-xs hover:border-sage-300 transition space-y-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Đơn Hàng Đã Đặt</span>
            <p class="text-2xl font-bold text-stone-800">{{ $allMyOrders->count() }} đơn hàng</p>
            <p class="text-xs text-emerald-600 font-medium">
                Đang giao: {{ $allMyOrders->where('status', 'SHIPPING')->count() }} • Hoàn thành: {{ $allMyOrders->where('status', 'COMPLETED')->count() }}
            </p>
        </a>

        <a href="{{ route('customer.vouchers') }}" class="bg-white p-5 rounded-3xl border border-sage-100 shadow-xs hover:border-sage-300 transition space-y-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Kho Voucher Của Shop</span>
            <p class="text-2xl font-bold text-terracotta-600">{{ $activeCoupons->count() }} mã ưu đãi</p>
            <p class="text-xs text-stone-500">Giảm tối đa đến {{ $activeCoupons->max('discount_percent') ?? 30 }}% →</p>
        </a>
    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TRANG 1: ĐỔI THÔNG TIN KHÁCH HÀNG & XÁC THỰC EMAIL (/tai-khoan/thong-tin) -->
    <!-- ========================================================================= -->
    @if($currentTab === 'profile')
    <div>
        <section id="section-profile" class="bg-white rounded-3xl border border-sage-100 p-6 sm:p-8 shadow-xs space-y-6">
            <div class="border-b border-sage-100 pb-4">
                <span class="text-xs font-bold uppercase tracking-widest text-sage-500">Hồ Sơ Cá Nhân</span>
                <h2 class="font-serif text-2xl font-bold text-stone-800 mt-0.5">
                    ✏️ Thay Đổi Thông Tin Khách Hàng
                </h2>
                <p class="text-xs text-stone-500 mt-1">
                    Cập nhật Họ và tên, Số điện thoại liên hệ và địa chỉ Email (kèm xác nhận Email).
                </p>
            </div>

            <form action="{{ route('customer.profile.update') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-stone-600 mb-1.5">
                        Họ và tên khách hàng *
                    </label>
                    <input type="text" name="name" required value="{{ old('name', $user->name) }}"
                        placeholder="Nhập họ và tên của bạn"
                        class="w-full px-4 py-2.5 text-sm rounded-2xl border border-sage-200 bg-[#F5FAF7] focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-stone-600 mb-1.5">
                        Số điện thoại liên hệ *
                    </label>
                    <input type="tel" name="phone" required value="{{ old('phone', $user->phone) }}"
                        placeholder="VD: 0909397979"
                        class="w-full px-4 py-2.5 text-sm rounded-2xl border border-sage-200 bg-[#F5FAF7] focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-stone-600 mb-1.5">
                        Địa chỉ Email *
                    </label>
                    <input type="email" name="email" required value="{{ old('email', $user->email) }}"
                        placeholder="Nhập địa chỉ Email"
                        class="w-full px-4 py-2.5 text-sm rounded-2xl border border-sage-200 bg-[#F5FAF7] focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-stone-600 mb-1.5">
                        Xác nhận lại Email *
                    </label>
                    <input type="email" name="email_confirmation" required value="{{ old('email_confirmation', $user->email) }}"
                        placeholder="Nhập lại địa chỉ Email để xác nhận"
                        class="w-full px-4 py-2.5 text-sm rounded-2xl border border-sage-200 bg-[#F5FAF7] focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                </div>

                <button type="submit" class="w-full bg-sage-500 hover:bg-sage-600 text-white font-semibold py-3 rounded-2xl shadow-md shadow-sage-200 transition text-sm cursor-pointer">
                    💾 Lưu Thay Đổi Thông Tin Cá Nhân
                </button>
            </form>

            <!-- Khối Xác thực / Xác nhận địa chỉ Email bằng mã OTP gửi về hộp thư Gmail -->
            <div class="pt-4 border-t border-sage-100 space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold text-stone-700">📧 Trạng thái xác thực Email:</span>
                    @if($user->email_verified_at)
                        <span class="text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                            ✓ Đã xác nhận ({{ $user->email_verified_at->format('d/m/Y H:i') }})
                        </span>
                    @else
                        <span class="text-[11px] font-bold bg-terracotta-50 text-terracotta-700 border border-terracotta-200 px-2.5 py-0.5 rounded-full">
                            ⏳ Chưa xác thực OTP qua Gmail
                        </span>
                    @endif
                </div>

                @php
                    $emailOtpSession = session('email_verification_otp');
                    $hasOtpForCurrentUser = $emailOtpSession && (int)($emailOtpSession['user_id'] ?? 0) === (int)$user->id;
                    $otpMailSent = $hasOtpForCurrentUser && !empty($emailOtpSession['mail_sent']);
                    $otpMailError = $hasOtpForCurrentUser ? ($emailOtpSession['mail_error'] ?? null) : null;
                @endphp

                <div class="p-3.5 rounded-2xl bg-[#F5FAF7] border border-sage-200 space-y-3 text-xs">
                    @if($otpMailSent)
                        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-2.5 leading-relaxed">
                            <p>
                                📬 Đã gửi thư chứa mã xác thực 6 chữ số về hộp thư Gmail <strong>{{ $emailOtpSession['email'] }}</strong>.
                                Vui lòng mở hộp thư đến Gmail của bạn để lấy mã 6 số và nhập vào ô bên dưới:
                            </p>
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('customer.email.inbox') }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-3.5 py-2 rounded-xl shadow-xs transition">
                                    📩 Mở Hộp Thư Đến Gmail ({{ $emailOtpSession['email'] }}) ↗
                                </a>
                                <a href="https://mail.google.com/mail/u/0/#inbox" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1 bg-white hover:bg-emerald-100/60 text-emerald-800 border border-emerald-300 font-semibold text-xs px-3 py-2 rounded-xl transition">
                                    🌐 Mở mail.google.com ↗
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="text-stone-600 leading-relaxed">
                                Mã xác thực 6 chữ số sẽ được gửi trực tiếp về hộp thư Gmail <strong>{{ $user->email }}</strong> của bạn.
                            </p>
                            <a href="{{ route('customer.email.inbox') }}" target="_blank"
                               class="text-[11px] font-bold text-sage-600 hover:underline">
                                📩 Mở Hộp Thư Đến Gmail ({{ $user->email }}) ↗
                            </a>
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center gap-2">
                        <form action="{{ route('customer.email.send-otp') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-stone-900 hover:bg-sage-600 text-white text-xs font-semibold px-3.5 py-2 rounded-xl transition cursor-pointer">
                                📨 {{ $otpMailSent ? 'Gửi lại mã về Gmail' : 'Gửi mã xác nhận về Gmail' }}
                            </button>
                        </form>

                        <form action="{{ route('customer.email.verify') }}" method="POST" class="flex-1 flex items-center gap-1.5 min-w-[200px]">
                            @csrf
                            <input type="text" name="email_otp" maxlength="6" required
                                value=""
                                autocomplete="one-time-code"
                                inputmode="numeric"
                                placeholder="Nhập mã 6 số trong Gmail"
                                class="w-full px-3 py-2 text-xs font-mono tracking-widest font-bold rounded-xl border border-sage-200 bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3.5 py-2 rounded-xl transition flex-shrink-0 cursor-pointer">
                                ✓ Xác nhận
                            </button>
                        </form>
                    </div>

                    <!-- Khối cấu hình tùy chọn Gmail SMTP (ẩn mặc định để không gây vướng) -->
                    <div class="pt-2 border-t border-sage-200/70">
                        <button type="button"
                            onclick="document.getElementById('gmail-smtp-config-box').classList.toggle('hidden')"
                            class="text-[11px] font-semibold text-stone-500 hover:text-terracotta-600 flex items-center gap-1 cursor-pointer">
                            ⚙ {{ !empty($gmailSmtpConfigured) ? 'Đã kết nối SMTP (' . $gmailSmtpSender . ')' : 'Tùy chọn: Cấu hình SMTP Google App Password riêng' }}
                        </button>

                        <form id="gmail-smtp-config-box"
                            action="{{ route('customer.email.configure-smtp') }}"
                            method="POST"
                            class="{{ $otpMailError ? '' : 'hidden' }} mt-2.5 p-3 rounded-xl bg-white border border-terracotta-200 space-y-2.5">
                            @csrf
                            <p class="text-[11px] text-stone-600 leading-relaxed">
                                Để hệ thống gửi thư thật qua máy chủ <code>smtp.gmail.com</code> về hộp thư Gmail, hãy nhập <strong>Email Gmail gửi thư</strong> và <strong>Mật khẩu ứng dụng Google 16 ký tự (App Password)</strong> (tạo tại <em>Google Account → Bảo mật → Xác minh 2 bước → Mật khẩu ứng dụng</em>):
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-stone-500 mb-1">Gmail gửi thư (MAIL_USERNAME)</label>
                                    <input type="email" name="smtp_email" required
                                        value="{{ old('smtp_email', !empty($gmailSmtpSender) ? $gmailSmtpSender : $user->email) }}"
                                        placeholder="vd: tenban@gmail.com"
                                        class="w-full px-3 py-1.5 text-xs rounded-lg border border-sage-200 bg-[#F5FAF7] focus:bg-white focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-stone-500 mb-1">Google App Password (16 ký tự)</label>
                                    <input type="password" name="smtp_app_password" required
                                        placeholder="xxxx xxxx xxxx xxxx"
                                        class="w-full px-3 py-1.5 text-xs font-mono rounded-lg border border-sage-200 bg-[#F5FAF7] focus:bg-white focus:outline-none">
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-terracotta-600 hover:bg-terracotta-700 text-white text-xs font-semibold py-2 rounded-lg transition cursor-pointer">
                                💾 Lưu Cấu Hình Gmail SMTP & Gửi Ngay Mã OTP Về {{ $user->email }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TRANG 2: SỔ ĐỊA CHỈ NHẬN HÀNG (/tai-khoan/dia-chi) -->
    <!-- ========================================================================= -->
    @if($currentTab === 'addresses')
    <div>
        <section id="section-addresses" class="bg-white rounded-3xl border border-sage-100 p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-sage-100 pb-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-sage-500">Giao Nhận Nhanh Chóng</span>
                    <h2 class="font-serif text-2xl font-bold text-stone-800 mt-0.5">
                        📍 Sổ Địa Chỉ Nhận Hàng ({{ $addresses->count() }} địa chỉ)
                    </h2>
                    <p class="text-xs text-stone-500 mt-1">
                        Lưu sẵn 2 – 3+ địa chỉ (Nhà riêng, Văn phòng, Người thân...) và bấm <strong>"Đặt làm mặc định"</strong> để thanh toán ngay không cần nhập lại.
                    </p>
                </div>
                <button type="button" onclick="document.getElementById('add-address-form-box').classList.toggle('hidden')"
                    class="text-xs font-bold bg-sage-500 hover:bg-sage-600 text-white px-4 py-2.5 rounded-full shadow-xs transition cursor-pointer">
                    + Thêm Địa Chỉ Mới
                </button>
            </div>

            <!-- Form thêm địa chỉ mới -->
            <div id="add-address-form-box" class="hidden p-5 rounded-2xl bg-[#F5FAF7] border border-sage-200 space-y-4 text-xs">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-sm text-stone-800">➕ Thêm Địa Chỉ Nhận Hàng Mới</h3>
                    <button type="button" onclick="document.getElementById('add-address-form-box').classList.add('hidden')" class="text-stone-400 hover:text-stone-700 font-bold cursor-pointer">✕ Đóng</button>
                </div>

                <form action="{{ route('customer.addresses.store') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-stone-600 mb-1">Nhãn địa chỉ *</label>
                            <input type="text" name="label" required value="Địa chỉ phụ" placeholder="VD: Nhà riêng, Công ty..."
                                class="w-full px-3.5 py-2 text-xs rounded-xl border border-sage-200 bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-stone-600 mb-1">Người nhận</label>
                            <input type="text" name="recipient_name" value="{{ $user->name }}" placeholder="Họ tên người nhận"
                                class="w-full px-3.5 py-2 text-xs rounded-xl border border-sage-200 bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-stone-600 mb-1">SĐT nhận hàng</label>
                            <input type="text" name="recipient_phone" value="{{ $user->phone }}" placeholder="Số điện thoại"
                                class="w-full px-3.5 py-2 text-xs rounded-xl border border-sage-200 bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-stone-600 mb-1">Tỉnh / Thành phố *</label>
                            <input type="text" name="province" required value="TP. Hồ Chí Minh" placeholder="VD: TP. Hồ Chí Minh"
                                class="w-full px-3.5 py-2 text-xs rounded-xl border border-sage-200 bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-stone-600 mb-1">Quận / Huyện *</label>
                            <input type="text" name="district" required placeholder="VD: Quận 3"
                                class="w-full px-3.5 py-2 text-xs rounded-xl border border-sage-200 bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-stone-600 mb-1">Phường / Xã *</label>
                            <input type="text" name="ward" required placeholder="VD: Phường Võ Thị Sáu"
                                class="w-full px-3.5 py-2 text-xs rounded-xl border border-sage-200 bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-stone-600 mb-1">Số nhà, tên đường, tòa nhà *</label>
                        <input type="text" name="street_address" required placeholder="VD: Số 120 Nguyễn Đình Chiểu"
                            class="w-full px-3.5 py-2 text-xs rounded-xl border border-sage-200 bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-stone-700 font-medium">
                            <input type="checkbox" name="is_default" value="1" checked class="rounded accent-sage-600">
                            <span>Đặt làm địa chỉ nhận hàng mặc định ngay</span>
                        </label>
                        <button type="submit" class="bg-sage-500 hover:bg-sage-600 text-white font-semibold px-5 py-2.5 rounded-xl shadow-xs transition cursor-pointer">
                            ✓ Lưu Địa Chỉ Mới
                        </button>
                    </div>
                </form>
            </div>

            <!-- Danh sách các địa chỉ đã lưu -->
            <div class="space-y-3.5">
                @foreach($addresses as $addr)
                    <div class="p-4 rounded-2xl border-2 transition {{ $addr->is_default ? 'border-sage-500 bg-sage-50/50 shadow-xs' : 'border-sage-100 bg-[#F5FAF7] hover:border-sage-200' }} space-y-3">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs font-bold text-stone-800 bg-white border border-sage-200 px-2.5 py-0.5 rounded-lg">
                                        🏷️ {{ $addr->label }}
                                    </span>
                                    @if($addr->is_default)
                                        <span class="text-[11px] font-bold bg-sage-600 text-white px-2.5 py-0.5 rounded-full shadow-2xs">
                                            ★ Địa chỉ Mặc Định
                                        </span>
                                    @endif
                                    <span class="text-xs font-semibold text-stone-700">
                                        {{ $addr->recipient_name ?: $user->name }} • 📞 {{ $addr->recipient_phone ?: $user->phone }}
                                    </span>
                                </div>
                                <p class="text-xs text-stone-700 font-medium leading-relaxed pt-0.5">
                                    📍 {{ $addr->address }}
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
                                @if(!$addr->is_default)
                                    <form action="{{ route('customer.addresses.default', $addr) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-xs font-bold bg-terracotta-600 hover:bg-terracotta-700 text-white px-3 py-1.5 rounded-xl transition shadow-2xs cursor-pointer">
                                            ★ Đặt làm mặc định
                                        </button>
                                    </form>
                                @endif

                                <button type="button" onclick="document.getElementById('edit-addr-{{ $addr->id }}').classList.toggle('hidden')"
                                    class="text-xs font-semibold text-sage-700 bg-white hover:bg-sage-50 border border-sage-200 px-3 py-1.5 rounded-xl transition cursor-pointer">
                                    Sửa
                                </button>

                                @if($addresses->count() > 1)
                                    <form action="{{ route('customer.addresses.destroy', $addr) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-500 bg-white hover:bg-red-50 border border-red-100 px-2.5 py-1.5 rounded-xl transition cursor-pointer">
                                            Xóa
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Form chỉnh sửa nhanh địa chỉ này -->
                        <div id="edit-addr-{{ $addr->id }}" class="hidden pt-3 border-t border-sage-200/70">
                            <form action="{{ route('customer.addresses.update', $addr) }}" method="POST" class="space-y-3 text-xs">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase text-stone-500 mb-1">Nhãn địa chỉ *</label>
                                        <input type="text" name="label" required value="{{ $addr->label }}"
                                            class="w-full px-3 py-2 text-xs rounded-xl border border-sage-200 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase text-stone-500 mb-1">Người nhận</label>
                                        <input type="text" name="recipient_name" value="{{ $addr->recipient_name ?: $user->name }}"
                                            class="w-full px-3 py-2 text-xs rounded-xl border border-sage-200 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase text-stone-500 mb-1">Số điện thoại</label>
                                        <input type="text" name="recipient_phone" value="{{ $addr->recipient_phone ?: $user->phone }}"
                                            class="w-full px-3 py-2 text-xs rounded-xl border border-sage-200 bg-white">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase text-stone-500 mb-1">Tỉnh / Thành phố *</label>
                                        <input type="text" name="province" required value="{{ $addr->province ?: 'TP. Hồ Chí Minh' }}"
                                            class="w-full px-3 py-2 text-xs rounded-xl border border-sage-200 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase text-stone-500 mb-1">Quận / Huyện *</label>
                                        <input type="text" name="district" required value="{{ $addr->district ?: 'Quận 1' }}"
                                            class="w-full px-3 py-2 text-xs rounded-xl border border-sage-200 bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase text-stone-500 mb-1">Phường / Xã *</label>
                                        <input type="text" name="ward" required value="{{ $addr->ward ?: 'Phường Bến Thành' }}"
                                            class="w-full px-3 py-2 text-xs rounded-xl border border-sage-200 bg-white">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-stone-500 mb-1">Số nhà, tên đường *</label>
                                    <input type="text" name="street_address" required value="{{ $addr->street_address ?: $addr->address }}"
                                        class="w-full px-3 py-2 text-xs rounded-xl border border-sage-200 bg-white">
                                </div>
                                <div class="flex items-center justify-between">
                                    <label class="flex items-center gap-2 cursor-pointer text-stone-600">
                                        <input type="checkbox" name="is_default" value="1" {{ $addr->is_default ? 'checked' : '' }} class="rounded accent-sage-600">
                                        <span>Đặt làm địa chỉ mặc định</span>
                                    </label>
                                    <div class="flex gap-2">
                                        <button type="button" onclick="document.getElementById('edit-addr-{{ $addr->id }}').classList.add('hidden')" class="px-3 py-1.5 rounded-xl border border-stone-200 bg-white text-stone-600 cursor-pointer">Hủy</button>
                                        <button type="submit" class="px-4 py-1.5 rounded-xl bg-sage-500 hover:bg-sage-600 text-white font-semibold cursor-pointer">Lưu cập nhật</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TRANG 3: GIỎ HÀNG ĐÃ LƯU (/tai-khoan/gio-hang) -->
    <!-- ========================================================================= -->
    @if($currentTab === 'cart')
    <div>
        <!-- Saved Cart Items -->
        <section id="section-saved-cart" class="bg-white rounded-3xl border border-sage-100 p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-sage-100 pb-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-sage-500">Đồng Bộ Tự Động Với Tài Khoản</span>
                    <h2 class="font-serif text-2xl font-bold text-stone-800">
                        🛒 Giỏ Hàng Đã Lưu Từ Trước ({{ count($savedCart) }} loại SP)
                    </h2>
                </div>
                @if(count($savedCart) > 0)
                    <a href="{{ route('checkout.index') }}" class="text-xs font-bold bg-sage-500 hover:bg-sage-600 text-white px-4 py-2 rounded-full shadow-xs transition">
                        Thanh Toán Ngay →
                    </a>
                @endif
            </div>

            @if(empty($savedCart))
                <div class="text-center py-12 space-y-3">
                    <p class="font-serif text-lg text-stone-700">Giỏ hàng của bạn hiện đang trống</p>
                    <p class="text-xs text-stone-400">Mọi sản phẩm bạn thêm vào giỏ khi đăng nhập sẽ được lưu trữ lâu dài tại đây.</p>
                    <a href="{{ route('products.index') }}" class="inline-block bg-sage-500 text-white text-xs font-semibold px-5 py-2.5 rounded-full">
                        Khám phá mỹ phẩm ngay
                    </a>
                </div>
            @else
                <div class="space-y-3.5">
                    @foreach($savedCart as $item)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl border border-sage-100 bg-[#F5FAF7] hover:border-sage-200 transition">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-16 h-16 rounded-2xl object-cover bg-white border border-sage-100 flex-shrink-0">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-mono font-bold text-[10px] bg-stone-900 text-white px-2 py-0.5 rounded-md">
                                            {{ $item['sku'] ?? ('LMN-SP' . str_pad((string)$item['id'], 3, '0', STR_PAD_LEFT)) }}
                                        </span>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-sage-500">{{ $item['brand_name'] ?? 'Shine' }}</span>
                                        <span class="text-[10px] text-emerald-600 font-semibold">• Kho: {{ $item['stock'] ?? 20 }} SP</span>
                                    </div>
                                    <a href="{{ route('products.show', $item['slug']) }}" class="block text-sm font-bold text-stone-800 hover:text-sage-600 line-clamp-1 mt-0.5">
                                        {{ $item['name'] }}
                                    </a>
                                    <p class="text-xs text-stone-400">{{ $item['volume'] }} • {{ $item['skin_type'] }}</p>
                                    <p class="text-sm font-bold text-sage-600 mt-0.5">
                                        {{ number_format($item['price'], 0, ',', '.') }}₫
                                        <span class="text-xs font-normal text-stone-400">x {{ $item['quantity'] }} = {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 flex-shrink-0">
                                <div class="flex items-center border border-sage-200 rounded-full bg-white px-1 py-0.5">
                                    <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                        <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                        <button type="submit" class="w-7 h-7 rounded-full hover:bg-sage-50 text-stone-600 font-bold text-xs">-</button>
                                    </form>
                                    <span class="w-7 text-center text-xs font-bold text-stone-800">{{ $item['quantity'] }}</span>
                                    <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                        <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                        <button type="submit" class="w-7 h-7 rounded-full hover:bg-sage-50 text-stone-600 font-bold text-xs">+</button>
                                    </form>
                                </div>

                                <form action="{{ route('cart.remove') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                    <button type="submit" class="text-xs font-semibold text-red-500 hover:bg-red-50 px-3 py-1.5 rounded-full border border-red-100">
                                        Xóa
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-sage-100 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <span class="text-xs text-stone-500">Tổng giá trị giỏ hàng đã lưu:</span>
                        <p class="text-xl font-bold text-sage-600">{{ number_format($savedCartTotal, 0, ',', '.') }}₫</p>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="bg-sage-500 hover:bg-sage-600 text-white font-semibold px-7 py-3 rounded-full text-sm shadow-md shadow-sage-200 transition">
                        Tiến Hành Đặt Hàng Ngay →
                    </a>
                </div>
            @endif
        </section>
    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TRANG 4: KHO VOUCHER ƯU ĐÃI (/tai-khoan/voucher) -->
    <!-- ========================================================================= -->
    @if($currentTab === 'vouchers')
    <div>
        <!-- Shop Vouchers for Customer Account (Terracotta Tone) -->
        <section id="section-vouchers" class="bg-gradient-to-br from-terracotta-50 via-[#FFF8F5] to-terracotta-100/60 rounded-3xl border border-terracotta-200 p-6 sm:p-8 shadow-xs space-y-5">
            <div class="border-b border-terracotta-200/80 pb-4">
                <span class="text-xs font-bold uppercase tracking-widest text-terracotta-700">Ưu Đãi Dành Cho Tài Khoản Khách Hàng</span>
                <h2 class="font-serif text-2xl font-bold text-terracotta-900">
                    🎟️ Kho Voucher Hiện Có ({{ $activeCoupons->count() }})
                </h2>
                <p class="text-xs text-terracotta-800/80 mt-1">
                    Mỗi đơn hàng chỉ được áp dụng <strong>01 mã giảm giá duy nhất</strong> (không áp dụng đồng thời 2 voucher).
                </p>
            </div>

            <div class="space-y-3">
                @forelse($activeCoupons as $coupon)
                    @php
                        $isThisCouponApplied = !empty($appliedCoupon) && $appliedCoupon['code'] === $coupon->code;
                        $hasAnotherCouponApplied = !empty($appliedCoupon) && $appliedCoupon['code'] !== $coupon->code;
                    @endphp
                    <div class="p-4 rounded-2xl border {{ $isThisCouponApplied ? 'border-terracotta-600 bg-terracotta-600 text-white shadow-md' : 'border-terracotta-200 bg-gradient-to-r from-white via-terracotta-50/70 to-terracotta-100/50' }} flex items-center justify-between gap-3 relative overflow-hidden">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-base {{ $isThisCouponApplied ? 'text-white bg-terracotta-700 border-terracotta-500' : 'text-terracotta-800 bg-terracotta-100 border-terracotta-300' }} px-2.5 py-0.5 rounded-lg border">
                                    {{ $coupon->code }}
                                </span>
                                <span class="text-xs font-bold {{ $isThisCouponApplied ? 'bg-white text-terracotta-800' : 'bg-terracotta-600 text-white' }} px-2.5 py-0.5 rounded-full">
                                    Giảm {{ $coupon->discount_percent }}%
                                </span>
                            </div>
                            <p class="text-xs {{ $isThisCouponApplied ? 'text-terracotta-50' : 'text-stone-700' }} font-medium">
                                {{ $coupon->description ?? "Giảm {$coupon->discount_percent}% cho mọi đơn hàng mỹ phẩm chính hãng" }}
                            </p>
                            <p class="text-[11px] {{ $isThisCouponApplied ? 'text-terracotta-200' : 'text-terracotta-700/75' }}">
                                Đơn tối thiểu: {{ number_format($coupon->min_order_amount ?? 0, 0, ',', '.') }}₫ • HSD: {{ $coupon->expires_at?->format('d/m/Y') }}
                            </p>
                        </div>

                        @if($isThisCouponApplied)
                            <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                                <span class="bg-white text-terracotta-800 text-xs font-bold px-3 py-1.5 rounded-xl shadow-xs">
                                    ✓ Đang dùng
                                </span>
                                <form action="{{ route('checkout.coupon.remove') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[11px] text-terracotta-100 font-bold hover:underline">
                                        Gỡ mã này
                                    </button>
                                </form>
                            </div>
                        @elseif($hasAnotherCouponApplied)
                            <button type="button" disabled title="Mỗi đơn chỉ được áp dụng 1 voucher. Vui lòng gỡ mã {{ $appliedCoupon['code'] }} trước."
                                class="flex-shrink-0 bg-terracotta-100/80 text-terracotta-400 border border-terracotta-200 text-xs font-semibold px-3.5 py-2 rounded-xl cursor-not-allowed">
                                Đã dùng {{ $appliedCoupon['code'] }}
                            </button>
                        @else
                            <form action="{{ route('customer.voucher.apply') }}" method="POST" class="flex-shrink-0">
                                @csrf
                                <input type="hidden" name="coupon_code" value="{{ $coupon->code }}">
                                <button type="submit" class="bg-terracotta-600 hover:bg-terracotta-700 text-white text-xs font-semibold px-3.5 py-2 rounded-xl transition shadow-xs">
                                    Áp dụng ngay
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="text-xs text-terracotta-700/70 text-center py-8">Hiện shop chưa có mã giảm giá nào đang mở.</p>
                @endforelse
            </div>
        </section>
    </div>
    @endif

    <!-- ========================================================================= -->
    <!-- TRANG 5: ĐƠN HÀNG CỦA TÔI (/tai-khoan/don-hang) -->
    <!-- ========================================================================= -->
    @if($currentTab === 'orders')
    <section id="section-my-orders" class="bg-white rounded-3xl border border-sage-100 p-6 sm:p-8 shadow-xs space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-sage-100 pb-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-sage-500">Lịch Sử Mua Sắm & Vận Chuyển</span>
                <h2 class="font-serif text-2xl font-bold text-stone-800">
                    📦 Đơn Hàng Của Tôi ({{ $myOrders->count() }})
                </h2>
            </div>

            <!-- Filter by Status -->
            <div class="flex flex-wrap gap-1.5">
                @foreach([
                    'ALL' => 'Tất cả (' . $allMyOrders->count() . ')',
                    'PENDING' => 'Chờ xác nhận (' . $allMyOrders->where('status', 'PENDING')->count() . ')',
                    'CONFIRMED' => 'Đã xác nhận (' . $allMyOrders->where('status', 'CONFIRMED')->count() . ')',
                    'SHIPPING' => 'Đang giao (' . $allMyOrders->where('status', 'SHIPPING')->count() . ')',
                    'COMPLETED' => 'Hoàn thành (' . $allMyOrders->where('status', 'COMPLETED')->count() . ')',
                    'CANCELLED' => 'Đã hủy (' . $allMyOrders->where('status', 'CANCELLED')->count() . ')',
                ] as $stCode => $stLabel)
                    <a href="{{ route('customer.orders', ['status' => $stCode]) }}"
                        class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition {{ $statusFilter === $stCode ? 'bg-sage-500 text-white shadow-xs' : 'bg-sage-50/70 text-stone-600 hover:bg-sage-100' }}">
                        {{ $stLabel }}
                    </a>
                @endforeach
            </div>
        </div>

        @if($myOrders->isEmpty())
            <div class="text-center py-12 space-y-2">
                <p class="font-serif text-lg text-stone-700">Chưa có đơn hàng nào ở trạng thái này</p>
                <p class="text-xs text-stone-400">Hãy đặt mua từ giỏ hàng đã lưu của bạn để theo dõi tiến trình giao hàng.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($myOrders as $order)
                    @php
                        $statusBadge = [
                            'PENDING' => ['label' => '⏳ Chờ Shop Xác Nhận', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
                            'CONFIRMED' => ['label' => '✓ Shop Đã Xác Nhận', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
                            'SHIPPING' => ['label' => '🚚 Đang Giao Hàng Đến Bạn', 'class' => 'bg-purple-50 text-purple-700 border-purple-200'],
                            'COMPLETED' => ['label' => '✨ Giao Thành Công', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                            'CANCELLED' => ['label' => '✕ Đã Hủy', 'class' => 'bg-red-50 text-red-700 border-red-200'],
                        ][$order->status] ?? ['label' => $order->status, 'class' => 'bg-stone-100 text-stone-700'];
                    @endphp
                    <div class="p-5 sm:p-6 rounded-3xl border border-sage-100 bg-[#F5FAF7] space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-sage-100 pb-3">
                            <div class="flex items-center gap-3">
                                <span class="font-mono font-bold text-base text-stone-800">#ORDER-{{ $order->id }}</span>
                                <span class="text-xs text-stone-400">Đặt lúc: {{ $order->created_at?->format('d/m/Y H:i') }}</span>
                            </div>
                            <span class="text-xs font-bold px-3.5 py-1 rounded-full border {{ $statusBadge['class'] }}">
                                {{ $statusBadge['label'] }}
                            </span>
                        </div>

                        <!-- Danh sách sản phẩm trong đơn -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($order->items as $item)
                                <div class="flex items-center justify-between gap-3 bg-white p-3 rounded-2xl border border-sage-50">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <img src="{{ $item->product->first_image ?? 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=400&q=80' }}"
                                            alt="{{ $item->product->name ?? '' }}"
                                            class="w-12 h-12 rounded-xl object-cover bg-sage-50 flex-shrink-0">
                                        <div class="min-w-0">
                                            <span class="font-mono text-[10px] font-bold text-sage-600">{{ $item->product->product_code ?? ('LMN-SP' . str_pad((string)$item->product_id, 3, '0', STR_PAD_LEFT)) }}</span>
                                            <a href="{{ isset($item->product->slug) ? route('products.show', $item->product->slug) : '#' }}" class="block text-xs font-bold text-stone-800 hover:text-sage-600 line-clamp-1">
                                                {{ $item->product->name ?? 'Sản phẩm #' . $item->product_id }}
                                            </a>
                                            <p class="text-[11px] text-stone-500">
                                                {{ number_format($item->price, 0, ',', '.') }}₫ x <strong>{{ $item->quantity }}</strong>
                                            </p>
                                        </div>
                                    </div>
                                    @if($item->product)
                                        <form action="{{ route('cart.add') }}" method="POST" class="flex-shrink-0">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="text-[11px] font-semibold bg-sage-50 hover:bg-sage-500 hover:text-white text-sage-600 px-3 py-1.5 rounded-full transition">
                                                + Mua lại
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-sage-100 text-xs text-stone-600">
                            <div>
                                <span>📍 Giao đến: <strong>{{ $order->address }}</strong> ({{ $order->phone }})</span>
                                @if($order->coupon_code)
                                    <span class="ml-2 inline-block bg-terracotta-600 text-white font-semibold px-2.5 py-0.5 rounded-full border border-terracotta-500">
                                        🎟️ Voucher {{ $order->coupon_code }} (-{{ number_format($order->discount_amount, 0, ',', '.') }}₫)
                                    </span>
                                @endif
                            </div>
                            <div class="text-base font-bold text-sage-600">
                                Tổng thanh toán: {{ number_format($order->total_amount, 0, ',', '.') }}₫
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
    @endif

    <!-- ========================================================================= -->
    <!-- SẢN PHẨM ĐÃ MUA TRƯỚC ĐÂY & GỢI Ý MUA SẮM (Thuộc trang Tổng Quan /tai-khoan) -->
    <!-- ========================================================================= -->
    @if($currentTab === 'overview')
    <section id="section-purchased" class="bg-white rounded-3xl border border-sage-100 p-6 sm:p-8 shadow-xs space-y-6">
        <div class="flex items-center justify-between border-b border-sage-100 pb-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-sage-500">Tủ Đồ Làm Đẹp Của Bạn</span>
                <h2 class="font-serif text-2xl font-bold text-stone-800">
                    💄 Sản Phẩm Bạn Đã Mua & Yêu Thích
                </h2>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-bold text-sage-600 hover:underline">Xem toàn bộ cửa hàng →</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach(($purchasedProducts->count() > 0 ? $purchasedProducts : $featuredProducts) as $product)
                <div class="rounded-2xl border border-sage-100 bg-[#F5FAF7] overflow-hidden flex flex-col justify-between p-4 space-y-3 hover:shadow-md transition">
                    <div class="space-y-2">
                        <img src="{{ $product->first_image }}" alt="{{ $product->name }}" class="w-full h-44 object-cover rounded-xl bg-white">
                        <div class="flex items-center justify-between text-[10px]">
                            <span class="font-mono font-bold bg-stone-900 text-white px-2 py-0.5 rounded-md">{{ $product->product_code }}</span>
                            <span class="font-semibold text-emerald-600">Kho: {{ $product->stock }} SP</span>
                        </div>
                        <span class="block text-[10px] font-bold uppercase text-sage-500">{{ $product->brand->name ?? '' }} • {{ $product->skin_type }}</span>
                        <a href="{{ route('products.show', $product->slug) }}" class="block text-sm font-bold text-stone-800 hover:text-sage-600 line-clamp-2">
                            {{ $product->name }}
                        </a>
                    </div>
                    <div class="pt-2 border-t border-sage-100 flex items-center justify-between">
                        <span class="text-sm font-bold text-sage-600">{{ number_format($product->effective_price, 0, ',', '.') }}₫</span>
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="text-xs font-semibold bg-stone-900 hover:bg-sage-500 text-white px-3.5 py-1.5 rounded-full transition">
                                + Thêm Vào Giỏ
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection
