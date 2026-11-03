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
        if ($selectedSkin !== 'Tất cả') {
            $skinProducts = $allProducts->filter(function ($product) use ($selectedSkin) {
                return mb_strtolower($product->skin_type) === mb_strtolower($selectedSkin)
                    || $product->skin_type === 'Mọi loại da';
            });
        } else {
            $skinProducts = $allProducts->take(8);
        }

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
