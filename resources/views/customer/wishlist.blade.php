@extends('layouts.app')

@section('title', 'Danh Mục Sản Phẩm Yêu Thích | Tài Khoản Khách Hàng SHINE Beauty')
@section('meta_robots', 'noindex, nofollow')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
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
                    @endif
                </div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-stone-800">{{ $user->name }}</h1>
                <p class="text-xs text-stone-500">
                    📧 {{ $user->email }} • 📞 {{ $user->phone ?? 'Chưa cập nhật' }} • 📍 {{ $user->address ?? 'TP. Hồ Chí Minh' }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('customer.account') }}" class="text-xs font-semibold bg-sage-50 hover:bg-sage-100 text-sage-700 border border-sage-200 px-4 py-2.5 rounded-full transition">
                ← Quay lại Tổng quan Tài khoản
            </a>
            <a href="{{ route('products.index') }}" class="text-xs font-semibold bg-sage-500 hover:bg-sage-600 text-white px-4 py-2.5 rounded-full transition shadow-xs">
                🛍️ Tiếp tục mua sắm
            </a>
        </div>
    </div>

    <!-- Thanh Danh Mục Trong Phần Tài Khoản Khách Hàng (Mỗi danh mục là 1 trang riêng biệt) -->
    <nav class="bg-white rounded-2xl border border-sage-100 p-2 shadow-2xs flex flex-wrap items-center gap-2 text-xs font-bold">
        <a href="{{ route('customer.account') }}" class="px-4 py-2.5 rounded-xl text-stone-600 hover:bg-sage-50 hover:text-sage-700 transition">
            👤 Tổng Quan Tài Khoản
        </a>
        <a href="{{ route('customer.profile') }}" class="px-4 py-2.5 rounded-xl text-stone-600 hover:bg-sage-50 hover:text-sage-700 transition">
            ✏️ Đổi Thông Tin & Email
        </a>
        <a href="{{ route('customer.addresses') }}" class="px-4 py-2.5 rounded-xl text-stone-600 hover:bg-sage-50 hover:text-sage-700 transition">
            📍 Sổ Địa Chỉ ({{ $user->addresses()->count() }})
        </a>
        <a href="{{ route('customer.wishlist') }}" class="px-4 py-2.5 rounded-xl bg-red-500 text-white shadow-xs flex items-center gap-1.5 transition">
            <span>❤️ Yêu Thích</span>
            <span data-wishlist-count class="bg-white text-red-600 px-2 py-0.5 rounded-full text-[11px]">{{ $wishlistProducts->count() }}</span>
        </a>
        <a href="{{ route('customer.cart') }}" class="px-4 py-2.5 rounded-xl text-stone-600 hover:bg-sage-50 hover:text-sage-700 transition">
            🛒 Giỏ Hàng ({{ collect($savedCart)->sum('quantity') }})
        </a>
        <a href="{{ route('customer.orders') }}" class="px-4 py-2.5 rounded-xl text-stone-600 hover:bg-sage-50 hover:text-sage-700 transition">
            📦 Đơn Hàng ({{ $ordersCount }})
        </a>
        <a href="{{ route('customer.vouchers') }}" class="px-4 py-2.5 rounded-xl text-stone-600 hover:bg-terracotta-50 hover:text-terracotta-700 transition">
            🎟️ Kho Voucher ({{ $couponsCount }})
        </a>
    </nav>

    <!-- Danh Mục Mới: Sản Phẩm Yêu Thích Của Khách Hàng -->
    <section class="bg-white rounded-3xl border border-sage-100 p-6 sm:p-8 shadow-xs space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-sage-100 pb-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-red-500">Danh Mục Cá Nhân Hóa</span>
                <h2 class="font-serif text-2xl sm:text-3xl font-bold text-stone-800 flex items-center gap-2 mt-0.5">
                    <span>❤️ Danh Mục Yêu Thích Của Bạn</span>
                    <span class="text-sm font-sans font-bold bg-red-50 text-red-600 border border-red-200 px-3 py-1 rounded-full">
                        <span data-wishlist-count>{{ $wishlistProducts->count() }}</span> sản phẩm
                    </span>
                </h2>
                <p class="text-xs text-stone-500 mt-1">
                    Các sản phẩm bạn đã thả tim yêu thích tại Trang chủ và Cửa hàng được lưu trữ tại đây để mua sắm bất cứ lúc nào.
                </p>
            </div>

            <a href="{{ route('home') }}" class="text-xs font-semibold bg-sage-50 hover:bg-sage-100 text-sage-700 border border-sage-200 px-4 py-2.5 rounded-full transition">
                + Khám phá thêm tại Trang chủ →
            </a>
        </div>

        @if($wishlistProducts->isEmpty())
            <div class="text-center py-16 space-y-4">
                <div class="w-16 h-16 mx-auto rounded-full bg-red-50 text-red-400 flex items-center justify-center text-3xl">
                    ♡
                </div>
                <div class="space-y-1">
                    <p class="font-serif text-xl font-bold text-stone-700">Danh mục Yêu Thích của bạn đang trống</p>
                    <p class="text-xs text-stone-400 max-w-md mx-auto">
                        Hãy nhấn vào biểu tượng trái tim trên các sản phẩm ở Trang chủ hoặc Trang sản phẩm để lưu vào danh mục Yêu Thích của riêng bạn.
                    </p>
                </div>
                <a href="{{ route('home') }}" class="inline-block bg-sage-500 hover:bg-sage-600 text-white text-xs font-semibold px-6 py-3 rounded-full shadow-sm transition">
                    Khám phá sản phẩm ngay →
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($wishlistProducts as $product)
                    <div id="wishlist-item-card-{{ $product->id }}">
                        @include('partials.product-card', ['product' => $product])
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
