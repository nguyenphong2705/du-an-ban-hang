<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ApplyCouponRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Coupon;
use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RuntimeException;

/**
 * Class CheckoutController
 *
 * Điều hướng và xử lý quy trình thanh toán, áp dụng mã giảm giá, đặt hàng và tra cứu đơn hàng:
 * - Hiển thị trang thanh toán, tính toán tạm tính, phí ship, số tiền giảm voucher.
 * - Áp dụng và gỡ bỏ mã giảm giá (voucher) cho đơn hàng.
 * - Đặt hàng và lưu thông tin đơn hàng cùng các món hàng vào cơ sở dữ liệu.
 * - Tra cứu nhanh lịch sử đơn hàng theo số điện thoại (chống SQL injection và IDOR).
 */
class CheckoutController extends Controller
{
    /** Ngưỡng miễn phí vận chuyển (mặc định: 499.000₫) */
    public const FREE_SHIPPING_THRESHOLD = CheckoutService::FREE_SHIPPING_THRESHOLD;

    /** Phí vận chuyển tiêu chuẩn toàn quốc (mặc định: 30.000₫) */
    public const STANDARD_SHIPPING_FEE = CheckoutService::STANDARD_SHIPPING_FEE;

    /**
     * Khởi tạo Controller với CheckoutService.
     *
     * @param CheckoutService $checkoutService Dịch vụ tính toán đơn hàng và thanh toán
     */
    public function __construct(
        private readonly CheckoutService $checkoutService
    ) {
    }

    /**
     * Hiển thị trang thanh toán và tổng quan đơn hàng.
     *
     * Chức năng:
     * - Lấy giỏ hàng từ session và thông tin mã giảm giá đang áp dụng.
     * - Tính toán chi tiết: tiền hàng, phí ship, số tiền giảm giá, tổng thanh toán.
     * - Tự động gỡ voucher nếu giỏ hàng không còn đủ điều kiện áp dụng.
     * - Hiển thị danh sách các voucher đang kích hoạt để khách hàng lựa chọn.
     * - Bảo vệ chống IDOR: chỉ cho phép xem đơn hàng vừa tạo nếu trùng phiên hoặc đúng user.
     *
     * @param Request $request Yêu cầu HTTP (có thể kèm 'success_order_id')
     * @return View Giao diện thanh toán (checkout.index)
     */
    public function index(Request $request): View
    {
        $cart = session()->get('cart', []);
        $appliedCouponSession = session()->get('applied_coupon');

        $summary = $this->checkoutService->calculateCartSummary($cart, $appliedCouponSession);

        // Nếu mã giảm giá trong session hết hạn hoặc không đủ điều kiện đơn tối thiểu thì tự động gỡ
        if ($appliedCouponSession && $summary['appliedCoupon'] === null) {
            session()->forget('applied_coupon');
        }

        $activeCoupons = $this->checkoutService->getActiveCoupons();

        // Chống IDOR: Chỉ cho phép xem đơn hàng vừa đặt trong phiên hiện tại hoặc của chính user đang đăng nhập
        $successOrder = null;
        if ($orderId = $request->integer('success_order_id')) {
            $lastPhone = session('last_customer_phone');
            $successOrder = Order::query()
                ->with(['items.product:id,sku,name,slug,images'])
                ->where('id', $orderId)
                ->where(function ($query) use ($lastPhone): void {
                    if (Auth::check()) {
                        $query->where('user_id', Auth::id());
                    }
                    if (!empty($lastPhone)) {
                        $query->orWhere('phone', $lastPhone);
                    }
                })
                ->first();
        }

        return view('checkout.index', array_merge($summary, [
            'cart' => $cart,
            'activeCoupons' => $activeCoupons,
            'successOrder' => $successOrder,
        ]));
    }

