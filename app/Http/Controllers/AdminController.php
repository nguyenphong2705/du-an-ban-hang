<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('items.product')->latest()->get();
        $products = Product::with(['category', 'brand'])->latest()->get();
        $categories = Category::all();
        $brands = Brand::all();

        $editingProduct = null;
        if ($editId = $request->query('edit_product')) {
            $editingProduct = Product::find($editId);
        }

        return view('admin.index', compact(
            'orders',
            'products',
            'categories',
            'brands',
            'editingProduct'
        ));
    }

    public function storeProduct(Request $request)
    {
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
            'is_best_seller' => 'nullable|boolean',
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

        return redirect()->route('admin.index')
            ->with('success', 'Đã thêm sản phẩm mỹ phẩm mới vào PostgreSQL!');
    }

    public function updateProduct(Request $request, Product $product)
    {
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

        return redirect()->route('admin.index')
            ->with('success', "Đã cập nhật sản phẩm \"{$product->name}\" thành công!");
    }

    public function destroyProduct(Product $product)
    {
        $name = $product->name;
        $product->delete();

        return redirect()->route('admin.index')
            ->with('success', "Đã xóa sản phẩm \"{$name}\" khỏi PostgreSQL.");
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:PENDING,CONFIRMED,SHIPPING,COMPLETED,CANCELLED',
        ]);

        $order->update(['status' => $request->input('status')]);

        return redirect()->route('admin.index')
            ->with('success', "Đã cập nhật trạng thái đơn hàng #{$order->id} sang {$order->status}.");
    }
}
