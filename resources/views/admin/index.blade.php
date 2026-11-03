@extends('layouts.app')

@section('title', 'Quản Trị Hệ Thống | Lumina Beauty Laravel 11')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
    <!-- Admin Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-8 rounded-3xl border border-rose-100 shadow-xs">
        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-rose-500">✦ Lumina Beauty Admin Portal (Laravel 11 Eloquent)</span>
            <h1 class="font-serif text-3xl font-bold text-stone-800 mt-1">Quản Trị Đơn Hàng & Sản Phẩm Mỹ Phẩm</h1>
            <p class="text-sm text-stone-500 mt-1">Kết nối trực tiếp cơ sở dữ liệu Docker PostgreSQL (shop_db)</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-rose-50 px-4 py-2.5 rounded-2xl border border-rose-100 text-xs">
                <span class="text-stone-500">Tổng đơn:</span>
                <strong class="text-rose-600 ml-1">{{ $orders->count() }}</strong>
            </div>
            <div class="bg-rose-50 px-4 py-2.5 rounded-2xl border border-rose-100 text-xs">
                <span class="text-stone-500">Sản phẩm:</span>
                <strong class="text-rose-600 ml-1">{{ $products->count() }}</strong>
            </div>
        </div>
    </div>

    <!-- 1. DANH SÁCH ĐƠN HÀNG -->
    <section class="bg-white p-6 sm:p-8 rounded-3xl border border-rose-100 shadow-xs space-y-6">
        <h2 class="font-serif text-2xl font-bold text-stone-800">1. Danh Sách Đơn Hàng ({{ $orders->count() }})</h2>

        @if($orders->isEmpty())
            <div class="p-8 rounded-2xl bg-[#FDFBF7] border border-rose-100 text-center text-sm text-stone-500">
                Chưa có đơn hàng nào. Hãy thử đặt hàng tại trang <a href="{{ route('checkout.index') }}" class="text-rose-600 font-semibold underline">/checkout</a>!
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-rose-100 text-xs uppercase tracking-wider text-stone-400">
                            <th class="py-3.5 px-4">Mã Đơn</th>
                            <th class="py-3.5 px-4">Khách Hàng</th>
                            <th class="py-3.5 px-4">Địa Chỉ</th>
                            <th class="py-3.5 px-4">Sản Phẩm</th>
                            <th class="py-3.5 px-4">Thanh Toán</th>
                            <th class="py-3.5 px-4">Tổng Tiền</th>
                            <th class="py-3.5 px-4">Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-50">
                        @foreach($orders as $order)
                            <tr class="hover:bg-rose-50/30">
                                <td class="py-4 px-4 font-mono font-bold text-xs text-stone-700">#ORDER-{{ $order->id }}</td>
                                <td class="py-4 px-4">
                                    <p class="font-semibold text-stone-800">{{ $order->customer_name }}</p>
                                    <p class="text-xs text-stone-500">{{ $order->phone }}</p>
                                </td>
                                <td class="py-4 px-4 text-xs text-stone-600 max-w-xs">{{ $order->address }}</td>
                                <td class="py-4 px-4 text-xs text-stone-600">
                                    <ul class="space-y-1">
                                        @foreach($order->items as $item)
                                            <li>• {{ $item->product->name ?? 'Sản phẩm' }} (x{{ $item->quantity }})</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="py-4 px-4 text-xs font-medium text-stone-700">{{ $order->payment_method }}</td>
                                <td class="py-4 px-4 font-bold text-rose-600">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                                <td class="py-4 px-4">
                                    <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" class="text-xs font-semibold bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-full border border-emerald-200 cursor-pointer">
                                            @foreach(['PENDING', 'CONFIRMED', 'SHIPPING', 'COMPLETED', 'CANCELLED'] as $st)
                                                <option value="{{ $st }}" {{ $order->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <!-- 2. THÊM / SỬA & XÓA SẢN PHẨM MỸ PHẨM -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Form Thêm / Sửa sản phẩm -->
        <form action="{{ $editingProduct ? route('admin.products.update', $editingProduct) : route('admin.products.store') }}" method="POST" class="lg:col-span-5 bg-white p-6 sm:p-8 rounded-3xl border border-rose-100 shadow-xs space-y-4 h-fit">
            @csrf
            @if($editingProduct)
                @method('PUT')
            @endif

            <div class="flex items-center justify-between pb-3 border-b border-rose-100">
                <h2 class="font-serif text-xl font-bold text-stone-800">
                    {{ $editingProduct ? 'Chỉnh Sửa Sản Phẩm #' . $editingProduct->id : 'Thêm Sản Phẩm Mỹ Phẩm Mới' }}
                </h2>
                @if($editingProduct)
                    <a href="{{ route('admin.index') }}" class="text-xs text-rose-600 hover:underline">Hủy sửa</a>
                @endif
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-600 mb-1">Tên sản phẩm mỹ phẩm *</label>
                <input type="text" name="name" required value="{{ old('name', $editingProduct->name ?? '') }}" placeholder="VD: Tinh Chất Phục Hồi Vitamin C 15%" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-stone-600 mb-1">Giá gốc (VNĐ) *</label>
                    <input type="number" name="price" required value="{{ old('price', $editingProduct->price ?? 450000) }}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-600 mb-1">Giá khuyến mãi (VNĐ)</label>
                    <input type="number" name="sale_price" value="{{ old('sale_price', $editingProduct->sale_price ?? 380000) }}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-stone-600 mb-1">Danh mục *</label>
                    <select name="category_id" class="w-full px-3 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]">
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ (old('category_id', $editingProduct->category_id ?? '') == $c->id) ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-600 mb-1">Thương hiệu *</label>
                    <select name="brand_id" class="w-full px-3 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]">
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}" {{ (old('brand_id', $editingProduct->brand_id ?? '') == $b->id) ? 'selected' : '' }}>
                                {{ $b->name }} ({{ $b->origin }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-stone-600 mb-1">Loại da phù hợp</label>
                    @php $currSkin = old('skin_type', $editingProduct->skin_type ?? 'Da dầu'); @endphp
                    <select name="skin_type" class="w-full px-3 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]">
                        @foreach(['Da dầu', 'Da khô', 'Da nhạy cảm', 'Mọi loại da'] as $st)
                            <option value="{{ $st }}" {{ $currSkin === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-600 mb-1">Dung tích / Trọng lượng</label>
                    <input type="text" name="volume" value="{{ old('volume', $editingProduct->volume ?? '30ml') }}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-600 mb-1">URL Ảnh sản phẩm</label>
                <input type="url" name="image" value="{{ old('image', $editingProduct->first_image ?? 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80') }}" class="w-full px-3.5 py-2 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]">
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-600 mb-1">Bảng thành phần (Ingredients)</label>
                <textarea name="ingredients" rows="2" class="w-full px-3.5 py-2 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]">{{ old('ingredients', $editingProduct->ingredients ?? 'Aqua, Niacinamide 10%, Hyaluronic Acid, Panthenol (Vitamin B5), Zinc PCA 1%.') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-600 mb-1">Mô tả công dụng</label>
                <textarea name="description" rows="2" class="w-full px-3.5 py-2 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]">{{ old('description', $editingProduct->description ?? 'Tinh chất dưỡng sáng và phục hồi màng bảo vệ da chuyên sâu chính hãng.') }}</textarea>
            </div>

            <label class="flex items-center gap-2 text-xs font-semibold text-stone-700 cursor-pointer">
                <input type="checkbox" name="is_best_seller" value="1" {{ old('is_best_seller', $editingProduct->is_best_seller ?? true) ? 'checked' : '' }} class="accent-rose-500">
                <span>Đánh dấu là Sản phẩm Bán chạy (Best Seller)</span>
            </label>

            <button type="submit" class="w-full bg-rose-500 hover:bg-rose-600 text-white font-semibold py-3 px-6 rounded-full shadow-md shadow-rose-200 transition text-sm">
                {{ $editingProduct ? 'Lưu Cập Nhật Sản Phẩm' : '+ Thêm Sản Phẩm Vào PostgreSQL' }}
            </button>
        </form>

        <!-- Danh sách sản phẩm -->
        <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-3xl border border-rose-100 shadow-xs space-y-4">
            <h2 class="font-serif text-xl font-bold text-stone-800 pb-3 border-b border-rose-100">
                Kho Sản Phẩm Mỹ Phẩm Hiện Có ({{ $products->count() }})
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-rose-100 text-xs uppercase text-stone-400">
                            <th class="py-3 px-3">Sản phẩm</th>
                            <th class="py-3 px-3">Loại da / Dung tích</th>
                            <th class="py-3 px-3">Giá bán</th>
                            <th class="py-3 px-3 text-right">Sửa / Xóa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-50">
                        @foreach($products as $prod)
                            <tr class="hover:bg-rose-50/30">
                                <td class="py-3.5 px-3 flex items-center gap-3">
                                    <img src="{{ $prod->first_image }}" alt="{{ $prod->name }}" class="w-12 h-12 rounded-xl object-cover bg-rose-50 flex-shrink-0">
                                    <div>
                                        <p class="font-semibold text-stone-800 line-clamp-1">{{ $prod->name }}</p>
                                        <p class="text-xs text-stone-400">{{ $prod->brand->name ?? '' }} • {{ $prod->category->name ?? '' }}</p>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 text-xs text-stone-600">
                                    <span class="bg-rose-50 text-rose-700 px-2.5 py-0.5 rounded-full font-medium">{{ $prod->skin_type }}</span>
                                    <span class="ml-1 text-stone-400">({{ $prod->volume }})</span>
                                </td>
                                <td class="py-3.5 px-3 font-bold text-rose-600 text-sm">
                                    {{ number_format($prod->effective_price, 0, ',', '.') }}₫
                                </td>
                                <td class="py-3.5 px-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.index', ['edit_product' => $prod->id]) }}" class="text-xs font-semibold text-blue-600 hover:underline">Sửa</a>
                                        <form action="{{ route('admin.products.destroy', $prod) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-red-500 hover:underline">Xóa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
