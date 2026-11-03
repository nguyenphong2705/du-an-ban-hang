<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $brands = Brand::all();

        $query = Product::with(['category', 'brand']);

        // Search filter (tìm theo Mã SP SKU, Tên SP, Hoạt chất, Mô tả)
        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'ilike', "%{$search}%")
                  ->orWhere('name', 'ilike', "%{$search}%")
                  ->orWhere('ingredients', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        // Category filter (by slug or id)
        if ($category = $request->query('category')) {
            if ($category !== 'ALL') {
                $query->whereHas('category', function ($q) use ($category) {
                    $q->where('slug', $category);
                    if (is_numeric($category)) {
                        $q->orWhere('id', (int) $category);
                    }
                });
            }
        }

        // Brand filter (by id or name)
        if ($brand = $request->query('brand')) {
            if ($brand !== 'ALL') {
                $query->whereHas('brand', function ($q) use ($brand) {
                    $q->where('name', $brand);
                    if (is_numeric($brand)) {
                        $q->orWhere('id', (int) $brand);
                    }
                });
            }
        }

        // Skin Type filter
        if ($skinType = $request->query('skin_type')) {
            if ($skinType !== 'Tất cả' && $skinType !== 'ALL') {
                $query->where('skin_type', $skinType);
            }
        }

        // Price Range filter
        if ($priceRange = $request->query('price_range')) {
            if ($priceRange === 'UNDER_500') {
                $query->whereRaw('COALESCE(sale_price, price) < 500000');
            } elseif ($priceRange === '500_TO_1000') {
                $query->whereRaw('COALESCE(sale_price, price) BETWEEN 500000 AND 1000000');
            } elseif ($priceRange === 'ABOVE_1000') {
                $query->whereRaw('COALESCE(sale_price, price) > 1000000');
            }
        }

        $products = $query->latest()->get();

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    public function show(string $slug)
    {
        $product = Product::with(['category', 'brand'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProducts = Product::with(['category', 'brand'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
