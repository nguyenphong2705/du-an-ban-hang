<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Class HomeController
 *
 * Điều hướng và xử lý logic hiển thị cho Trang chủ của website bán hàng mỹ phẩm.
 * Bao gồm: danh mục nổi bật, thương hiệu đối tác, sản phẩm bán chạy, và bộ khám phá sản phẩm theo loại da.
 */
class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ website.
     *
     * Chức năng:
     * - Lấy danh sách danh mục và thương hiệu kèm số lượng sản phẩm.
     * - Lấy top 6 sản phẩm bán chạy nhất (best sellers).
     * - Lọc sản phẩm phù hợp theo loại da người dùng chọn ('Da dầu', 'Da khô', 'Da nhạy cảm', 'Tất cả').
     * - Phân trang 8 sản phẩm/trang cho khối khám phá loại da (#skin-explorer).
     *
     * @param Request $request Yêu cầu HTTP chứa tham số lọc 'skin_type' (mặc định: 'Tất cả')
     * @return View Giao diện trang chủ (home.blade.php)
     */
    public function index(Request $request): View
    {
        // 1. Lấy danh sách danh mục và thương hiệu có đếm số lượng sản phẩm tương ứng
        $categories = Category::withCount('products')->get();
        $brands = Brand::withCount('products')->get();

        // 2. Lấy tổng số sản phẩm và top 6 sản phẩm bán chạy nhất (Best Sellers)
        $totalProductsCount = Cache::remember('home.total_products_count', 300, fn () => Product::count());
        $bestSellers = Product::with(['category', 'brand'])
            ->where('is_best_seller', true)
            ->latest()
            ->take(6)
            ->get();

        // 4. Xử lý bộ lọc sản phẩm theo loại da (Skin Explorer)
        $selectedSkin = $request->query('skin_type', 'Tất cả');
        $skinQuery = Product::with(['category', 'brand'])
            ->latest();

        if ($selectedSkin !== 'Tất cả') {
            $skinQuery->where(function ($q) use ($selectedSkin) {
                $q->where('skin_type', $selectedSkin)
                    ->orWhere('skin_type', 'Mọi loại da');
            });
        }

        // Mỗi trang gồm 2 hàng x 4 cột = 8 sản phẩm, giữ nguyên query string và neo vào thẻ #skin-explorer
        $skinProducts = $skinQuery->paginate(8)
            ->withQueryString()
            ->fragment('skin-explorer');

        // 5. Trả về view kèm toàn bộ dữ liệu cần thiết
        return view('home', compact(
            'categories',
            'brands',
            'bestSellers',
            'totalProductsCount',
            'skinProducts',
            'selectedSkin'
        ));
    }
}
