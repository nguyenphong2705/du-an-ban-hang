<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('products')->get();
        $brands = Brand::withCount('products')->get();

        $allProducts = Product::with(['category', 'brand'])
            ->latest()
            ->get();

        $bestSellers = $allProducts->where('is_best_seller', true)->take(6);

        $selectedSkin = $request->query('skin_type', 'Tất cả');
        $skinQuery = Product::with(['category', 'brand'])
            ->latest();

        if ($selectedSkin !== 'Tất cả') {
            $skinQuery->where(function ($q) use ($selectedSkin) {
                $q->where('skin_type', $selectedSkin)
                    ->orWhere('skin_type', 'Mọi loại da');
            });
        }

        // Mỗi trang gồm 2 hàng x 4 cột = 8 sản phẩm
        $skinProducts = $skinQuery->paginate(8)
            ->withQueryString()
            ->fragment('skin-explorer');

        return view('home', compact(
            'categories',
            'brands',
            'bestSellers',
            'allProducts',
            'skinProducts',
            'selectedSkin'
        ));
    }
}
