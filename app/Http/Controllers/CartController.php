<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:50',
        ]);

        $product = Product::with('brand')->findOrFail($request->input('product_id'));
        $quantity = (int) $request->input('quantity', 1);

        $cart = session()->get('cart', []);
        $id = (string) $product->id;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->effective_price,
                'original_price' => $product->price,
                'image' => $product->first_image,
                'volume' => $product->volume,
                'skin_type' => $product->skin_type,
                'brand_name' => $product->brand?->name ?? 'Lumina',
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout.index')
                ->with('success', "Đã thêm \"{$product->name}\" vào giỏ hàng!");
        }

        return back()->with('success', "Đã thêm \"{$product->name}\" vào giỏ hàng!");
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required|integer',
        ]);

        $cart = session()->get('cart', []);
        $id = (string) $request->input('product_id');
        $qty = (int) $request->input('quantity');

        if (isset($cart[$id])) {
            if ($qty <= 0) {
                unset($cart[$id]);
            } else {
                $cart[$id]['quantity'] = $qty;
            }
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Đã cập nhật số lượng giỏ hàng.');
    }

    public function remove(Request $request)
    {
        $id = (string) $request->input('product_id');
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Đã làm trống giỏ hàng.');
    }
}
