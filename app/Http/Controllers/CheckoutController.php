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

class CheckoutController extends Controller
{
    public const FREE_SHIPPING_THRESHOLD = CheckoutService::FREE_SHIPPING_THRESHOLD;
    public const STANDARD_SHIPPING_FEE = CheckoutService::STANDARD_SHIPPING_FEE;

    public function __construct(
        private readonly CheckoutService $checkoutService
    ) {
    }

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

    public function removeCoupon(): RedirectResponse
    {
        session()->forget('applied_coupon');

        return back()->with('success', 'Đã gỡ mã giảm giá.');
    }

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
