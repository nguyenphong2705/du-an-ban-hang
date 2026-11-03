<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = session()->get('cart', []);
        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);

        $appliedCoupon = session()->get('applied_coupon');
        $discountAmount = 0;

        if ($appliedCoupon) {
            $coupon = Coupon::where('code', $appliedCoupon['code'])
                ->where('is_active', true)
                ->first();
            if ($coupon && !$coupon->isExpired()) {
                $discountAmount = (int) round($subtotal * ($coupon->discount_percent / 100));
            } else {
                session()->forget('applied_coupon');
                $appliedCoupon = null;
            }
        }

        $totalAmount = max(0, $subtotal - $discountAmount);
        $activeCoupons = Coupon::where('is_active', true)->get();

        $successOrder = null;
        if ($orderId = $request->query('success_order_id')) {
            $successOrder = Order::with('items.product')->find($orderId);
        }

        return view('checkout.index', compact(
            'cart',
            'subtotal',
            'discountAmount',
            'totalAmount',
            'appliedCoupon',
            'activeCoupons',
            'successOrder'
        ));
    }

    public function applyCoupon(Request $request)
    {
        $code = strtoupper(trim($request->input('coupon_code', '')));
        if (!$code) {
            session()->forget('applied_coupon');
            return back()->with('error', 'Vui lòng nhập mã giảm giá.');
        }

        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon || !$coupon->is_active || $coupon->isExpired()) {
            return back()->with('error', "Mã giảm giá \"{$code}\" không tồn tại hoặc đã hết hạn.");
        }

        session()->put('applied_coupon', [
            'code' => $coupon->code,
            'discount_percent' => $coupon->discount_percent,
        ]);

        return back()->with('success', "Đã áp dụng mã giảm giá {$coupon->code} (-{$coupon->discount_percent}%)!");
    }

    public function removeCoupon()
    {
        session()->forget('applied_coupon');
        return back()->with('success', 'Đã gỡ mã giảm giá.');
    }

    public function store(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('products.index')
                ->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'address' => 'required|string|max:500',
            'note' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:COD,BANK_TRANSFER',
        ]);

        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $appliedCoupon = session()->get('applied_coupon');
        $couponCode = null;
        $discountAmount = 0;

        if ($appliedCoupon) {
            $coupon = Coupon::where('code', $appliedCoupon['code'])
                ->where('is_active', true)
                ->first();
            if ($coupon && !$coupon->isExpired()) {
                $couponCode = $coupon->code;
                $discountAmount = (int) round($subtotal * ($coupon->discount_percent / 100));
            }
        }

        $totalAmount = max(0, $subtotal - $discountAmount);

        $order = DB::transaction(function () use ($validated, $cart, $totalAmount, $couponCode, $discountAmount) {
            $newOrder = Order::create([
                'user_id' => Auth::id(),
                'customer_name' => $validated['customer_name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'note' => $validated['note'] ?? null,
                'payment_method' => $validated['payment_method'],
                'coupon_code' => $couponCode,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'status' => 'PENDING',
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $newOrder->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                Product::where('id', $item['id'])
                    ->where('stock', '>=', $item['quantity'])
                    ->decrement('stock', $item['quantity']);
            }

            return $newOrder;
        });

        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->delete();
        }
        session()->forget(['cart', 'applied_coupon']);
        session()->put('last_customer_phone', $validated['phone']);

        return redirect()->route('checkout.index', ['success_order_id' => $order->id])
            ->with('success', 'Đặt hàng thành công!');
    }

    // Trang dành cho Khách hàng tra cứu đơn hàng của mình bằng SĐT (/my-orders)
    public function trackOrders(Request $request)
    {
        $phone = trim($request->query('phone', session('last_customer_phone', '')));
        $orders = collect();

        if ($phone !== '') {
            $orders = Order::with('items.product')
                ->where('phone', 'like', '%' . $phone . '%')
                ->latest()
                ->get();
        }

        return view('checkout.track', compact('phone', 'orders'));
    }
}
