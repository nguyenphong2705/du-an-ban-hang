<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    private function authorizeShopOwner()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login')
                ->with('error', 'Vui lòng đăng nhập bằng tài khoản Quản trị viên của Shop để truy cập.');
        }

        return null;
    }

    public function index(Request $request)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        // Toàn bộ dữ liệu gốc để tính KPI & Thống kê
        $allOrders = Order::with('items.product')->latest()->get();
        $allProducts = Product::with(['category', 'brand', 'orderItems'])->latest()->get();
        $categories = Category::withCount('products')->get();
        $brands = Brand::withCount('products')->get();
        $coupons = Coupon::latest()->get();
        $customersCount = User::where('is_admin', false)->count();

        // Tính toán số lượng đã bán & doanh thu từng sản phẩm
        foreach ($allProducts as $prod) {
            $prod->sold_quantity = (int) $prod->orderItems->sum('quantity');
            $prod->sold_revenue = (int) $prod->orderItems->sum(fn ($oi) => $oi->quantity * $oi->price);
        }

        // Thống kê Sản phẩm Bán chạy (Best Sellers & Top Sold)
        $bestSellingProducts = $allProducts
            ->filter(fn ($p) => $p->is_best_seller || $p->sold_quantity > 0)
            ->sortByDesc(fn ($p) => ($p->sold_quantity * 1000) + ($p->is_best_seller ? 500 : 0))
            ->values();

        // Thống kê Sản phẩm Hết hàng (stock == 0) & Sắp hết hàng (1 <= stock <= 15)
        $outOfStockProducts = $allProducts->where('stock', '<=', 0)->values();
        $lowStockProducts = $allProducts->filter(fn ($p) => $p->stock > 0 && $p->stock <= 15)->sortBy('stock')->values();

        // Bộ lọc Tra cứu Đơn hàng cho Quản trị viên
        $orderSearch = trim($request->query('order_search', ''));
        $orderStatus = $request->query('order_status', 'ALL');

        $orders = $allOrders->filter(function ($order) use ($orderSearch, $orderStatus) {
            if ($orderStatus !== 'ALL' && $order->status !== $orderStatus) {
                return false;
            }
            if ($orderSearch !== '') {
                $kw = mb_strtolower($orderSearch);
                $matchId = str_contains(mb_strtolower('#order-' . $order->id), $kw) || (string) $order->id === $kw;
                $matchName = str_contains(mb_strtolower($order->customer_name), $kw);
                $matchPhone = str_contains(mb_strtolower($order->phone), $kw);
                return $matchId || $matchName || $matchPhone;
            }
            return true;
        })->values();

        // Bộ lọc Xem & Quản lý Sản phẩm trong Kho
        $productSearch = trim($request->query('product_search', ''));
        $stockFilter = $request->query('stock_filter', 'ALL');
        $categoryFilter = $request->query('category_filter', 'ALL');

        $products = $allProducts->filter(function ($product) use ($productSearch, $stockFilter, $categoryFilter) {
            if ($categoryFilter !== 'ALL' && (string) $product->category_id !== (string) $categoryFilter) {
                return false;
            }
            if ($stockFilter === 'BEST_SELLER' && !$product->is_best_seller) {
                return false;
            }
            if ($stockFilter === 'OUT_OF_STOCK' && $product->stock > 0) {
                return false;
            }
            if ($stockFilter === 'LOW_STOCK' && ($product->stock <= 0 || $product->stock > 15)) {
                return false;
            }
            if ($stockFilter === 'IN_STOCK' && $product->stock <= 15) {
                return false;
            }
            if ($productSearch !== '') {
                $kw = mb_strtolower($productSearch);
                return str_contains(mb_strtolower($product->sku ?? ''), $kw)
                    || str_contains(mb_strtolower($product->name), $kw)
                    || str_contains(mb_strtolower($product->brand->name ?? ''), $kw)
                    || str_contains(mb_strtolower($product->skin_type ?? ''), $kw);
            }
            return true;
        })->values();

        $editingProduct = null;
        if ($editId = $request->query('edit_product')) {
            $editingProduct = Product::find($editId);
        }

        // KPI Metrics tổng hợp cho Quản trị viên
        $stats = [
            'total_revenue' => $allOrders->where('status', '!=', 'CANCELLED')->sum('total_amount'),
            'completed_revenue' => $allOrders->where('status', 'COMPLETED')->sum('total_amount'),
            'total_orders' => $allOrders->count(),
            'pending_orders' => $allOrders->whereIn('status', ['PENDING', 'CONFIRMED', 'SHIPPING'])->count(),
            'total_products' => $allProducts->count(),
            'best_seller_count' => $bestSellingProducts->count(),
            'out_of_stock_count' => $outOfStockProducts->count(),
            'low_stock_count' => $lowStockProducts->count(),
            'active_coupons' => $coupons->where('is_active', true)->count(),
            'customers_count' => $customersCount,
        ];

        return view('admin.index', compact(
            'orders',
            'allOrders',
            'products',
            'allProducts',
            'bestSellingProducts',
            'outOfStockProducts',
            'lowStockProducts',
            'categories',
            'brands',
            'coupons',
            'editingProduct',
            'orderSearch',
            'orderStatus',
            'productSearch',
            'stockFilter',
            'categoryFilter',
            'stats'
        ));
    }

    public function storeProduct(Request $request)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $validated = $request->validate([
            'sku' => 'nullable|string|max:50|unique:products,sku',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'skin_type' => 'required|string|max:100',
            'volume' => 'required|string|max:100',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|url',
            'ingredients' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $slug = Str::slug($validated['name']) . '-' . rand(1000, 9999);
        $nextId = (int) Product::max('id') + 1;
        $sku = !empty($validated['sku'])
            ? strtoupper(trim($validated['sku']))
            : ('LMN-SP' . str_pad((string) $nextId, 3, '0', STR_PAD_LEFT));

        Product::create([
            'sku' => $sku,
            'name' => $validated['name'],
            'slug' => $slug,
            'price' => (int) $validated['price'],
            'sale_price' => !empty($validated['sale_price']) ? (int) $validated['sale_price'] : null,
            'category_id' => (int) $validated['category_id'],
            'brand_id' => (int) $validated['brand_id'],
            'skin_type' => $validated['skin_type'],
            'volume' => $validated['volume'],
            'stock' => isset($validated['stock']) ? (int) $validated['stock'] : 20,
            'images' => [
                $validated['image'] ?? 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80',
            ],
            'ingredients' => $validated['ingredients'] ?? 'Aqua, Niacinamide 10%, Hyaluronic Acid, Panthenol (Vitamin B5), Tocopherol.',
            'description' => $validated['description'] ?? 'Sản phẩm mỹ phẩm chính hãng cao cấp phân phối tại Lumina Beauty.',
            'is_best_seller' => $request->has('is_best_seller'),
        ]);

        return redirect()->route('admin.index')
            ->with('success', "Đã thêm sản phẩm mới [{$sku}] với tồn kho 20 SP thành công!");
    }

    public function updateProduct(Request $request, Product $product)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $validated = $request->validate([
            'sku' => 'nullable|string|max:50|unique:products,sku,' . $product->id,
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'skin_type' => 'required|string|max:100',
            'volume' => 'required|string|max:100',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|url',
            'ingredients' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $images = $product->images;
        if (!empty($validated['image'])) {
            $images = [$validated['image']];
        }

        $product->update([
            'sku' => !empty($validated['sku']) ? strtoupper(trim($validated['sku'])) : $product->product_code,
            'name' => $validated['name'],
            'price' => (int) $validated['price'],
            'sale_price' => !empty($validated['sale_price']) ? (int) $validated['sale_price'] : null,
            'category_id' => (int) $validated['category_id'],
            'brand_id' => (int) $validated['brand_id'],
            'skin_type' => $validated['skin_type'],
            'volume' => $validated['volume'],
            'stock' => isset($validated['stock']) ? (int) $validated['stock'] : $product->stock,
            'images' => $images,
            'ingredients' => $validated['ingredients'] ?? $product->ingredients,
            'description' => $validated['description'] ?? $product->description,
            'is_best_seller' => $request->has('is_best_seller'),
        ]);

        return redirect()->route('admin.index')
            ->with('success', "Đã cập nhật sản phẩm [{$product->product_code}] \"{$product->name}\" thành công!");
    }

    public function standardizeProducts()
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $products = Product::orderBy('id')->get();
        foreach ($products as $index => $product) {
            $product->update([
                'sku' => 'LMN-SP' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'stock' => 20,
            ]);
        }

        return back()->with('success', 'Đã chuẩn hóa toàn bộ sản phẩm: Tất cả đều có Mã sản phẩm (LMN-SP001...) rõ ràng và tồn kho đồng bộ 20 sản phẩm!');
    }

    public function updateProductStock(Request $request, Product $product)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product->update(['stock' => (int) $validated['stock']]);

        return back()->with('success', "Đã cập nhật tồn kho sản phẩm \"{$product->name}\" thành {$product->stock} sản phẩm!");
    }

    public function toggleBestSeller(Product $product)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $product->update(['is_best_seller' => !$product->is_best_seller]);

        return back()->with('success', "Đã cập nhật trạng thái Bán chạy (Best Seller) cho \"{$product->name}\"!");
    }

    public function destroyProduct(Product $product)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $name = $product->name;
        $product->delete();

        return back()->with('success', "Đã xóa sản phẩm \"{$name}\" khỏi hệ thống.");
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $request->validate([
            'status' => 'required|in:PENDING,CONFIRMED,SHIPPING,COMPLETED,CANCELLED',
        ]);

        $order->update(['status' => $request->input('status')]);

        return back()->with('success', "Đã cập nhật trạng thái đơn hàng #ORDER-{$order->id} sang {$order->status}.");
    }

    public function destroyOrder(Order $order)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $id = $order->id;
        $order->delete();

        return back()->with('success', "Đã xóa đơn hàng #ORDER-{$id}.");
    }

    public function storeCoupon(Request $request)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'discount_percent' => 'required|integer|min:1|max:90',
            'description' => 'nullable|string|max:255',
            'min_order_amount' => 'nullable|numeric|min:0',
            'expires_at' => 'required|date',
        ]);

        Coupon::create([
            'code' => strtoupper(trim($validated['code'])),
            'discount_percent' => (int) $validated['discount_percent'],
            'description' => $validated['description'] ?? "Giảm {$validated['discount_percent']}% cho đơn hàng mỹ phẩm",
            'min_order_amount' => (int) ($validated['min_order_amount'] ?? 0),
            'expires_at' => $validated['expires_at'],
            'is_active' => true,
        ]);

        return back()->with('success', 'Đã phát hành mã giảm giá (Voucher) mới thành công!');
    }

    public function toggleCoupon(Coupon $coupon)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $coupon->update(['is_active' => !$coupon->is_active]);

        return back()->with('success', "Đã cập nhật trạng thái mã giảm giá {$coupon->code}.");
    }

    public function destroyCoupon(Coupon $coupon)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $code = $coupon->code;
        $coupon->delete();

        return back()->with('success', "Đã xóa mã giảm giá {$code}.");
    }

    public function storeCategory(Request $request)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|url',
        ]);

        Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . rand(10, 99),
            'image' => $validated['image'] ?? 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80',
        ]);

        return back()->with('success', "Đã thêm danh mục \"{$validated['name']}\"!");
    }

    public function storeBrand(Request $request)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'origin' => 'required|string|max:100',
            'logo' => 'nullable|url',
        ]);

        Brand::create([
            'name' => $validated['name'],
            'origin' => $validated['origin'],
            'logo' => $validated['logo'] ?? 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=400&q=80',
        ]);

        return back()->with('success', "Đã thêm thương hiệu \"{$validated['name']}\"!");
    }
}
