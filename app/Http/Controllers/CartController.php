<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class CartController
 *
 * Quản lý giỏ hàng mua sắm của khách lẻ và khách hàng đã đăng nhập.
 * Cơ chế hoạt động:
 * - Lưu trữ trong Laravel Session đối với khách vãng lai.
 * - Tự động đồng bộ hai chiều với bảng `cart_items` trong database khi khách hàng đăng nhập.
 * - Hỗ trợ các chức năng: thêm sản phẩm, cập nhật số lượng, xóa từng món, làm trống giỏ hàng và mua ngay (buy now).
 */
class CartController extends Controller
{
    /**
     * Đồng bộ giỏ hàng giữa Session và Database của tài khoản người dùng.
     *
     * Quy trình đồng bộ:
     * 1. Gộp các sản phẩm khách đã thêm vào session trước khi đăng nhập vào database.
     * 2. Lấy toàn bộ sản phẩm giỏ hàng đã lưu trước đó trong database ra session để người dùng tiếp tục mua sắm.
     *
     * @param User $user Thực thể người dùng vừa đăng nhập thành công
     * @return array Danh sách mảng các sản phẩm trong giỏ hàng sau khi đã hợp nhất
     */
    public static function syncUserCartToSession(User $user): array
    {
        $sessionCart = session()->get('cart', []);

        // 1. Gộp sản phẩm từ session (nếu khách thêm trước khi đăng nhập) vào DB
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

        // 2. Tải toàn bộ giỏ hàng đã lưu từ trước trong DB ra session
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

    /**
     * Thêm sản phẩm vào giỏ hàng.
     *
     * Chức năng:
     * - Kiểm tra tính hợp lệ của sản phẩm và số lượng đặt (1 - 50 sản phẩm).
     * - Nếu sản phẩm đã có trong giỏ hàng, cộng dồn số lượng.
     * - Nếu là khách hàng đăng nhập, tự động lưu vào bảng `cart_items` trong database.
     * - Hỗ trợ tham số `buy_now`: chuyển thẳng tới trang thanh toán (/thanh-toan).
     *
     * @param Request $request Yêu cầu HTTP chứa 'product_id', 'quantity', và tùy chọn 'buy_now'
     * @return RedirectResponse Chuyển hướng quay lại hoặc chuyển đến trang thanh toán
     */
    public function add(Request $request): RedirectResponse
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

        // Lưu bản ghi vào DB nếu tài khoản đã đăng nhập
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

        // Nếu bấm "Mua ngay" -> chuyển thẳng đến trang thanh toán
        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout.index')
                ->with('success', "Đã thêm \"{$product->name}\" vào giỏ hàng!");
        }

        return back()->with('success', "Đã thêm \"{$product->name}\" vào giỏ hàng!");
    }

    /**
     * Cập nhật số lượng của một sản phẩm trong giỏ hàng.
     *
     * Chức năng:
     * - Cập nhật số lượng mới trong Session và Database.
     * - Nếu số lượng <= 0, tự động xóa sản phẩm đó khỏi giỏ hàng.
     *
     * @param Request $request Yêu cầu HTTP chứa 'product_id' và 'quantity'
     * @return RedirectResponse Quay về trang trước kèm thông báo thành công
     */
    public function update(Request $request): RedirectResponse
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

        if ($request->boolean('open_drawer')) {
            session()->flash('open_cart_drawer', true);
        }

        return back()->with('success', 'Đã cập nhật số lượng giỏ hàng.');
    }

    /**
     * Xóa một sản phẩm cụ thể ra khỏi giỏ hàng.
     *
     * Chức năng:
     * - Xóa item theo product_id khỏi Session giỏ hàng.
     * - Xóa bản ghi tương ứng trong bảng cart_items nếu người dùng đã đăng nhập.
     *
     * @param Request $request Yêu cầu HTTP chứa 'product_id'
     * @return RedirectResponse Quay về trang trước kèm thông báo thành công
     */
    public function remove(Request $request): RedirectResponse
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

        if ($request->boolean('open_drawer')) {
            session()->flash('open_cart_drawer', true);
        }

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    /**
     * Làm trống (xóa toàn bộ) giỏ hàng.
     *
     * Chức năng:
     * - Xóa session giỏ hàng hiện tại.
     * - Xóa toàn bộ sản phẩm giỏ hàng của user trong database nếu đã đăng nhập.
     *
     * @return RedirectResponse Quay về trang trước kèm thông báo thành công
     */
    public function clear(): RedirectResponse
    {
        session()->forget('cart');
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->delete();
        }
        return back()->with('success', 'Đã làm trống giỏ hàng.');
    }
}
