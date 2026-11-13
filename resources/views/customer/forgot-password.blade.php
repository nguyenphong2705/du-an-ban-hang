@extends('layouts.app')

@section('title', 'Quên Mật Khẩu & Khôi Phục Tài Khoản | SHINE Beauty')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="flex items-center justify-between">
        <a href="{{ route('customer.login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-sage-600 hover:underline">
            ← Quay lại trang Đăng Nhập / Đăng Ký
        </a>
        <span class="text-xs font-bold uppercase tracking-widest text-sage-500">Bảo Mật Tài Khoản Khách Hàng</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Bước 1: Yêu cầu gửi mã OTP qua SĐT & Email -->
        <div class="lg:col-span-6 bg-white rounded-3xl border border-sage-100 p-7 shadow-sm space-y-5">
            <div class="space-y-1.5 border-b border-sage-100 pb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-sage-500">Bước 1 / 2</span>
                <h1 class="text-2xl font-bold text-stone-800">Nhận Mã Khôi Phục Mật Khẩu</h1>
                <p class="text-xs text-stone-500 leading-relaxed">
                    Nhập <strong>Email</strong> hoặc <strong>Số điện thoại</strong> đã đăng ký. Hệ thống sẽ gửi mã xác thực 6 chữ số (OTP) về số điện thoại và email của bạn.
                </p>
            </div>

            @if($errors->has('identifier'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-xs font-medium">
                    ⚠ {{ $errors->first('identifier') }}
                </div>
            @endif

            <form action="{{ route('customer.password.send') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-2">
                        Email hoặc Số điện thoại đăng ký *
                    </label>
                    <input type="text" name="identifier" required
                        value="{{ old('identifier', $resetState['email'] ?? '') }}"
                        placeholder="Nhập Email hoặc Số điện thoại (VD: 0909397979)"
                        class="w-full px-4 py-3 text-sm rounded-2xl border border-sage-200 bg-[#F5FAF7] focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-2">
                        Phương thức nhận mã xác thực (OTP)
                    </label>
                    <div class="space-y-2 text-xs">
                        <label class="flex items-center gap-2.5 p-3 rounded-2xl border border-sage-200 bg-[#F5FAF7] cursor-pointer hover:bg-sage-50/50">
                            <input type="radio" name="channel" value="both" checked class="accent-sage-500">
                            <span class="font-medium text-stone-800">📧📱 Gửi đồng thời qua cả Email & SMS Số điện thoại</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-3 rounded-2xl border border-sage-200 bg-[#F5FAF7] cursor-pointer hover:bg-sage-50/50">
                            <input type="radio" name="channel" value="phone" {{ ($resetState['channel'] ?? '') === 'phone' ? 'checked' : '' }} class="accent-sage-500">
                            <span class="font-medium text-stone-800">📱 Nhận mã OTP qua tin nhắn Số điện thoại (SMS)</span>
                        </label>
                        <label class="flex items-center gap-2.5 p-3 rounded-2xl border border-sage-200 bg-[#F5FAF7] cursor-pointer hover:bg-sage-50/50">
                            <input type="radio" name="channel" value="email" {{ ($resetState['channel'] ?? '') === 'email' ? 'checked' : '' }} class="accent-sage-500">
                            <span class="font-medium text-stone-800">📧 Nhận mã OTP qua hộp thư Email</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full bg-stone-900 hover:bg-sage-600 text-white font-semibold py-3.5 rounded-2xl transition text-sm shadow-sm">
                    {{ !empty($resetState) ? 'Gửi Lại Mã Xác Thực Mới →' : 'Gửi Mã Xác Thực Về SĐT & Email →' }}
                </button>
            </form>
        </div>

        <!-- Bước 2: Nhập mã OTP & Tạo mật khẩu mới -->
        <div class="lg:col-span-6 bg-white rounded-3xl border border-sage-100 p-7 shadow-sm space-y-5">
            <div class="space-y-1.5 border-b border-sage-100 pb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-sage-500">Bước 2 / 2</span>
                <h2 class="text-2xl font-bold text-stone-800">Xác Thực Mã & Tạo Mật Khẩu Mới</h2>
                <p class="text-xs text-stone-500 leading-relaxed">
                    Nhập mã OTP 6 chữ số đã được gửi tới số điện thoại / email của bạn để thiết lập mật khẩu mới.
                </p>
            </div>

            @if(session('otp_sent'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs space-y-1.5">
                    <p class="font-bold">✓ {{ session('otp_sent') }}</p>
                    @if(!empty($resetState['code']))
                        <div class="flex items-center justify-between bg-white/90 px-3 py-2 rounded-xl border border-emerald-200 mt-1">
                            <span>Mã OTP vừa gửi tới SĐT/Email: <strong class="font-mono text-sm text-sage-600">{{ $resetState['code'] }}</strong></span>
                            <button type="button" onclick="document.getElementById('otp-input').value='{{ $resetState['code'] }}'" class="text-[11px] font-bold text-emerald-700 hover:underline">
                                Điền nhanh
                            </button>
                        </div>
                    @endif
                </div>
            @elseif(!empty($resetState['code']))
                <div class="bg-sage-50/70 border border-sage-200 text-stone-700 p-4 rounded-2xl text-xs space-y-1">
                    <p>Đang khôi phục cho: <strong>{{ $resetState['name'] }}</strong> ({{ $resetState['phone'] }} | {{ $resetState['email'] }})</p>
                    <p>Mã OTP hiệu lực: <strong class="font-mono text-sage-600">{{ $resetState['code'] }}</strong></p>
                </div>
            @endif

            @if($errors->has('otp_code') || $errors->has('password'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-xs font-medium">
                    ⚠ {{ $errors->first('otp_code') ?: $errors->first('password') }}
                </div>
            @endif

            <form action="{{ route('customer.password.reset') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-2">
                        Mã xác thực 6 số (OTP) *
                    </label>
                    <input type="text" id="otp-input" name="otp_code" maxlength="6" required
                        value="{{ old('otp_code', $resetState['code'] ?? '') }}"
                        placeholder="Nhập 6 chữ số (VD: 123456)"
                        class="w-full px-4 py-3 text-sm font-mono tracking-widest font-bold rounded-2xl border border-sage-200 bg-[#F5FAF7] focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-2">
                        Mật khẩu mới (tối thiểu 6 ký tự) *
                    </label>
                    <input type="password" name="password" required
                        placeholder="Nhập mật khẩu mới"
                        class="w-full px-4 py-3 text-sm rounded-2xl border border-sage-200 bg-[#F5FAF7] focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-2">
                        Xác nhận mật khẩu mới *
                    </label>
                    <input type="password" name="password_confirmation" required
                        placeholder="Nhập lại mật khẩu mới"
                        class="w-full px-4 py-3 text-sm rounded-2xl border border-sage-200 bg-[#F5FAF7] focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-400">
                </div>

                <button type="submit" class="w-full bg-sage-500 hover:bg-sage-600 text-white font-semibold py-3.5 rounded-2xl shadow-md shadow-sage-200 transition text-sm">
                    Xác Nhận & Tạo Mật Khẩu Mới →
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
