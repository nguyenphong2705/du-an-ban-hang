@extends('layouts.app')

@section('title', 'Tra Cứu Đơn Hàng Đã Mua | Shine Beauty')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="text-center space-y-2">
        <span class="text-xs font-bold uppercase tracking-widest text-sage-500">Dành Cho Khách Mua Hàng</span>
        <h1 class="font-serif text-3xl font-bold text-stone-800">Tra Cứu Trạng Thái Đơn Hàng</h1>
        <p class="text-sm text-stone-500">Nhập số điện thoại đặt hàng để kiểm tra tiến độ xác nhận và giao hàng</p>
    </div>

    <!-- Search Form -->
    <form action="{{ route('orders.track') }}" method="GET" class="bg-white p-6 rounded-3xl border border-sage-100 shadow-xs flex flex-col sm:flex-row gap-3">
        <input type="text" name="phone" value="{{ $phone }}" required placeholder="Nhập số điện thoại đặt hàng (VD: 0909123456)..."
            class="flex-1 px-4 py-3 text-sm rounded-2xl border border-sage-200 bg-[#F5FAF7] focus:outline-none focus:ring-2 focus:ring-sage-300">
        <button type="submit" class="bg-sage-500 hover:bg-sage-600 text-white font-semibold px-7 py-3 rounded-2xl text-sm transition shadow-sm">
            🔍 Tra Cứu Đơn Hàng
        </button>
    </form>

    @if($phone !== '')
        @if($orders->isEmpty())
            <div class="bg-white p-10 rounded-3xl border border-sage-100 text-center space-y-3">
                <p class="font-serif text-lg font-semibold text-stone-700">
                    Không tìm thấy đơn hàng nào với số điện thoại "{{ $phone }}"
                </p>
                <p class="text-xs text-stone-400">Gợi ý: Bạn có thể thử số điện thoại mẫu <code>0909123456</code> hoặc <code>0918888999</code>.</p>
            </div>
        @else
            <div class="space-y-4">
                <p class="text-xs font-bold uppercase tracking-wider text-stone-500">
                    Tìm thấy {{ $orders->count() }} đơn hàng của SĐT {{ $phone }}
                </p>

                @foreach($orders as $order)
                    <div class="bg-white p-6 rounded-3xl border border-sage-100 shadow-xs space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-sage-50 pb-3">
                            <div>
                                <span class="font-mono font-bold text-stone-800">#ORDER-{{ $order->id }}</span>
                                <span class="text-xs text-stone-400 ml-2">{{ $order->created_at?->format('d/m/Y H:i') }}</span>
                            </div>
                            <span class="text-xs font-bold px-3 py-1 rounded-full bg-sage-50 text-sage-600 border border-sage-200">
                                Trạng thái: {{ $order->status }}
                            </span>
                        </div>

                        <div class="text-xs text-stone-600 space-y-1">
                            <p><strong>Người nhận:</strong> {{ $order->customer_name }} ({{ $order->phone }})</p>
                            <p><strong>Địa chỉ giao:</strong> {{ $order->address }}</p>
                        </div>

                        <div class="border-t border-sage-50 pt-3 flex items-center justify-between">
                            @php
                                $pmLabel = match($order->payment_method) {
                                    'BANK_TRANSFER' => 'Chuyển khoản QR Ngân hàng',
                                    'MOMO_QR' => 'Thanh toán QR MoMo',
                                    default => 'Thanh toán khi nhận hàng (COD)',
                                };
                            @endphp
                            <div class="text-xs text-stone-500">
                                {{ $order->items->count() }} sản phẩm • {{ $pmLabel }}
                                @if($order->coupon_code)
                                    • Voucher <strong class="text-wine-700 bg-wine-50 px-2 py-0.5 rounded-full border border-wine-200">{{ $order->coupon_code }}</strong>
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
    @endif
</div>
@endsection
