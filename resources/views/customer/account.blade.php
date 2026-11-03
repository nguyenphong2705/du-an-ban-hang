@extends('layouts.app')

@section('title', 'Trung Tâm Tài Khoản Khách Hàng | Lumina Beauty')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
    <!-- Customer Header Banner -->
    <div class="bg-white rounded-3xl border border-rose-100 p-6 sm:p-8 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-rose-500 to-rose-300 text-white flex items-center justify-center font-serif text-2xl font-bold shadow-md shadow-rose-200">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[11px] font-bold uppercase tracking-widest bg-rose-50 text-rose-600 border border-rose-200 px-2.5 py-0.5 rounded-full">
                        Khách Hàng Thân Thiết
                    </span>
                    <span class="text-xs font-mono text-stone-500">@<span>{{ $user->username }}</span></span>
                </div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-stone-800">{{ $user->name }}</h1>
                <p class="text-xs text-stone-500">
                    📧 {{ $user->email }} • 📞 {{ $user->phone ?? 'Chưa cập nhật' }} • 📍 {{ $user->address ?? 'TP. Hồ Chí Minh' }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('products.index') }}" class="text-xs font-semibold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-4 py-2.5 rounded-full transition">
                🛍️ Tiếp tục mua sắm
            </a>
            <form action="{{ route('customer.logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-xs font-semibold bg-stone-900 hover:bg-red-600 text-white px-4 py-2.5 rounded-full transition">
                    Đăng xuất tài khoản
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="#section-saved-cart" class="bg-white p-5 rounded-3xl border border-rose-100 shadow-xs hover:border-rose-300 transition space-y-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Giỏ Hàng Đã Lưu Trước Đây</span>
            <p class="text-2xl font-bold text-rose-600">{{ collect($savedCart)->sum('quantity') }} sản phẩm</p>
            <p class="text-xs text-stone-500">Tạm tính: {{ number_format($savedCartTotal, 0, ',', '.') }}₫</p>
        </a>

        <a href="#section-my-orders" class="bg-white p-5 rounded-3xl border border-rose-100 shadow-xs hover:border-rose-300 transition space-y-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Đơn Hàng Đã Đặt</span>
            <p class="text-2xl font-bold text-stone-800">{{ $allMyOrders->count() }} đơn hàng</p>
            <p class="text-xs text-emerald-600 font-medium">
                Đang giao: {{ $allMyOrders->where('status', 'SHIPPING')->count() }} • Hoàn thành: {{ $allMyOrders->where('status', 'COMPLETED')->count() }}
            </p>
        </a>

        <a href="#section-vouchers" class="bg-white p-5 rounded-3xl border border-rose-100 shadow-xs hover:border-rose-300 transition space-y-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Kho Voucher Của Shop</span>
            <p class="text-2xl font-bold text-amber-600">{{ $activeCoupons->count() }} mã ưu đãi</p>
            <p class="text-xs text-stone-500">Giảm tối đa đến {{ $activeCoupons->max('discount_percent') ?? 30 }}%</p>
        </a>

        <a href="#section-purchased" class="bg-white p-5 rounded-3xl border border-rose-100 shadow-xs hover:border-rose-300 transition space-y-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400">Sản Phẩm Đã Từng Mua</span>
            <p class="text-2xl font-bold text-stone-800">{{ $purchasedProducts->count() }} dòng mỹ phẩm</p>
            <p class="text-xs text-rose-500 font-medium">Đặt mua lại nhanh với 1 click</p>
        </a>
    </div>

    <!-- ========================================================================= -->
    <!-- 1. GIỎ HÀNG ĐÃ THÊM VÀO TRƯỚC ĐÂY & KHO VOUCHER HIỆN CÓ CỦA SHOP -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Saved Cart Items -->
        <section id="section-saved-cart" class="lg:col-span-7 bg-white rounded-3xl border border-rose-100 p-6 sm:p-8 shadow-xs space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-rose-100 pb-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-rose-500">Đồng Bộ Tự Động Với Tài Khoản</span>
                    <h2 class="font-serif text-2xl font-bold text-stone-800">
                        🛒 Giỏ Hàng Đã Lưu Từ Trước ({{ count($savedCart) }} loại SP)
                    </h2>
                </div>
                @if(count($savedCart) > 0)
                    <a href="{{ route('checkout.index') }}" class="text-xs font-bold bg-rose-500 hover:bg-rose-600 text-white px-4 py-2 rounded-full shadow-xs transition">
                        Thanh Toán Ngay →
                    </a>
                @endif
            </div>

            @if(empty($savedCart))
                <div class="text-center py-12 space-y-3">
                    <p class="font-serif text-lg text-stone-700">Giỏ hàng của bạn hiện đang trống</p>
                    <p class="text-xs text-stone-400">Mọi sản phẩm bạn thêm vào giỏ khi đăng nhập sẽ được lưu trữ lâu dài tại đây.</p>
                    <a href="{{ route('products.index') }}" class="inline-block bg-rose-500 text-white text-xs font-semibold px-5 py-2.5 rounded-full">
                        Khám phá mỹ phẩm ngay
                    </a>
                </div>
            @else
                <div class="space-y-3.5">
                    @foreach($savedCart as $item)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl border border-rose-100 bg-[#FDFBF7] hover:border-rose-200 transition">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-16 h-16 rounded-2xl object-cover bg-white border border-rose-100 flex-shrink-0">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-mono font-bold text-[10px] bg-stone-900 text-white px-2 py-0.5 rounded-md">
                                            {{ $item['sku'] ?? ('LMN-SP' . str_pad((string)$item['id'], 3, '0', STR_PAD_LEFT)) }}
                                        </span>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-rose-500">{{ $item['brand_name'] ?? 'Lumina' }}</span>
                                        <span class="text-[10px] text-emerald-600 font-semibold">• Kho: {{ $item['stock'] ?? 20 }} SP</span>
                                    </div>
                                    <a href="{{ route('products.show', $item['slug']) }}" class="block text-sm font-bold text-stone-800 hover:text-rose-600 line-clamp-1 mt-0.5">
                                        {{ $item['name'] }}
                                    </a>
                                    <p class="text-xs text-stone-400">{{ $item['volume'] }} • {{ $item['skin_type'] }}</p>
                                    <p class="text-sm font-bold text-rose-600 mt-0.5">
                                        {{ number_format($item['price'], 0, ',', '.') }}₫
                                        <span class="text-xs font-normal text-stone-400">x {{ $item['quantity'] }} = {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 flex-shrink-0">
                                <div class="flex items-center border border-rose-200 rounded-full bg-white px-1 py-0.5">
                                    <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                        <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                        <button type="submit" class="w-7 h-7 rounded-full hover:bg-rose-50 text-stone-600 font-bold text-xs">-</button>
                                    </form>
                                    <span class="w-7 text-center text-xs font-bold text-stone-800">{{ $item['quantity'] }}</span>
                                    <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                        <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                        <button type="submit" class="w-7 h-7 rounded-full hover:bg-rose-50 text-stone-600 font-bold text-xs">+</button>
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

                <div class="pt-4 border-t border-rose-100 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <span class="text-xs text-stone-500">Tổng giá trị giỏ hàng đã lưu:</span>
                        <p class="text-xl font-bold text-rose-600">{{ number_format($savedCartTotal, 0, ',', '.') }}₫</p>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="bg-rose-500 hover:bg-rose-600 text-white font-semibold px-7 py-3 rounded-full text-sm shadow-md shadow-rose-200 transition">
                        Tiến Hành Đặt Hàng Ngay →
                    </a>
                </div>
            @endif
        </section>

        <!-- Shop Vouchers for Customer Account -->
        <section id="section-vouchers" class="lg:col-span-5 bg-white rounded-3xl border border-rose-100 p-6 sm:p-8 shadow-xs space-y-5">
            <div class="border-b border-rose-100 pb-4">
                <span class="text-xs font-bold uppercase tracking-widest text-rose-500">Ưu Đãi Dành Cho Tài Khoản Khách Hàng</span>
                <h2 class="font-serif text-2xl font-bold text-stone-800">
                    🎟️ Kho Voucher Hiện Có ({{ $activeCoupons->count() }})
                </h2>
                <p class="text-xs text-stone-500 mt-1">Bấm "Áp dụng ngay" để gắn mã giảm giá trực tiếp vào đơn thanh toán của bạn</p>
            </div>

            <div class="space-y-3">
                @forelse($activeCoupons as $coupon)
                    <div class="p-4 rounded-2xl border border-rose-200 bg-gradient-to-r from-rose-50/70 via-white to-rose-50/40 flex items-center justify-between gap-3 relative overflow-hidden">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-base text-rose-600 bg-rose-100/80 px-2.5 py-0.5 rounded-lg border border-rose-200">
                                    {{ $coupon->code }}
                                </span>
                                <span class="text-xs font-bold bg-rose-500 text-white px-2.5 py-0.5 rounded-full">
                                    Giảm {{ $coupon->discount_percent }}%
                                </span>
                            </div>
                            <p class="text-xs text-stone-700 font-medium">
                                {{ $coupon->description ?? "Giảm {$coupon->discount_percent}% cho mọi đơn hàng mỹ phẩm chính hãng" }}
                            </p>
                            <p class="text-[11px] text-stone-400">
                                Đơn tối thiểu: {{ number_format($coupon->min_order_amount ?? 0, 0, ',', '.') }}₫ • HSD: {{ $coupon->expires_at?->format('d/m/Y') }}
                            </p>
                        </div>

                        <form action="{{ route('customer.voucher.apply') }}" method="POST" class="flex-shrink-0">
                            @csrf
                            <input type="hidden" name="coupon_code" value="{{ $coupon->code }}">
                            <button type="submit" class="bg-stone-900 hover:bg-rose-600 text-white text-xs font-semibold px-3.5 py-2 rounded-xl transition shadow-xs">
                                {{ (!empty($appliedCoupon) && $appliedCoupon['code'] === $coupon->code) ? '✓ Đang dùng' : 'Áp dụng ngay' }}
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-xs text-stone-400 text-center py-8">Hiện shop chưa có mã giảm giá nào đang mở.</p>
                @endforelse
            </div>
        </section>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. THEO DÕI LỊCH SỬ & TRẠNG THÁI ĐƠN HÀNG CỦA KHÁCH HÀNG -->
    <!-- ========================================================================= -->
    <section id="section-my-orders" class="bg-white rounded-3xl border border-rose-100 p-6 sm:p-8 shadow-xs space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-rose-100 pb-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-rose-500">Lịch Sử Mua Sắm & Vận Chuyển</span>
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
                    <a href="{{ route('customer.account', ['status' => $stCode]) }}#section-my-orders"
                        class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition {{ $statusFilter === $stCode ? 'bg-rose-500 text-white shadow-xs' : 'bg-rose-50/70 text-stone-600 hover:bg-rose-100' }}">
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
                    <div class="p-5 sm:p-6 rounded-3xl border border-rose-100 bg-[#FDFBF7] space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-rose-100 pb-3">
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
                                <div class="flex items-center justify-between gap-3 bg-white p-3 rounded-2xl border border-rose-50">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <img src="{{ $item->product->first_image ?? 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=400&q=80' }}"
                                            alt="{{ $item->product->name ?? '' }}"
                                            class="w-12 h-12 rounded-xl object-cover bg-rose-50 flex-shrink-0">
                                        <div class="min-w-0">
                                            <span class="font-mono text-[10px] font-bold text-rose-600">{{ $item->product->product_code ?? ('LMN-SP' . str_pad((string)$item->product_id, 3, '0', STR_PAD_LEFT)) }}</span>
                                            <a href="{{ isset($item->product->slug) ? route('products.show', $item->product->slug) : '#' }}" class="block text-xs font-bold text-stone-800 hover:text-rose-600 line-clamp-1">
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
                                            <button type="submit" class="text-[11px] font-semibold bg-rose-50 hover:bg-rose-500 hover:text-white text-rose-600 px-3 py-1.5 rounded-full transition">
                                                + Mua lại
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-rose-100 text-xs text-stone-600">
                            <div>
                                <span>📍 Giao đến: <strong>{{ $order->address }}</strong> ({{ $order->phone }})</span>
                                @if($order->coupon_code)
                                    <span class="ml-2 inline-block bg-emerald-50 text-emerald-700 font-semibold px-2.5 py-0.5 rounded-full border border-emerald-200">
                                        🎟️ Voucher {{ $order->coupon_code }} (-{{ number_format($order->discount_amount, 0, ',', '.') }}₫)
                                    </span>
                                @endif
                            </div>
                            <div class="text-base font-bold text-rose-600">
                                Tổng thanh toán: {{ number_format($order->total_amount, 0, ',', '.') }}₫
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- ========================================================================= -->
    <!-- 3. SẢN PHẨM ĐÃ MUA TRƯỚC ĐÂY & GỢI Ý MUA SẮM -->
    <!-- ========================================================================= -->
    <section id="section-purchased" class="bg-white rounded-3xl border border-rose-100 p-6 sm:p-8 shadow-xs space-y-6">
        <div class="flex items-center justify-between border-b border-rose-100 pb-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-rose-500">Tủ Đồ Làm Đẹp Của Bạn</span>
                <h2 class="font-serif text-2xl font-bold text-stone-800">
                    💄 Sản Phẩm Bạn Đã Mua & Yêu Thích
                </h2>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-bold text-rose-600 hover:underline">Xem toàn bộ cửa hàng →</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach(($purchasedProducts->count() > 0 ? $purchasedProducts : $featuredProducts) as $product)
                <div class="rounded-2xl border border-rose-100 bg-[#FDFBF7] overflow-hidden flex flex-col justify-between p-4 space-y-3 hover:shadow-md transition">
                    <div class="space-y-2">
                        <img src="{{ $product->first_image }}" alt="{{ $product->name }}" class="w-full h-44 object-cover rounded-xl bg-white">
                        <div class="flex items-center justify-between text-[10px]">
                            <span class="font-mono font-bold bg-stone-900 text-white px-2 py-0.5 rounded-md">{{ $product->product_code }}</span>
                            <span class="font-semibold text-emerald-600">Kho: {{ $product->stock }} SP</span>
                        </div>
                        <span class="block text-[10px] font-bold uppercase text-rose-500">{{ $product->brand->name ?? '' }} • {{ $product->skin_type }}</span>
                        <a href="{{ route('products.show', $product->slug) }}" class="block text-sm font-bold text-stone-800 hover:text-rose-600 line-clamp-2">
                            {{ $product->name }}
                        </a>
                    </div>
                    <div class="pt-2 border-t border-rose-100 flex items-center justify-between">
                        <span class="text-sm font-bold text-rose-600">{{ number_format($product->effective_price, 0, ',', '.') }}₫</span>
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="text-xs font-semibold bg-stone-900 hover:bg-rose-500 text-white px-3.5 py-1.5 rounded-full transition">
                                + Thêm giỏ
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
