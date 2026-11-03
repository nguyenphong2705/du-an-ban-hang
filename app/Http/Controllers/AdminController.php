<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    private function authorizeShopOwner()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login')
                ->with('error', 'Vui lòng đăng nhập bằng tài khoản Chủ Shop (Admin) để truy cập trang quản trị.');
        }

        return null;
    }

    public function index(Request $request)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $orders = Order::with('items.product')->latest()->get();
        $products = Product::with(['category', 'brand'])->latest()->get();
        $categories = Category::withCount('products')->get();
        $brands = Brand::withCount('products')->get();
        $coupons = Coupon::latest()->get();

        $editingProduct = null;
        if ($editId = $request->query('edit_product')) {
            $editingProduct = Product::find($editId);
        }

        $activeTab = $request->query('tab', 'dashboard');

        // KPI Metrics cho Chủ Shop
        $stats = [
            'total_revenue' => $orders->where('status', '!=', 'CANCELLED')->sum('total_amount'),
            'completed_revenue' => $orders->where('status', 'COMPLETED')->sum('total_amount'),
            'total_orders' => $orders->count(),
            'pending_orders' => $orders->whereIn('status', ['PENDING', 'CONFIRMED'])->count(),
            'total_products' => $products->count(),
            'low_stock_products' => $products->where('stock', '<=', 35)->count(),
            'active_coupons' => $coupons->where('is_active', true)->count(),
        ];

        return view('admin.index', compact(
            'orders',
            'products',
            'categories',
            'brands',
            'coupons',
            'editingProduct',
            'activeTab',
            'stats'
        ));
    }

    public function storeProduct(Request $request)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $validated = $request->validate([
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

        Product::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'price' => (int) $validated['price'],
            'sale_price' => !empty($validated['sale_price']) ? (int) $validated['sale_price'] : null,
            'category_id' => (int) $validated['category_id'],
            'brand_id' => (int) $validated['brand_id'],
            'skin_type' => $validated['skin_type'],
            'volume' => $validated['volume'],
            'stock' => (int) ($validated['stock'] ?? 80),
            'images' => [
                $validated['image'] ?? 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80',
            ],
            'ingredients' => $validated['ingredients'] ?? 'Aqua, Niacinamide 10%, Hyaluronic Acid, Panthenol (Vitamin B5), Tocopherol.',
            'description' => $validated['description'] ?? 'Sản phẩm mỹ phẩm chính hãng cao cấp phân phối tại Lumina Beauty.',
            'is_best_seller' => $request->has('is_best_seller'),
        ]);

        return redirect()->route('admin.index', ['tab' => 'products'])
            ->with('success', 'Đã thêm sản phẩm mỹ phẩm mới vào kho hàng!');
    }

    public function updateProduct(Request $request, Product $product)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $validated = $request->validate([
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
            'name' => $validated['name'],
            'price' => (int) $validated['price'],
            'sale_price' => !empty($validated['sale_price']) ? (int) $validated['sale_price'] : null,
            'category_id' => (int) $validated['category_id'],
            'brand_id' => (int) $validated['brand_id'],
            'skin_type' => $validated['skin_type'],
            'volume' => $validated['volume'],
            'stock' => (int) ($validated['stock'] ?? $product->stock),
            'images' => $images,
            'ingredients' => $validated['ingredients'] ?? $product->ingredients,
            'description' => $validated['description'] ?? $product->description,
            'is_best_seller' => $request->has('is_best_seller'),
        ]);

        return redirect()->route('admin.index', ['tab' => 'products'])
            ->with('success', "Đã cập nhật sản phẩm \"{$product->name}\" thành công!");
    }

    public function destroyProduct(Product $product)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $name = $product->name;
        $product->delete();

        return redirect()->route('admin.index', ['tab' => 'products'])
            ->with('success', "Đã xóa sản phẩm \"{$name}\" khỏi hệ thống.");
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

        return redirect()->route('admin.index', ['tab' => 'orders'])
            ->with('success', "Đã cập nhật trạng thái đơn hàng #ORDER-{$order->id} sang {$order->status}.");
    }

    public function destroyOrder(Order $order)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $id = $order->id;
        $order->delete();

        return redirect()->route('admin.index', ['tab' => 'orders'])
            ->with('success', "Đã xóa đơn hàng #ORDER-{$id}.");
    }

    // Quản lý Mã giảm giá (Coupons / Vouchers)
    public function storeCoupon(Request $request)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'discount_percent' => 'required|integer|min:1|max:90',
            'expires_at' => 'required|date',
        ]);

        Coupon::create([
            'code' => strtoupper(trim($validated['code'])),
            'discount_percent' => (int) $validated['discount_percent'],
            'expires_at' => $validated['expires_at'],
            'is_active' => true,
        ]);

        return redirect()->route('admin.index', ['tab' => 'coupons'])
            ->with('success', 'Đã phát hành mã giảm giá (Voucher) mới thành công!');
    }

    public function toggleCoupon(Coupon $coupon)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $coupon->update(['is_active' => !$coupon->is_active]);

        return redirect()->route('admin.index', ['tab' => 'coupons'])
            ->with('success', "Đã cập nhật trạng thái mã giảm giá {$coupon->code}.");
    }

    public function destroyCoupon(Coupon $coupon)
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $code = $coupon->code;
        $coupon->delete();

        return redirect()->route('admin.index', ['tab' => 'coupons'])
            ->with('success', "Đã xóa mã giảm giá {$code}.");
    }

    // Quản lý Danh mục & Thương hiệu
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

        return redirect()->route('admin.index', ['tab' => 'catalog'])
            ->with('success', "Đã thêm danh mục \"{$validated['name']}\"!");
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

        return redirect()->route('admin.index', ['tab' => 'catalog'])
            ->with('success', "Đã thêm thương hiệu \"{$validated['name']}\"!");
    }
}
