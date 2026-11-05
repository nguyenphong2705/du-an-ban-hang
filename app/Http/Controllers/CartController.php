<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public static function syncUserCartToSession(User $user): array
    {
        $sessionCart = session()->get('cart', []);

        // Gộp sản phẩm từ session (nếu khách thêm trước khi đăng nhập) vào DB
        foreach ($sessionCart as $item) {
            if (!empty($item['id'])) {
                $existing = CartItem::where('user_id', $user->id)
                    ->where('product_id', $item['id'])
                    ->first();
                if (!$existing) {
                    CartItem::create([
                        'user_id' => $user->id,
                        'product_id' => $item['id'],
                        'quantity' => (int) ($item['quantity'] ?? 1),
                    ]);
                }
            }
        }

        // Tải toàn bộ giỏ hàng đã lưu từ trước trong DB ra session
        $savedItems = CartItem::with('product.brand')
            ->where('user_id', $user->id)
            ->get();

        $mergedCart = [];
        foreach ($savedItems as $ci) {
            $product = $ci->product;
            if (!$product) {
                continue;
            }
            $mergedCart[(string) $product->id] = [
                'id' => $product->id,
                'sku' => $product->product_code,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->effective_price,
                'original_price' => $product->price,
                'image' => $product->first_image,
                'volume' => $product->volume,
                'skin_type' => $product->skin_type,
                'brand_name' => $product->brand?->name ?? 'Shine',
                'stock' => $product->stock,
                'quantity' => $ci->quantity,
                'saved_at' => $ci->updated_at?->format('d/m/Y H:i'),
            ];
        }

        session()->put('cart', $mergedCart);
        return $mergedCart;
    }

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
                'sku' => $product->product_code,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->effective_price,
                'original_price' => $product->price,
                'image' => $product->first_image,
                'volume' => $product->volume,
                'skin_type' => $product->skin_type,
                'brand_name' => $product->brand?->name ?? 'Shine',
                'stock' => $product->stock,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        if (Auth::check()) {
            CartItem::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                ],
                [
                    'quantity' => $cart[$id]['quantity'],
                ]
            );
        }

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
                if (Auth::check()) {
                    CartItem::where('user_id', Auth::id())
                        ->where('product_id', (int) $id)
                        ->delete();
                }
            } else {
                $cart[$id]['quantity'] = $qty;
                if (Auth::check()) {
                    CartItem::updateOrCreate(
                        ['user_id' => Auth::id(), 'product_id' => (int) $id],
                        ['quantity' => $qty]
                    );
                }
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

        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())
                ->where('product_id', (int) $id)
                ->delete();
        }

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    public function clear()
    {
        session()->forget('cart');
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->delete();
        }
        return back()->with('success', 'Đã làm trống giỏ hàng.');
    }
}
