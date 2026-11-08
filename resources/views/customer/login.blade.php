@extends('layouts.app')

@section('title', 'Đăng Nhập Tài Khoản Khách Hàng | Shine Beauty')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="bg-white rounded-3xl border border-sage-100 p-8 sm:p-10 shadow-sm text-center space-y-6">
        <div class="w-20 h-20 mx-auto rounded-3xl bg-[#F5FAF7] border border-sage-100 p-2.5 flex items-center justify-center shadow-2xs">
            <img src="{{ asset('logo.png') }}?v=3" alt="Shine Beauty Logo" width="60" height="60" class="w-14 h-14 object-contain">
        </div>
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold bg-sage-50 text-sage-600 border border-sage-200">
            👤 Cổng Đăng Nhập Khách Hàng
        </div>
        <h1 class="font-serif text-3xl font-bold text-stone-800">Thành Viên Shine Beauty</h1>
        <p class="text-sm text-stone-500 max-w-xl mx-auto">
            Theo dõi lịch sử đơn hàng, đồng bộ giỏ hàng tự động và sử dụng kho mã giảm giá ưu đãi độc quyền của shop.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
            <button type="button" onclick="openAuthModal('login')" class="w-60 inline-flex items-center justify-center bg-sage-500 hover:bg-sage-600 text-white font-semibold px-7 py-3.5 rounded-2xl shadow-md shadow-sage-200 transition text-sm cursor-pointer">
                🔑 Mở Popup Đăng Nhập
            </button>
            <button type="button" onclick="openAuthModal('register')" class="w-60 inline-flex items-center justify-center bg-stone-900 hover:bg-sage-600 text-white font-semibold px-7 py-3.5 rounded-2xl shadow-md transition text-sm cursor-pointer">
                ✨ Mở Popup Đăng Ký Mới
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-6 text-left border-t border-sage-100">
            <div class="p-4 rounded-2xl bg-[#F5FAF7] border border-sage-100 space-y-1">
                <p class="text-xs font-bold text-sage-600">✓ Theo dõi đơn hàng</p>
                <p class="text-xs text-stone-500">Xem lịch sử đặt hàng và trạng thái vận chuyển chi tiết theo thời gian thực.</p>
            </div>
            <div class="p-4 rounded-2xl bg-[#F5FAF7] border border-sage-100 space-y-1">
                <p class="text-xs font-bold text-sage-600">✓ Giỏ hàng thông minh</p>
                <p class="text-xs text-stone-500">Tự động lưu trữ và khôi phục giỏ hàng khi đăng nhập lại.</p>
            </div>
            <div class="p-4 rounded-2xl bg-[#F5FAF7] border border-sage-100 space-y-1">
                <p class="text-xs font-bold text-sage-600">✓ Ưu đãi độc quyền</p>
                <p class="text-xs text-stone-500">Nhanh chóng áp dụng các mã giảm giá dành riêng cho thành viên như LUMINA20, FLASHSALE30.</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const params = new URLSearchParams(window.location.search);
        const tab = params.get('tab') === 'register' ? 'register' : @json(old('auth_tab', 'login'));
        openAuthModal(tab);
    });
</script>
@endsection
