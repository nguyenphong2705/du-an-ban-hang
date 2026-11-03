@extends('layouts.app')

@section('title', 'Đăng Nhập Tài Khoản Khách Hàng | Lumina Beauty')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Top Portal Switcher Banner -->
    <div class="mb-8 bg-white rounded-3xl border border-rose-100 p-4 shadow-xs flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-rose-500 text-white shadow-xs">
                👤 Trang 1: Cổng Đăng Nhập Khách Hàng (/login)
            </span>
            <span class="text-xs text-stone-500 hidden sm:inline">
                Theo dõi đơn hàng, giỏ hàng đã lưu trước đây & kho voucher ưu đãi
            </span>
        </div>
        <a href="{{ route('admin.login') }}" class="text-xs font-bold text-stone-700 hover:text-rose-600 bg-stone-100 hover:bg-rose-50 px-4 py-2 rounded-full transition border border-stone-200">
            🔐 Chuyển sang Trang 2: Đăng nhập Quản trị viên Shop (/admin/login) →
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left Column: Login Form & Sample Account Box -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-rose-100 p-7 sm:p-9 shadow-sm space-y-6">
            <div class="space-y-1.5 border-b border-rose-100 pb-4">
                <span class="text-xs font-bold uppercase tracking-widest text-rose-500">Thành Viên Lumina Beauty</span>
                <h1 class="font-serif text-3xl font-bold text-stone-800">Đăng Nhập Khách Hàng</h1>
                <p class="text-xs text-stone-500">
                    Đăng nhập bằng <strong>Tên đăng nhập</strong> hoặc <strong>Email</strong> để xem đơn hàng, giỏ hàng đã lưu và mã giảm giá của shop.
                </p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-xs font-medium">
                    ⚠ {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('customer.login.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-2">
                        Tên đăng nhập hoặc Email *
                    </label>
                    <input type="text" id="cust-login" name="login" required value="{{ old('login', 'tuyenthanhthanh3979') }}"
                        placeholder="Nhập tuyenthanhthanh3979 hoặc tuyenthanhthanh3979@gmail.com"
                        class="w-full px-4 py-3 text-sm rounded-2xl border border-rose-200 bg-[#FDFBF7] focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-2">
                        Mật khẩu *
                    </label>
                    <input type="password" id="cust-password" name="password" required value="Phong2705@@"
                        placeholder="••••••••••••"
                        class="w-full px-4 py-3 text-sm rounded-2xl border border-rose-200 bg-[#FDFBF7] focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-400">
                </div>

                <div class="flex items-center justify-between text-xs text-stone-500">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" checked class="rounded accent-rose-500">
                        <span>Tự động lưu giỏ hàng & phiên đăng nhập</span>
                    </label>
                    <span class="text-rose-500 font-medium">Đồng bộ PostgreSQL</span>
                </div>

                <button type="submit" class="w-full bg-rose-500 hover:bg-rose-600 text-white font-semibold py-3.5 rounded-2xl shadow-md shadow-rose-200 transition text-sm">
                    Đăng Nhập Vào Tài Khoản Khách Hàng →
                </button>
            </form>

            <!-- Quick Autofill Sample Customer Account -->
            <div class="pt-4 border-t border-rose-100 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-600">
                        🎁 Tài khoản Khách hàng Mẫu (Đã nạp sẵn Đơn hàng & Giỏ hàng cũ)
                    </span>
                    <span class="text-[11px] bg-rose-50 text-rose-600 px-2.5 py-0.5 rounded-full font-semibold">Click để điền</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <button type="button" onclick="fillCustomer('tuyenthanhthanh3979', 'Phong2705@@')"
                        class="text-left p-3.5 rounded-2xl bg-[#FDFBF7] hover:bg-rose-50/70 border border-rose-200 transition space-y-1">
                        <p class="font-bold text-stone-800">Cách 1: Đăng nhập bằng Username</p>
                        <p class="font-mono text-[11px] text-stone-600">User: <strong class="text-rose-600">tuyenthanhthanh3979</strong></p>
                        <p class="font-mono text-[11px] text-stone-600">Pass: <strong class="text-rose-600">Phong2705@@</strong></p>
                    </button>

                    <button type="button" onclick="fillCustomer('tuyenthanhthanh3979@gmail.com', 'Phong2705@@')"
                        class="text-left p-3.5 rounded-2xl bg-[#FDFBF7] hover:bg-rose-50/70 border border-rose-200 transition space-y-1">
                        <p class="font-bold text-stone-800">Cách 2: Đăng nhập bằng Email</p>
                        <p class="font-mono text-[11px] text-stone-600">Email: <strong class="text-rose-600">tuyenthanhthanh3979@gmail.com</strong></p>
                        <p class="font-mono text-[11px] text-stone-600">Pass: <strong class="text-rose-600">Phong2705@@</strong></p>
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: Benefits & Quick Registration -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-gradient-to-br from-rose-500 to-rose-600 text-white rounded-3xl p-7 shadow-md space-y-4">
                <span class="text-[11px] font-bold uppercase tracking-widest bg-white/20 px-3 py-1 rounded-full">
                    Đặc Quyền Tài Khoản Khách Hàng
                </span>
                <h2 class="font-serif text-2xl font-bold">Tiện Ích Khi Đăng Nhập</h2>
                <ul class="space-y-2.5 text-xs text-rose-50 leading-relaxed">
                    <li>✓ <strong>Theo dõi đơn hàng:</strong> Xem lịch sử đặt hàng, trạng thái vận chuyển (`Chờ xác nhận`, `Đang giao`, `Hoàn thành`) và chi tiết mỹ phẩm đã mua.</li>
                    <li>✓ <strong>Lưu giữ giỏ hàng tự động:</strong> Các sản phẩm đã thêm vào giỏ hàng trước đây được lưu vào cơ sở dữ liệu PostgreSQL, không bị mất khi tắt trình duyệt.</li>
                    <li>✓ <strong>Ví Voucher độc quyền:</strong> Theo dõi tất cả mã giảm giá hiện có của shop (`LUMINA20`, `FLASHSALE30`, `SKINCARE15`, `NEWMEMBER10`) và bấm áp dụng chỉ với 1 click.</li>
                </ul>
            </div>

            <!-- Form Đăng ký nhanh cho khách mới -->
            <div class="bg-white rounded-3xl border border-rose-100 p-6 shadow-xs space-y-4">
                <h3 class="font-serif text-lg font-bold text-stone-800">Chưa có tài khoản? Đăng ký mới</h3>
                <form action="{{ route('customer.register') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div class="grid grid-cols-2 gap-2.5">
                        <input type="text" name="name" required placeholder="Họ và tên *" class="px-3.5 py-2.5 rounded-xl border border-rose-200 bg-[#FDFBF7]">
                        <input type="text" name="username" required placeholder="Tên đăng nhập *" class="px-3.5 py-2.5 rounded-xl border border-rose-200 bg-[#FDFBF7]">
                    </div>
                    <div class="grid grid-cols-2 gap-2.5">
                        <input type="email" name="email" required placeholder="Email *" class="px-3.5 py-2.5 rounded-xl border border-rose-200 bg-[#FDFBF7]">
                        <input type="text" name="phone" required placeholder="Số điện thoại *" class="px-3.5 py-2.5 rounded-xl border border-rose-200 bg-[#FDFBF7]">
                    </div>
                    <input type="text" name="address" placeholder="Địa chỉ nhận hàng mặc định" class="w-full px-3.5 py-2.5 rounded-xl border border-rose-200 bg-[#FDFBF7]">
                    <input type="password" name="password" required placeholder="Mật khẩu (tối thiểu 6 ký tự) *" class="w-full px-3.5 py-2.5 rounded-xl border border-rose-200 bg-[#FDFBF7]">
                    <button type="submit" class="w-full bg-stone-900 hover:bg-rose-600 text-white font-semibold py-2.5 rounded-xl transition">
                        Tạo Tài Khoản Khách Hàng Mới
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function fillCustomer(login, pass) {
        document.getElementById('cust-login').value = login;
        document.getElementById('cust-password').value = pass;
    }
</script>
@endsection
