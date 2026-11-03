<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = session()->get('cart', []);
        $totalAmount = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);

        $successOrder = null;
        if ($orderId = $request->query('success_order_id')) {
            $successOrder = Order::with('items.product')->find($orderId);
        }

        return view('checkout.index', compact('cart', 'totalAmount', 'successOrder'));
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

        $totalAmount = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);

        $order = DB::transaction(function () use ($validated, $cart, $totalAmount) {
            $newOrder = Order::create([
                'customer_name' => $validated['customer_name'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'note' => $validated['note'] ?? null,
                'payment_method' => $validated['payment_method'],
                'total_amount' => $totalAmount,
                'status' => 'CONFIRMED',
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $newOrder->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            return $newOrder;
        });

        session()->forget('cart');

        return redirect()->route('checkout.index', ['success_order_id' => $order->id])
            ->with('success', 'Đặt hàng thành công!');
    }
}