    /**
     * Áp dụng mã giảm giá (voucher) vào đơn hàng.
     *
     * Quy tắc nghiệp vụ:
     * - Mỗi đơn hàng chỉ áp dụng tối đa 01 voucher.
     * - Kiểm tra mã có tồn tại, còn hạn sử dụng và đạt giá trị đơn hàng tối thiểu không.
     *
     * @param ApplyCouponRequest $request Form request kiểm tra định dạng mã giảm giá
     * @return RedirectResponse Quay lại trang thanh toán kèm thông báo kết quả
     */
    public function applyCoupon(ApplyCouponRequest $request): RedirectResponse
    {
        $code = $request->validated('coupon_code');
        $existingCoupon = session()->get('applied_coupon');

        // Chỉ cho phép áp dụng tối đa 01 voucher trên mỗi đơn hàng
        if (!empty($existingCoupon['code'])) {
            if ($existingCoupon['code'] === $code) {
                return back()->with('error', "Mã giảm giá {$code} đang được áp dụng trên đơn hàng này.");
            }

            return back()->with(
                'error',
                "Mỗi đơn hàng chỉ được áp dụng tối đa 01 voucher (đang dùng mã {$existingCoupon['code']}). Vui lòng bấm \"Gỡ mã\" hiện tại nếu muốn đổi sang mã {$code}."
            );
        }

        $coupon = Coupon::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->first();

        if (!$coupon || $coupon->isExpired()) {
            return back()->with('error', "Mã giảm giá \"{$code}\" không tồn tại hoặc đã hết hạn.");
        }

        $cart = session()->get('cart', []);
        $subtotal = (int) collect($cart)->sum(
            static fn (array $item): int => (int) $item['price'] * (int) $item['quantity']
        );

        if ($subtotal < (int) ($coupon->min_order_amount ?? 0)) {
            return back()->with(
                'error',
                "Mã giảm giá {$coupon->code} (-{$coupon->discount_percent}%) yêu cầu giá trị đơn hàng tối thiểu " . number_format((int) $coupon->min_order_amount, 0, ',', '.') . '₫.'
            );
        }

        session()->put('applied_coupon', [
            'code' => $coupon->code,
            'discount_percent' => (int) $coupon->discount_percent,
        ]);

        return back()->with('success', "Đã áp dụng mã giảm giá {$coupon->code} (-{$coupon->discount_percent}%)! (Mỗi đơn chỉ áp dụng 01 voucher)");
    }

    /**
     * Gỡ bỏ mã giảm giá đang áp dụng khỏi đơn hàng.
     *
     * @return RedirectResponse Quay lại trang thanh toán kèm thông báo
     */
    public function removeCoupon(): RedirectResponse
    {
        session()->forget('applied_coupon');

        return back()->with('success', 'Đã gỡ mã giảm giá.');
    }

    /**
     * Xử lý xác nhận đặt hàng và lưu đơn hàng vào database.
     *
     * Quy trình xử lý:
     * 1. Kiểm tra giỏ hàng có sản phẩm hay không.
     * 2. Gọi CheckoutService để tạo đơn hàng (Order) và chi tiết đơn hàng (OrderItem) trong Database Transaction.
     * 3. Trừ tồn kho sản phẩm tương ứng.
     * 4. Xóa session giỏ hàng và voucher sau khi đặt hàng thành công.
     * 5. Lưu số điện thoại vào session để hỗ trợ tra cứu đơn và chuyển đến trang thông báo thành công.
     *
     * @param StoreOrderRequest $request Form request xác thực thông tin người nhận (tên, sđt, địa chỉ, phương thức thanh toán)
     * @return RedirectResponse Chuyển hướng đến trang thành công hoặc quay lại nếu có lỗi
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('products.index')
                ->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        try {
            $order = $this->checkoutService->placeOrder(
                $request->validated(),
                $cart,
                session()->get('applied_coupon'),
                Auth::id() ? (int) Auth::id() : null
            );
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        session()->forget(['cart', 'applied_coupon']);
        session()->put('last_customer_phone', $order->phone);

        return redirect()->route('checkout.index', ['success_order_id' => $order->id])
            ->with('success', 'Đặt hàng thành công!');
    }

    /**
     * Tra cứu lịch sử đơn hàng nhanh bằng số điện thoại khách hàng.
     *
     * Tính năng an toàn:
     * - Lọc sạch ký tự lạ, ngăn chặn SQL Wildcard Injection (ký tự % hoặc _).
     * - Chỉ tìm kiếm khi số điện thoại có từ 9 chữ số trở lên.
     * - Tự động điền số điện thoại vừa đặt hàng gần nhất từ session nếu có.
     *
     * @param Request $request Yêu cầu HTTP chứa tham số query 'phone'
     * @return View Giao diện tra cứu đơn hàng (checkout.track)
     */
    public function trackOrders(Request $request): View
    {
        $rawPhone = trim((string) $request->query('phone', (string) session('last_customer_phone', '')));
        // Chỉ giữ ký tự số và dấu + để ngăn SQL LIKE Wildcard Injection (% hoặc _)
        $phone = preg_replace('/[^0-9+]/', '', $rawPhone) ?: '';
        $orders = collect();

        if (strlen($phone) >= 9) {
            $orders = Order::query()
                ->with(['items.product:id,sku,name,slug,images'])
                ->where('phone', $phone)
                ->latest()
                ->limit(20)
                ->get();
        }

        return view('checkout.track', compact('phone', 'orders'));
    }
}
