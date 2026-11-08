<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CheckoutService
{
    public const FREE_SHIPPING_THRESHOLD = 500000;
    public const STANDARD_SHIPPING_FEE = 15000;

    // 1. Bảng giá Phí cố định (Flat Rate)
    public const FLAT_INNER_FEE = 15000;      // 15.000₫ Nội thành (TP.HCM / Hà Nội)
    public const FLAT_PROVINCE_FEE = 35000;   // 35.000₫ Ngoại tỉnh

    // 2. Bảng giá Tính theo khu vực (Zone-based)
    public const ZONE_INNER_METRO_FEE = 15000;     // Vùng 1: Nội thành TP.HCM / Hà Nội (15.000₫)
    public const ZONE_SUBURBAN_METRO_FEE = 25000;  // Vùng 2: Ngoại thành TP.HCM / Hà Nội (25.000₫)
    public const ZONE_INTER_PROVINCE_FEE = 35000;  // Vùng 3: Liên tỉnh (35.000₫)
    public const ZONE_REMOTE_AREA_FEE = 50000;     // Vùng 4: Vùng sâu vùng xa / Hải đảo (50.000₫)

    /**
     * Tính phí vận chuyển cho đơn dưới 500.000₫ theo 2 phương thức:
     * - FLAT_RATE: Phí cố định (15.000₫ nội thành TP.HCM/Hà Nội, 35.000₫ ngoại tỉnh)
     * - ZONE_BASED: Tính theo khu vực (Nội thành 15.000₫, Ngoại thành 25.000₫, Liên tỉnh 35.000₫, Vùng sâu vùng xa 50.000₫)
     *
     * @return array{fee: int, zone_code: string, zone_label: string, mode: string}
     */
    public function calculateShippingDetails(
        int $subtotal,
        int $afterDiscount,
        string $shippingMode = 'ZONE_BASED',
        string $province = 'TP. Hồ Chí Minh',
        string $district = 'Quận 1'
    ): array {
        $mode = in_array($shippingMode, ['FLAT_RATE', 'ZONE_BASED'], true) ? $shippingMode : 'ZONE_BASED';

        if ($subtotal <= 0 || $afterDiscount >= self::FREE_SHIPPING_THRESHOLD) {
            return [
                'fee' => 0,
                'zone_code' => 'FREE_SHIP',
                'zone_label' => 'Miễn phí vận chuyển toàn quốc (Đơn từ 500.000₫)',
                'mode' => $mode,
            ];
        }

        $provNorm = mb_strtolower(trim($province), 'UTF-8');
        $distNorm = mb_strtolower(trim($district), 'UTF-8');

        $isHcmOrHanoi = str_contains($provNorm, 'hồ chí minh')
            || str_contains($provNorm, 'hcm')
            || str_contains($provNorm, 'hà nội')
            || str_contains($provNorm, 'ha noi');

        // Kiểm tra Huyện ngoại thành của TP.HCM & Hà Nội
        $suburbanKeywords = [
            'huyện', 'thị xã', 'củ chi', 'hóc môn', 'bình chánh', 'nhà bè', 'cần giờ',
            'đông anh', 'gia lâm', 'sóc sơn', 'thanh trì', 'hoài đức', 'thạch thất',
            'quốc oai', 'chương mỹ', 'đan phượng', 'thường tín', 'phú xuyên', 'ứng hòa',
            'mỹ đức', 'mê linh', 'ba vì', 'phúc thọ', 'sơn tây',
        ];
        $isSuburbanDistrict = false;
        foreach ($suburbanKeywords as $kw) {
            if ($distNorm !== '' && str_contains($distNorm, $kw)) {
                $isSuburbanDistrict = true;
                break;
            }
        }

        // Kiểm tra Tỉnh vùng sâu vùng xa / Miền núi cao & Hải đảo
        $remoteProvinces = [
            'hà giang', 'cao bằng', 'bắc kạn', 'lạng sơn', 'tuyên quang', 'lào cai',
            'yên bái', 'điện biên', 'hòa bình', 'lai châu', 'sơn la', 'kon tum',
            'gia lai', 'đắk lắk', 'đắk nông', 'bình phước', 'cà mau', 'bạc liêu',
            'sóc trăng', 'trà vinh',
        ];
        $remoteDistricts = [
            'phú quốc', 'côn đảo', 'lý sơn', 'cát hải', 'bạch long vĩ',
            'hoàng sa', 'trường sa', 'phú quý', 'cồn cỏ', 'kiên hải', 'cô tô', 'vân đồn',
        ];

        $isRemoteArea = false;
        foreach ($remoteProvinces as $rp) {
            if (str_contains($provNorm, $rp)) {
                $isRemoteArea = true;
                break;
            }
        }
        if (!$isRemoteArea) {
            foreach ($remoteDistricts as $rd) {
                if (str_contains($distNorm, $rd)) {
                    $isRemoteArea = true;
                    break;
                }
            }
        }

        // CHẾ ĐỘ 1: PHÍ CỐ ĐỊNH (FLAT RATE)
        if ($mode === 'FLAT_RATE') {
            if ($isHcmOrHanoi && !$isSuburbanDistrict) {
                return [
                    'fee' => self::FLAT_INNER_FEE,
                    'zone_code' => 'FLAT_INNER',
                    'zone_label' => 'Phí cố định (Flat Rate) — Nội thành: 15.000₫',
                    'mode' => $mode,
                ];
            }

            return [
                'fee' => self::FLAT_PROVINCE_FEE,
                'zone_code' => 'FLAT_OUTER',
                'zone_label' => 'Phí cố định (Flat Rate) — Ngoại tỉnh / Ngoại thành: 35.000₫',
                'mode' => $mode,
            ];
        }

        // CHẾ ĐỘ 2: TÍNH THEO KHU VỰC (ZONE-BASED)
        if ($isHcmOrHanoi) {
            if ($isSuburbanDistrict) {
                return [
                    'fee' => self::ZONE_SUBURBAN_METRO_FEE,
                    'zone_code' => 'ZONE_SUBURBAN',
                    'zone_label' => 'Khu vực Ngoại thành TP.HCM / Hà Nội: 25.000₫',
                    'mode' => $mode,
                ];
            }

            return [
                'fee' => self::ZONE_INNER_METRO_FEE,
                'zone_code' => 'ZONE_INNER',
                'zone_label' => 'Khu vực Nội thành TP.HCM / Hà Nội: 15.000₫',
                'mode' => $mode,
            ];
        }

        if ($isRemoteArea) {
            return [
                'fee' => self::ZONE_REMOTE_AREA_FEE,
                'zone_code' => 'ZONE_REMOTE',
                'zone_label' => 'Khu vực Vùng sâu vùng xa / Miền núi & Hải đảo: 50.000₫',
                'mode' => $mode,
            ];
        }

        return [
            'fee' => self::ZONE_INTER_PROVINCE_FEE,
            'zone_code' => 'ZONE_PROVINCE',
            'zone_label' => 'Khu vực Liên tỉnh (Tỉnh / Thành phố khác): 35.000₫',
            'mode' => $mode,
        ];
    }

    /**
     * Tính toán đầy đủ giá trị giỏ hàng, giảm giá voucher (tối đa 1 mã) và phí vận chuyển.
     */
    public function calculateCartSummary(
        array $cart,
        ?array $appliedCouponSession,
        string $shippingMode = 'ZONE_BASED',
        string $province = 'TP. Hồ Chí Minh',
        string $district = 'Quận 1'
    ): array {
        $subtotal = (int) collect($cart)->sum(
            static fn (array $item): int => (int) $item['price'] * (int) $item['quantity']
        );

        $discountAmount = 0;
        $validCoupon = null;

        if (!empty($appliedCouponSession['code']) && $subtotal > 0) {
            $coupon = Coupon::query()
                ->where('code', $appliedCouponSession['code'])
                ->where('is_active', true)
                ->whereDate('expires_at', '>=', today())
                ->first();

            if ($coupon && $subtotal >= (int) ($coupon->min_order_amount ?? 0)) {
                $discountAmount = (int) round($subtotal * ($coupon->discount_percent / 100));
                $validCoupon = [
                    'code' => $coupon->code,
                    'discount_percent' => (int) $coupon->discount_percent,
                ];
            }
        }

        $afterDiscount = max(0, $subtotal - $discountAmount);
        $shippingDetails = $this->calculateShippingDetails($subtotal, $afterDiscount, $shippingMode, $province, $district);
        $shippingFee = $shippingDetails['fee'];

        $amountNeededForFreeShip = ($subtotal > 0 && $afterDiscount < self::FREE_SHIPPING_THRESHOLD)
            ? (self::FREE_SHIPPING_THRESHOLD - $afterDiscount)
            : 0;

        $totalAmount = $subtotal > 0 ? ($afterDiscount + $shippingFee) : 0;

        return [
            'subtotal' => $subtotal,
            'discountAmount' => $discountAmount,
            'afterDiscount' => $afterDiscount,
            'shippingFee' => $shippingFee,
            'shippingMode' => $shippingDetails['mode'],
            'shippingZoneCode' => $shippingDetails['zone_code'],
            'shippingZoneLabel' => $shippingDetails['zone_label'],
            'freeShippingThreshold' => self::FREE_SHIPPING_THRESHOLD,
            'standardShippingFee' => self::STANDARD_SHIPPING_FEE,
            'amountNeededForFreeShip' => $amountNeededForFreeShip,
            'totalAmount' => $totalAmount,
            'appliedCoupon' => $validCoupon,
        ];
    }

    /**
     * Lấy danh sách voucher đang hoạt động (có Cache ngắn hạn để giảm tải DB).
     */
    public function getActiveCoupons(): Collection
    {
        return Cache::remember('active_coupons_list', now()->addMinutes(5), static function (): Collection {
            return Coupon::query()
                ->select(['id', 'code', 'discount_percent', 'description', 'min_order_amount', 'expires_at', 'is_active'])
                ->where('is_active', true)
                ->whereDate('expires_at', '>=', today())
                ->orderByDesc('discount_percent')
                ->get();
        });
    }

    /**
     * Xử lý tạo đơn hàng an toàn với Pessimistic Locking (lockForUpdate) và Batch Insert.
     */
    public function placeOrder(array $validatedData, array $cart, ?array $appliedCouponSession, ?int $userId): Order
    {
        $shippingMode = (string) ($validatedData['shipping_mode'] ?? 'ZONE_BASED');
        $province = (string) ($validatedData['province'] ?? 'TP. Hồ Chí Minh');
        $district = (string) ($validatedData['district'] ?? 'Quận 1');

        $summary = $this->calculateCartSummary($cart, $appliedCouponSession, $shippingMode, $province, $district);

        return DB::transaction(function () use ($validatedData, $cart, $summary, $userId): Order {
            $productIds = collect($cart)->pluck('id')->map(static fn ($id): int => (int) $id)->unique()->values()->all();

            $products = Product::query()
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $now = now();
            $orderItemsPayload = [];

            foreach ($cart as $item) {
                $productId = (int) $item['id'];
                $quantity = (int) $item['quantity'];
                /** @var Product|null $product */
                $product = $products->get($productId);

                if (!$product || $product->stock < $quantity) {
                    $productName = $product->name ?? ($item['name'] ?? "ID #{$productId}");
                    throw new RuntimeException("Sản phẩm \"{$productName}\" không đủ số lượng tồn kho.");
                }

                $product->decrement('stock', $quantity);

                $orderItemsPayload[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => (int) $product->effective_price,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            $noteWithShipping = $validatedData['note'] ?? null;
            if ($summary['shippingFee'] > 0) {
                $shipTag = "[Ship: {$summary['shippingZoneLabel']}]";
                $noteWithShipping = $noteWithShipping ? "{$noteWithShipping} {$shipTag}" : $shipTag;
            }

            $order = Order::query()->create([
                'user_id' => $userId,
                'customer_name' => $validatedData['customer_name'],
                'phone' => $validatedData['phone'],
                'address' => $validatedData['full_address'],
                'note' => $noteWithShipping,
                'payment_method' => $validatedData['payment_method'],
                'coupon_code' => $summary['appliedCoupon']['code'] ?? null,
                'discount_amount' => $summary['discountAmount'],
                'total_amount' => $summary['totalAmount'],
                'status' => 'PENDING',
            ]);

            foreach ($orderItemsPayload as &$row) {
                $row['order_id'] = $order->id;
            }
            unset($row);

            OrderItem::query()->insert($orderItemsPayload);

            if ($userId !== null) {
                CartItem::query()->where('user_id', $userId)->delete();
            }

            return $order;
        });
    }
}
