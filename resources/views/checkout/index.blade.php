@extends('layouts.app')

@section('title', 'Thanh Toán & Đặt Hàng | Lumina Beauty')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    @if($successOrder)
        <div class="max-w-2xl mx-auto py-8 text-center">
            <div class="bg-white p-10 rounded-3xl border border-rose-100 shadow-lg space-y-6">
                <div class="w-20 h-20 mx-auto rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl font-bold">
                    ✓
                </div>
                <span class="text-xs font-bold uppercase tracking-widest text-rose-500">Đặt Hàng Thành Công</span>
                <h1 class="font-serif text-3xl font-bold text-stone-800">
                    Cảm Ơn {{ $successOrder->customer_name }} Đã Mua Sắm Tại Lumina Beauty!
                </h1>
                <p class="text-sm text-stone-600 leading-relaxed">
                    Đơn hàng của bạn đã được lưu trực tiếp vào bảng <code>orders</code> trong cơ sở dữ liệu PostgreSQL qua Laravel Eloquent.
                </p>

                <div class="bg-[#FDFBF7] p-5 rounded-2xl border border-rose-100 text-left text-sm space-y-2">
                    <div class="flex justify-between">
                        <span class="text-stone-500">Mã đơn hàng:</span>
                        <span class="font-mono font-bold text-stone-800">#ORDER-{{ $successOrder->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">Số điện thoại:</span>
                        <span class="font-semibold text-stone-800">{{ $successOrder->phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">Địa chỉ nhận hàng:</span>
                        <span class="font-semibold text-stone-800">{{ $successOrder->address }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">Phương thức thanh toán:</span>
                        <span class="font-semibold text-stone-800">
                            {{ $successOrder->payment_method === 'COD' ? 'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản ngân hàng' }}
                        </span>
                    </div>
                    <div class="flex justify-between border-t border-rose-100 pt-2">
                        <span class="text-stone-500">Tổng thanh toán:</span>
                        <span class="font-bold text-rose-600 text-base">{{ number_format($successOrder->total_amount, 0, ',', '.') }}₫</span>
                    </div>
                </div>

                <div class="flex flex-wrap justify-center gap-4 pt-2">
                    <a href="{{ route('products.index') }}" class="bg-rose-500 hover:bg-rose-600 text-white font-semibold px-7 py-3 rounded-full text-sm transition">
                        Tiếp Tục Mua Sắm
                    </a>
                    <a href="{{ route('admin.index') }}" class="bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold px-7 py-3 rounded-full text-sm transition border border-rose-200">
                        Xem Đơn Hàng Trong Quản Trị (/admin)
                    </a>
                </div>
            </div>
        </div>
    @else
        <div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-rose-600 hover:text-rose-700 mb-2">
                ← Tiếp tục chọn mỹ phẩm
            </a>
            <h1 class="font-serif text-3xl font-bold text-stone-800">Thanh Toán & Giao Hàng</h1>
        </div>

        @if(empty($cart))
            <div class="bg-white rounded-3xl border border-rose-100 p-12 text-center space-y-4">
                <p class="font-serif text-xl text-stone-700">Giỏ hàng của bạn hiện chưa có sản phẩm nào</p>
                <a href="{{ route('products.index') }}" class="inline-block bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold px-6 py-3 rounded-full transition">
                    Khám phá sản phẩm ngay
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Shipping & Payment Form -->
                <form action="{{ route('checkout.store') }}" method="POST" class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-3xl border border-rose-100 shadow-xs space-y-6">
                    @csrf
                    <h2 class="font-serif text-xl font-bold text-stone-800 pb-3 border-b border-rose-100">
                        1. Thông Tin Người Nhận
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-2">Họ và tên người nhận *</label>
                            <input type="text" name="customer_name" required value="{{ old('customer_name') }}" placeholder="VD: Nguyễn Thị Minh Châu" class="w-full px-4 py-3 text-sm rounded-2xl border border-rose-200 bg-[#FDFBF7] focus:outline-none focus:ring-2 focus:ring-rose-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-2">Số điện thoại liên hệ *</label>
                            <input type="tel" name="phone" required value="{{ old('phone') }}" placeholder="VD: 0909 123 456" class="w-full px-4 py-3 text-sm rounded-2xl border border-rose-200 bg-[#FDFBF7] focus:outline-none focus:ring-2 focus:ring-rose-300">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-2">Địa chỉ giao hàng chi tiết *</label>
                        <input type="text" name="address" required value="{{ old('address') }}" placeholder="Số nhà, tên đường, Phường/Xã, Quận/Huyện, Tỉnh/Thành phố" class="w-full px-4 py-3 text-sm rounded-2xl border border-rose-200 bg-[#FDFBF7] focus:outline-none focus:ring-2 focus:ring-rose-300">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-2">Ghi chú đơn hàng (Tùy chọn)</label>
                        <textarea name="note" rows="3" placeholder="Ghi chú giao giờ hành chính hoặc gói quà tặng..." class="w-full px-4 py-3 text-sm rounded-2xl border border-rose-200 bg-[#FDFBF7] focus:outline-none focus:ring-2 focus:ring-rose-300">{{ old('note') }}</textarea>
                    </div>

                    <h2 class="font-serif text-xl font-bold text-stone-800 pt-2 pb-3 border-b border-rose-100">
                        2. Phương Thức Thanh Toán
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="p-4 rounded-2xl border border-rose-200 bg-[#FDFBF7] flex items-start gap-3 cursor-pointer hover:bg-rose-50/50">
                            <input type="radio" name="payment_method" value="COD" checked class="mt-1 accent-rose-500">
                            <div>
                                <p class="text-sm font-bold text-stone-800">Thanh toán khi nhận hàng (COD)</p>
                                <p class="text-xs text-stone-500 mt-0.5">Kiểm tra tem niêm phong mỹ phẩm trước khi thanh toán</p>
                            </div>
                        </label>

                        <label class="p-4 rounded-2xl border border-rose-200 bg-[#FDFBF7] flex items-start gap-3 cursor-pointer hover:bg-rose-50/50">
                            <input type="radio" name="payment_method" value="BANK_TRANSFER" class="mt-1 accent-rose-500">
                            <div>
                                <p class="text-sm font-bold text-stone-800">Chuyển khoản Ngân hàng</p>
                                <p class="text-xs text-stone-500 mt-0.5">Tặng kèm bộ sample mặt nạ dưỡng ẩm cao cấp</p>
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-rose-500 hover:bg-rose-600 text-white font-semibold py-4 px-8 rounded-full shadow-lg shadow-rose-200 transition text-base">
                        Hoàn Tất Đặt Hàng ({{ number_format($totalAmount, 0, ',', '.') }}₫)
                    </button>
                </form>

                <!-- Cart Summary -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-rose-100 shadow-xs space-y-6">
                        <h2 class="font-serif text-xl font-bold text-stone-800 pb-3 border-b border-rose-100">
                            Chi Tiết Giỏ Hàng ({{ count($cart) }})
                        </h2>

                        <div class="space-y-4 max-h-96 overflow-y-auto pr-1">
                            @foreach($cart as $item)
                                <div class="flex gap-3.5 items-center pb-4 border-b border-rose-50">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-16 h-16 rounded-2xl object-cover bg-rose-50 flex-shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-stone-800 line-clamp-1">{{ $item['name'] }}</p>
                                        <p class="text-xs text-stone-400">{{ $item['volume'] }} • {{ $item['skin_type'] }}</p>
                                        <p class="text-sm font-bold text-rose-600 mt-0.5">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫</p>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                            <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                            <button type="submit" class="w-7 h-7 rounded-full bg-rose-50 text-stone-600 hover:text-rose-600 font-bold text-xs">-</button>
                                        </form>
                                        <span class="w-6 text-center text-xs font-bold">{{ $item['quantity'] }}</span>
                                        <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                            <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                            <button type="submit" class="w-7 h-7 rounded-full bg-rose-50 text-stone-600 hover:text-rose-600 font-bold text-xs">+</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="space-y-2 text-sm pt-2">
                            <div class="flex justify-between text-stone-500">
                                <span>Tạm tính</span>
                                <span>{{ number_format($totalAmount, 0, ',', '.') }}₫</span>
                            </div>
                            <div class="flex justify-between text-stone-500">
                                <span>Phí vận chuyển</span>
                                <span class="text-emerald-600 font-semibold">Miễn phí</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold text-stone-800 pt-3 border-t border-rose-100">
                                <span>Tổng thanh toán</span>
                                <span class="text-rose-600">{{ number_format($totalAmount, 0, ',', '.') }}₫</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
