<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Class AdminController
 *
 * Điều khiển toàn bộ Trung tâm Quản trị viên (Chủ Shop):
 * - Bảng điều khiển KPI: Doanh thu, số lượng đơn hàng, khách hàng, biểu đồ phân tích và cảnh báo hàng sắp hết.
 * - Quản lý Sản phẩm: Thêm, sửa, xóa, tải lên ảnh, lọc nâng cao theo SKU/tồn kho/danh mục, chuẩn hóa dữ liệu.
 * - Quản lý Đơn hàng: Lọc trạng thái, xem chi tiết, đổi trạng thái (chờ xác nhận, đang giao, hoàn thành, hủy).
 * - Quản lý Khuyến mãi (Voucher): Tạo mã giảm giá, thiết lập hạn sử dụng và đơn tối thiểu, bật/tắt/xóa mã.
 * - Quản lý Danh mục & Thương hiệu: Thêm nhanh danh mục và hãng sản xuất phục vụ phân loại sản phẩm.
 */
class AdminController extends Controller
{
    /**
     * Xác thực quyền Quản trị viên của Chủ Shop trước khi truy cập bất kỳ chức năng nào.
     *
     * @return RedirectResponse|null Chuyển hướng về trang đăng nhập admin nếu chưa đăng nhập hoặc không có quyền admin; ngược lại trả về null
     */
    private function authorizeShopOwner(): ?RedirectResponse
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login')
                ->with('error', 'Vui lòng đăng nhập bằng tài khoản Quản trị viên của Shop để truy cập.');
        }

        return null;
    }

    /**
     * Trang chủ Bảng điều khiển Quản trị viên.
     *
     * Tự động điều hướng đến tab phù hợp dựa trên tham số query:
     * - Nếu có tham số sản phẩm ('edit_product', 'stock_filter', 'category_filter', 'product_search') -> tab Sản phẩm.
     * - Nếu có tham số đơn hàng ('order_search', 'order_status') -> tab Đơn hàng.
     * - Mặc định -> tab Thống kê KPI & Phân tích.
     *
     * @param Request $request Yêu cầu HTTP
     * @return View|RedirectResponse Giao diện quản trị tương ứng
     */
    public function index(Request $request)
    {
        if ($request->hasAny(['edit_product', 'stock_filter', 'category_filter', 'product_search'])) {
            return $this->renderAdminPage($request, 'products');
        }
        if ($request->hasAny(['order_search', 'order_status'])) {
            return $this->renderAdminPage($request, 'orders');
        }
        return $this->renderAdminPage($request, 'analytics');
    }

    /**
     * Hiển thị tab Thống kê KPI & Phân tích số liệu kinh doanh.
     *
     * @param Request $request Yêu cầu HTTP
     * @return View|RedirectResponse
     */
    public function analytics(Request $request)
    {
        return $this->renderAdminPage($request, 'analytics');
    }

    /**
     * Hiển thị tab Quản lý Đơn hàng của khách.
     *
     * @param Request $request Yêu cầu HTTP
     * @return View|RedirectResponse
     */
    public function orders(Request $request)
    {
        return $this->renderAdminPage($request, 'orders');
    }

    /**
     * Hiển thị tab Quản lý Kho & Danh sách Sản phẩm.
     *
     * @param Request $request Yêu cầu HTTP
     * @return View|RedirectResponse
     */
    public function products(Request $request)
    {
        return $this->renderAdminPage($request, 'products');
    }

    /**
     * Hiển thị tab Quản lý Mã giảm giá (Voucher).
     *
     * @param Request $request Yêu cầu HTTP
     * @return View|RedirectResponse
     */
    public function coupons(Request $request)
    {
        return $this->renderAdminPage($request, 'coupons');
    }

    /**
     * Hàm phụ trách gom dữ liệu, tính toán các chỉ số kinh doanh và hiển thị trang quản trị.
     *
     * Dữ liệu được tính toán bao gồm:
     * - Doanh thu thực tế (đơn đã giao hoặc đã thanh toán), doanh thu tiềm năng.
     * - Thống kê số lượng đơn hàng theo từng trạng thái (chờ duyệt, đang giao, hoàn thành, hủy).
     * - Doanh thu và số lượng bán của từng sản phẩm cụ thể.
     * - Cảnh báo hàng hết kho (stock = 0) và hàng sắp hết (stock <= 15).
     * - Dữ liệu biểu đồ doanh thu theo ngày và phân bổ sản phẩm theo danh mục.
     * - Xử lý tìm kiếm và bộ lọc đơn hàng / sản phẩm theo các tiêu chí quản trị viên chọn.
     *
     * @param Request $request Yêu cầu HTTP
     * @param string $activePage Tên tab đang kích hoạt ('analytics', 'orders', 'products', 'coupons')
     * @return View|RedirectResponse
     */
    private function renderAdminPage(Request $request, string $activePage)
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
            ->sortByDesc(fn ($p) => $p->sold_quantity * 1000000 + ($p->is_best_seller ? 500000 : 0))
            ->values()
            ->take(8);

        // Chỉ số KPI cốt lõi
        $totalOrdersCount = $allOrders->count();
        $deliveredOrders = $allOrders->where('status', 'COMPLETED');
        $realizedRevenue = $deliveredOrders->sum('total_amount');
        $potentialRevenue = $allOrders->whereNotIn('status', ['CANCELLED'])->sum('total_amount');

        $pendingOrdersCount = $allOrders->where('status', 'PENDING')->count();
        $shippingOrdersCount = $allOrders->where('status', 'SHIPPING')->count();
        $completedOrdersCount = $deliveredOrders->count();
        $cancelledOrdersCount = $allOrders->where('status', 'CANCELLED')->count();

        // Cảnh báo kho hàng
        $outOfStockCount = $allProducts->where('stock', '<=', 0)->count();
        $lowStockCount = $allProducts->where('stock', '>', 0)->where('stock', '<=', 15)->count();
        $totalStockUnits = $allProducts->sum('stock');

        // Biểu đồ Doanh thu 7 ngày gần nhất
        $revenueLast7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $dayKey = $day->format('d/m');
            $dayRevenue = $allOrders
                ->filter(fn ($o) => $o->created_at->format('Y-m-d') === $day->format('Y-m-d') && $o->status !== 'CANCELLED')
                ->sum('total_amount');
            $revenueLast7Days[] = [
                'day' => $dayKey,
                'revenue' => $dayRevenue,
            ];
        }

        // Biểu đồ Sản phẩm theo Danh mục
        $categoryDistribution = $categories->map(fn ($cat) => [
            'name' => $cat->name,
            'count' => $cat->products_count,
        ]);

        // Bộ lọc đơn hàng (Order Filters)
        $orderSearch = trim((string) $request->input('order_search', ''));
        $orderStatus = $request->input('order_status', 'ALL');

        $filteredOrders = $allOrders;
        if ($orderStatus !== 'ALL') {
            $filteredOrders = $filteredOrders->where('status', $orderStatus);
        }
        if ($orderSearch !== '') {
            $kw = mb_strtolower($orderSearch);
            $filteredOrders = $filteredOrders->filter(function ($ord) use ($kw) {
                return str_contains((string) $ord->id, $kw)
                    || str_contains(mb_strtolower($ord->customer_name ?? ''), $kw)
                    || str_contains(mb_strtolower($ord->phone ?? ''), $kw)
                    || str_contains(mb_strtolower($ord->address ?? ''), $kw);
            });
        }

        // Bộ lọc sản phẩm (Product Filters & Quick Stock Tabs)
        $productSearch = trim((string) $request->input('product_search', ''));
        $stockFilter = $request->input('stock_filter', 'ALL');
        $categoryFilter = $request->input('category_filter', 'ALL');

        $filteredProducts = $allProducts;
        if ($categoryFilter !== 'ALL') {
            $filteredProducts = $filteredProducts->where('category_id', (int) $categoryFilter);
        }
        if ($stockFilter === 'BEST_SELLER') {
            $filteredProducts = $filteredProducts->where('is_best_seller', true);
        } elseif ($stockFilter === 'OUT_OF_STOCK') {
            $filteredProducts = $filteredProducts->where('stock', '<=', 0);
        } elseif ($stockFilter === 'LOW_STOCK') {
            $filteredProducts = $filteredProducts->where('stock', '>', 0)->where('stock', '<=', 15);
        } elseif ($stockFilter === 'IN_STOCK') {
            $filteredProducts = $filteredProducts->where('stock', '>', 15);
        }

        if ($productSearch !== '') {
            $kw = mb_strtolower($productSearch);
            $filteredProducts = $filteredProducts->filter(function ($p) use ($kw) {
                return str_contains(mb_strtolower($p->sku ?? ''), $kw)
                    || str_contains(mb_strtolower($p->name), $kw)
                    || str_contains(mb_strtolower($p->brand->name ?? ''), $kw)
                    || str_contains(mb_strtolower($p->skin_type ?? ''), $kw);
            });
        }

        // Sản phẩm đang được chọn để chỉnh sửa (nếu có)
        $editingProduct = null;
        if ($editId = $request->input('edit_product')) {
            $editingProduct = Product::find($editId);
        }

        $stats = [
            'total_orders' => $totalOrdersCount,
            'realized_revenue' => $realizedRevenue,
            'potential_revenue' => $potentialRevenue,
            'customers_count' => $customersCount,
            'pending_orders' => $pendingOrdersCount,
            'shipping_orders' => $shippingOrdersCount,
            'completed_orders' => $completedOrdersCount,
            'cancelled_orders' => $cancelledOrdersCount,
            'out_of_stock_count' => $outOfStockCount,
            'low_stock_count' => $lowStockCount,
            'total_stock_units' => $totalStockUnits,
            'revenue_chart' => $revenueLast7Days,
            'category_chart' => $categoryDistribution,
        ];

        return view('admin.index', compact(
            'activePage',
            'filteredOrders',
            'filteredProducts',
            'categories',
            'brands',
            'coupons',
            'bestSellingProducts',
            'editingProduct',
            'orderSearch',
            'orderStatus',
            'productSearch',
            'stockFilter',
            'categoryFilter',
            'stats'
        ));
    }

    /**
     * REST API lọc nhanh danh sách sản phẩm phục vụ AJAX tại trang Quản trị.
     *
     * Hỗ trợ lọc theo:
     * - category_filter: ID danh mục hoặc 'ALL'
     * - stock_filter: 'ALL', 'BEST_SELLER', 'OUT_OF_STOCK', 'LOW_STOCK', 'IN_STOCK'
     * - product_search: Từ khóa tìm kiếm theo SKU, tên sản phẩm, thương hiệu, loại da
     *
     * @param Request $request Yêu cầu HTTP chứa tham số lọc
     * @return JsonResponse Danh sách sản phẩm dạng JSON kèm tổng số bản ghi
     */
    public function filterProductsApi(Request $request): JsonResponse
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'category_filter' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if ($value !== 'ALL' && (!ctype_digit((string) $value) || !Category::where('id', (int) $value)->exists())) {
                    $fail('Giá trị category_filter không hợp lệ.');
                }
            }],
            'stock_filter' => ['nullable', 'string', 'in:ALL,BEST_SELLER,OUT_OF_STOCK,LOW_STOCK,IN_STOCK'],
            'product_search' => ['nullable', 'string', 'max:255'],
        ]);

        $categoryFilter = $validated['category_filter'] ?? 'ALL';
        $stockFilter = $validated['stock_filter'] ?? 'ALL';
        $productSearch = trim($validated['product_search'] ?? '');

        $allProducts = Product::with(['category', 'brand', 'orderItems'])->latest()->get();
        foreach ($allProducts as $prod) {
            $prod->sold_quantity = (int) $prod->orderItems->sum('quantity');
            $prod->sold_revenue = (int) $prod->orderItems->sum(fn ($oi) => $oi->quantity * $oi->price);
        }

        $filtered = $allProducts->filter(function ($product) use ($productSearch, $stockFilter, $categoryFilter) {
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

        return response()->json([
            'filters' => [
                'category_filter' => $categoryFilter,
                'stock_filter' => $stockFilter,
                'product_search' => $productSearch,
            ],
            'total_count' => $allProducts->count(),
            'filtered_count' => $filtered->count(),
            'data' => $filtered->map(fn (Product $p) => [
                'id' => $p->id,
                'sku' => $p->product_code,
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => $p->price,
                'sale_price' => $p->sale_price,
                'final_price' => $p->final_price,
                'stock' => $p->stock,
                'volume' => $p->volume,
                'skin_type' => $p->skin_type,
                'is_best_seller' => $p->is_best_seller,
                'sold_quantity' => $p->sold_quantity ?? 0,
                'image' => $p->first_image,
                'category_id' => $p->category_id,
                'category_name' => $p->category->name ?? '',
                'brand_id' => $p->brand_id,
                'brand_name' => $p->brand->name ?? '',
            ]),
        ]);
    }

    /**
     * Xử lý lưu tệp hình ảnh sản phẩm được tải lên từ máy tính.
     *
     * @param Request $request Yêu cầu HTTP chứa file 'image_file'
     * @return string|null Đường dẫn tương đối của tệp đã lưu trong /uploads/products/ hoặc null
     */
    private function handleProductImageUpload(Request $request): ?string
    {
        if (!$request->hasFile('image_file')) {
            return null;
        }

        $file = $request->file('image_file');
        $uploadDir = public_path('uploads/products');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = 'product-' . time() . '-' . Str::random(6) . '.' . $ext;
        $file->move($uploadDir, $filename);

        return '/uploads/products/' . $filename;
    }

    /**
     * Tải lên ảnh nhúng trực tiếp trong trình soạn thảo mô tả chi tiết sản phẩm.
     *
     * @param Request $request Yêu cầu HTTP chứa file ảnh 'image'
     * @return JsonResponse Trả về URL của ảnh đã lưu để chèn vào trình soạn thảo
     */
    public function uploadEditorImage(Request $request): JsonResponse
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $file = $request->file('image');
        $uploadDir = public_path('uploads/products');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = 'desc-' . time() . '-' . Str::random(6) . '.' . $ext;
        $file->move($uploadDir, $filename);

        return response()->json([
            'url' => '/uploads/products/' . $filename,
        ]);
    }

    /**
     * Xóa các tệp ảnh sản phẩm cục bộ trên ổ cứng máy chủ khi ảnh bị thay đổi hoặc sản phẩm bị xóa.
     *
     * @param array|null $images Mảng đường dẫn ảnh cần xóa
     * @return void
     */
    private function deleteLocalProductImages(?array $images): void
    {
        if (!is_array($images)) {
            return;
        }

        foreach ($images as $img) {
            if (is_string($img) && str_starts_with($img, '/uploads/products/')) {
                $fullPath = public_path(ltrim($img, '/'));
                if (is_file($fullPath)) {
                    @unlink($fullPath);
                }
            }
        }
    }

    /**
     * Thêm mới một sản phẩm mỹ phẩm vào hệ thống.
     *
     * Quy trình:
     * 1. Xác thực dữ liệu đầu vào (tên, giá, giá khuyến mãi, danh mục, thương hiệu, loại da, dung tích, tồn kho, thành phần, mô tả).
     * 2. Tự động sinh mã SKU chuẩn `LMN-SPxxx` nếu người dùng để trống.
     * 3. Tự động tạo slug thân thiện cho SEO.
     * 4. Xử lý lưu ảnh tải lên hoặc dùng ảnh mặc định.
     * 5. Lưu vào cơ sở dữ liệu và chuyển về tab Sản phẩm.
     *
     * @param Request $request Yêu cầu HTTP chứa thông tin sản phẩm
     * @return RedirectResponse
     */
    public function storeProduct(Request $request): RedirectResponse
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
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'ingredients' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $slug = Str::slug($validated['name']) . '-' . rand(1000, 9999);
        $nextId = (int) Product::max('id') + 1;
        $sku = !empty($validated['sku'])
            ? strtoupper(trim($validated['sku']))
            : ('LMN-SP' . str_pad((string) $nextId, 3, '0', STR_PAD_LEFT));

        $uploadedPath = $this->handleProductImageUpload($request);

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
                $uploadedPath ?? 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80',
            ],
            'ingredients' => $validated['ingredients'] ?? 'Aqua, Niacinamide 10%, Hyaluronic Acid, Panthenol (Vitamin B5), Tocopherol.',
            'description' => $validated['description'] ?? 'Sản phẩm mỹ phẩm chính hãng cao cấp phân phối tại Shine Beauty.',
            'is_best_seller' => $request->has('is_best_seller'),
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', "Đã thêm sản phẩm mới [{$sku}] thành công!");
    }

    /**
     * Cập nhật thông tin chi tiết của sản phẩm.
     *
     * Chức năng:
     * - Kiểm tra tính duy nhất của mã SKU ngoại trừ chính sản phẩm đang sửa.
     * - Xử lý thay thế ảnh mới và tự động dọn dẹp ảnh cũ trong thư mục máy chủ.
     * - Cập nhật các trường thông tin: giá, tồn kho, thành phần hoạt chất, mô tả, nhãn bán chạy.
     *
     * @param Request $request Yêu cầu HTTP
     * @param Product $product Thực thể sản phẩm cần cập nhật
     * @return RedirectResponse
     */
    public function updateProduct(Request $request, Product $product): RedirectResponse
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
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'remove_image' => 'nullable|boolean',
            'ingredients' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $images = $product->images;
        $uploadedPath = $this->handleProductImageUpload($request);

        if ($uploadedPath) {
            $this->deleteLocalProductImages($product->images);
            $images = [$uploadedPath];
        } elseif ($request->boolean('remove_image')) {
            $this->deleteLocalProductImages($product->images);
            $images = ['https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80'];
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

        return redirect()->route('admin.products.index')
            ->with('success', "Đã cập nhật sản phẩm [{$product->product_code}] \"{$product->name}\" thành công!");
    }

    /**
     * Chuẩn hóa hàng loạt toàn bộ mã SKU và tồn kho của sản phẩm.
     *
     * Chức năng tiện ích:
     * - Đặt lại mã SKU theo thứ tự `LMN-SP001`, `LMN-SP002`,...
     * - Đặt lại số lượng tồn kho mặc định thành 20 sản phẩm cho tất cả các mặt hàng.
     *
     * @return RedirectResponse
     */
    public function standardizeProducts(): RedirectResponse
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

    /**
     * Cập nhật nhanh số lượng hàng tồn kho của một sản phẩm cụ thể.
     *
     * @param Request $request Yêu cầu HTTP chứa 'stock'
     * @param Product $product Thực thể sản phẩm cần sửa
     * @return RedirectResponse
     */
    public function updateProductStock(Request $request, Product $product): RedirectResponse
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

    /**
     * Bật hoặc tắt trạng thái "Bán chạy" (Best Seller) của một sản phẩm.
     *
     * @param Product $product Thực thể sản phẩm cần đổi trạng thái
     * @return RedirectResponse
     */
    public function toggleBestSeller(Product $product): RedirectResponse
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $product->update(['is_best_seller' => !$product->is_best_seller]);

        return back()->with('success', "Đã cập nhật trạng thái Bán chạy (Best Seller) cho \"{$product->name}\"!");
    }

    /**
     * Xóa sản phẩm và toàn bộ hình ảnh tệp tin liên quan khỏi hệ thống.
     *
     * @param Product $product Thực thể sản phẩm cần xóa
     * @return RedirectResponse
     */
    public function destroyProduct(Product $product): RedirectResponse
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $name = $product->name;
        $this->deleteLocalProductImages($product->images);
        $product->delete();

        return back()->with('success', "Đã xóa sản phẩm \"{$name}\" khỏi hệ thống.");
    }

    /**
     * Cập nhật trạng thái xử lý của một đơn hàng.
     *
     * Các trạng thái hợp lệ:
     * - PENDING: Chờ xác nhận
     * - CONFIRMED: Đã xác nhận
     * - SHIPPING: Đang giao hàng
     * - COMPLETED: Đã hoàn thành (đã giao)
     * - CANCELLED: Đã hủy
     *
     * @param Request $request Yêu cầu HTTP chứa 'status'
     * @param Order $order Thực thể đơn hàng cần cập nhật
     * @return RedirectResponse
     */
    public function updateOrderStatus(Request $request, Order $order): RedirectResponse
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

    /**
     * Xóa một đơn hàng khỏi hệ thống quản lý.
     *
     * @param Order $order Thực thể đơn hàng cần xóa
     * @return RedirectResponse
     */
    public function destroyOrder(Order $order): RedirectResponse
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $id = $order->id;
        $order->delete();

        return back()->with('success', "Đã xóa đơn hàng #ORDER-{$id}.");
    }

    /**
     * Tạo và phát hành mã giảm giá (Voucher) mới.
     *
     * Thuộc tính voucher:
     * - code: Mã giảm giá (viết hoa tự động, ví dụ: SHINE10, BEAUTY20).
     * - discount_percent: Tỷ lệ giảm giá (1% - 90%).
     * - min_order_amount: Giá trị đơn hàng tối thiểu cần đạt để được áp dụng.
     * - expires_at: Ngày giờ hết hạn của mã.
     *
     * @param Request $request Yêu cầu HTTP
     * @return RedirectResponse
     */
    public function storeCoupon(Request $request): RedirectResponse
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

    /**
     * Kích hoạt hoặc tạm ngưng một mã giảm giá.
     *
     * @param Coupon $coupon Thực thể voucher cần đổi trạng thái
     * @return RedirectResponse
     */
    public function toggleCoupon(Coupon $coupon): RedirectResponse
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $coupon->update(['is_active' => !$coupon->is_active]);

        return back()->with('success', "Đã cập nhật trạng thái mã giảm giá {$coupon->code}.");
    }

    /**
     * Xóa mã giảm giá khỏi hệ thống.
     *
     * @param Coupon $coupon Thực thể voucher cần xóa
     * @return RedirectResponse
     */
    public function destroyCoupon(Coupon $coupon): RedirectResponse
    {
        if ($redirect = $this->authorizeShopOwner()) {
            return $redirect;
        }

        $code = $coupon->code;
        $coupon->delete();

        return back()->with('success', "Đã xóa mã giảm giá {$code}.");
    }

    /**
     * Thêm mới danh mục sản phẩm mỹ phẩm (ví dụ: Serum, Kem chống nắng, Sữa rửa mặt).
     *
     * @param Request $request Yêu cầu HTTP chứa 'name' và tùy chọn 'image'
     * @return RedirectResponse
     */
    public function storeCategory(Request $request): RedirectResponse
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

    /**
     * Thêm mới thương hiệu mỹ phẩm đối tác (ví dụ: La Roche-Posay, Anessa, Cerave).
     *
     * @param Request $request Yêu cầu HTTP chứa 'name', 'origin' (xuất xứ) và tùy chọn 'logo'
     * @return RedirectResponse
     */
    public function storeBrand(Request $request): RedirectResponse
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
