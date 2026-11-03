"use client";

import React, { useState, useEffect } from "react";
import Link from "next/link";
import { useCart } from "@/context/CartContext";
import { formatVND } from "@/lib/prisma";
import {
  CheckCircle2,
  CreditCard,
  Truck,
  Plus,
  Minus,
  Trash2,
  ArrowLeft,
  ShieldCheck,
  TicketPercent,
  Sparkles,
  X,
  Flame,
} from "lucide-react";

interface CouponItem {
  id: string;
  code: string;
  discountPercent: number;
  description?: string | null;
  minOrderAmount: number;
  maxDiscount?: number | null;
  expiresAt: string;
  isActive: boolean;
}

export default function CheckoutPage() {
  const { items, updateQuantity, removeFromCart, clearCart, totalAmount } =
    useCart();

  const [customerName, setCustomerName] = useState("");
  const [phone, setPhone] = useState("");
  const [address, setAddress] = useState("");
  const [note, setNote] = useState("");
  const [paymentMethod, setPaymentMethod] = useState<"COD" | "BANK_TRANSFER">(
    "COD"
  );

  // Coupon / Voucher states
  const [couponInput, setCouponInput] = useState("");
  const [availableCoupons, setAvailableCoupons] = useState<CouponItem[]>([]);
  const [appliedCoupon, setAppliedCoupon] = useState<CouponItem | null>(null);
  const [couponLoading, setCouponLoading] = useState(false);
  const [couponError, setCouponError] = useState("");
  const [couponSuccessMsg, setCouponSuccessMsg] = useState("");

  const [loading, setLoading] = useState(false);
  const [errorMsg, setErrorMsg] = useState("");
  const [orderSuccess, setOrderSuccess] = useState<{
    id: string;
    customerName: string;
    couponCode?: string | null;
    discountAmount?: number;
    totalAmount: number;
    paymentMethod: string;
  } | null>(null);

  // Load active coupons from Database & check if user saved a Flash Sale code
  useEffect(() => {
    fetch("/api/coupons")
      .then((res) => res.json())
      .then((data) => {
        if (Array.isArray(data)) {
          const now = new Date();
          const activeValid = data.filter(
            (c: CouponItem) => c.isActive && new Date(c.expiresAt) > now
          );
          setAvailableCoupons(activeValid);
        }
      })
      .catch((err) => console.error("Failed to fetch coupons:", err));

    try {
      const savedCode = localStorage.getItem("lumina_saved_voucher");
      if (savedCode) {
        setCouponInput(savedCode);
      }
    } catch {
      // ignore storage errors
    }
  }, []);

  // Re-calculate discount dynamically when cart totalAmount or appliedCoupon changes
  const calculatedDiscount = React.useMemo(() => {
    if (!appliedCoupon) return 0;
    if (totalAmount < appliedCoupon.minOrderAmount) return 0;
    let disc = Math.round((totalAmount * appliedCoupon.discountPercent) / 100);
    if (appliedCoupon.maxDiscount && disc > appliedCoupon.maxDiscount) {
      disc = appliedCoupon.maxDiscount;
    }
    return disc;
  }, [appliedCoupon, totalAmount]);

  const finalTotalAmount = Math.max(0, totalAmount - calculatedDiscount);

  const handleApplyCoupon = async (codeToApply?: string) => {
    const targetCode = (codeToApply ?? couponInput).trim().toUpperCase();
    if (!targetCode) {
      setCouponError("Vui lòng nhập mã giảm giá.");
      return;
    }

    setCouponLoading(true);
    setCouponError("");
    setCouponSuccessMsg("");

    try {
      const res = await fetch("/api/coupons", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          action: "validate",
          code: targetCode,
          subtotal: totalAmount,
        }),
      });

      const data = await res.json();
      if (!res.ok || !data.valid) {
        setAppliedCoupon(null);
        setCouponError(data.error || "Mã giảm giá không hợp lệ.");
      } else {
        setCouponInput(data.coupon.code);
        setAppliedCoupon(data.coupon);
        setCouponSuccessMsg(
          `Áp dụng mã ${data.coupon.code} (-${
            data.coupon.discountPercent
          }%) thành công! Bạn tiết kiệm được ${formatVND(data.discountAmount)}.`
        );
      }
    } catch {
      setCouponError("Không thể kiểm tra mã giảm giá. Vui lòng thử lại.");
    } finally {
      setCouponLoading(false);
    }
  };

  const handleRemoveCoupon = () => {
    setAppliedCoupon(null);
    setCouponInput("");
    setCouponError("");
    setCouponSuccessMsg("");
    try {
      localStorage.removeItem("lumina_saved_voucher");
    } catch {
      // ignore
    }
  };

  const handlePlaceOrder = async (e: React.FormEvent) => {
    e.preventDefault();
    if (items.length === 0) return;

    setLoading(true);
    setErrorMsg("");

    try {
      const response = await fetch("/api/orders", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          customerName,
          phone,
          address,
          note,
          paymentMethod,
          couponCode:
            appliedCoupon && calculatedDiscount > 0 ? appliedCoupon.code : null,
          items: items.map((i) => ({
            productId: i.id,
            quantity: i.quantity,
            price: i.price,
          })),
        }),
      });

      const data = await response.json();
      if (!response.ok) {
        setErrorMsg(data.error || "Có lỗi xảy ra khi đặt hàng.");
      } else {
        setOrderSuccess(data);
        clearCart();
        try {
          localStorage.removeItem("lumina_saved_voucher");
        } catch {
          // ignore
        }
      }
    } catch {
      setErrorMsg("Không thể kết nối máy chủ. Vui lòng thử lại.");
    } finally {
      setLoading(false);
    }
  };

  if (orderSuccess) {
    return (
      <div className="max-w-2xl mx-auto px-4 py-16 text-center">
        <div className="bg-white p-10 rounded-3xl border border-rose-100 shadow-lg space-y-6">
          <div className="w-20 h-20 mx-auto rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <CheckCircle2 className="w-10 h-10" />
          </div>
          <span className="text-xs font-bold uppercase tracking-widest text-rose-500">
            Đặt Hàng Thành Công
          </span>
          <h1 className="font-serif text-3xl font-bold text-stone-800">
            Cảm Ơn {orderSuccess.customerName} Đã Mua Sắm Tại Lumina Beauty!
          </h1>
          <p className="text-sm text-stone-600 leading-relaxed">
            Đơn hàng của bạn đã được lưu trực tiếp vào hệ thống cơ sở dữ liệu
            PostgreSQL. Chuyên viên tư vấn sẽ liên hệ xác nhận trong vòng 15
            phút.
          </p>
          <div className="bg-[#FDFBF7] p-5 rounded-2xl border border-rose-100 text-left text-sm space-y-2">
            <div className="flex justify-between">
              <span className="text-stone-500">Mã đơn hàng:</span>
              <span className="font-mono font-bold text-stone-800">
                #{orderSuccess.id.slice(-8).toUpperCase()}
              </span>
            </div>
            <div className="flex justify-between">
              <span className="text-stone-500">Phương thức thanh toán:</span>
              <span className="font-semibold text-stone-800">
                {orderSuccess.paymentMethod === "COD"
                  ? "Thanh toán khi nhận hàng (COD)"
                  : "Chuyển khoản ngân hàng"}
              </span>
            </div>
            {orderSuccess.couponCode && (
              <div className="flex justify-between text-emerald-700">
                <span>Mã giảm giá đã áp dụng ({orderSuccess.couponCode}):</span>
                <span className="font-bold">
                  -{formatVND(orderSuccess.discountAmount || 0)}
                </span>
              </div>
            )}
            <div className="flex justify-between border-t border-rose-100 pt-2">
              <span className="text-stone-500">Tổng thanh toán:</span>
              <span className="font-bold text-rose-600 text-base">
                {formatVND(orderSuccess.totalAmount)}
              </span>
            </div>
          </div>

          <div className="flex flex-wrap justify-center gap-4 pt-2">
            <Link
              href="/products"
              className="bg-rose-500 hover:bg-rose-600 text-white font-semibold px-7 py-3 rounded-full text-sm transition"
            >
              Tiếp Tục Mua Sắm
            </Link>
            <Link
              href="/admin"
              className="bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold px-7 py-3 rounded-full text-sm transition border border-rose-200"
            >
              Xem Đơn Hàng Trong Quản Trị (/admin)
            </Link>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
      <div className="flex items-center justify-between">
        <div>
          <Link
            href="/products"
            className="inline-flex items-center gap-1.5 text-xs font-medium text-rose-600 hover:text-rose-700 mb-2"
          >
            <ArrowLeft className="w-3.5 h-3.5" /> Tiếp tục chọn mỹ phẩm
          </Link>
          <h1 className="font-serif text-3xl font-bold text-stone-800">
            Thanh Toán & Giao Hàng
          </h1>
        </div>
      </div>

      {items.length === 0 ? (
        <div className="bg-white rounded-3xl border border-rose-100 p-12 text-center space-y-4">
          <p className="font-serif text-xl text-stone-700">
            Giỏ hàng của bạn hiện chưa có sản phẩm nào
          </p>
          <Link
            href="/products"
            className="inline-block bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold px-6 py-3 rounded-full transition"
          >
            Khám phá sản phẩm ngay
          </Link>
        </div>
      ) : (
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
          {/* Shipping Form */}
          <form
            onSubmit={handlePlaceOrder}
            className="lg:col-span-7 bg-white p-6 sm:p-8 rounded-3xl border border-rose-100 shadow-xs space-y-6"
          >
            <h2 className="font-serif text-xl font-bold text-stone-800 pb-3 border-b border-rose-100">
              1. Thông Tin Người Nhận
            </h2>

            {errorMsg && (
              <div className="p-4 rounded-2xl bg-red-50 text-red-600 text-sm border border-red-200">
                {errorMsg}
              </div>
            )}

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-2">
                  Họ và tên người nhận *
                </label>
                <input
                  type="text"
                  required
                  value={customerName}
                  onChange={(e) => setCustomerName(e.target.value)}
                  placeholder="VD: Nguyễn Thị Minh Châu"
                  className="w-full px-4 py-3 text-sm rounded-2xl border border-rose-200 bg-[#FDFBF7] focus:outline-none focus:ring-2 focus:ring-rose-300"
                />
              </div>

              <div>
                <label className="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-2">
                  Số điện thoại liên hệ *
                </label>
                <input
                  type="tel"
                  required
                  value={phone}
                  onChange={(e) => setPhone(e.target.value)}
                  placeholder="VD: 0909 123 456"
                  className="w-full px-4 py-3 text-sm rounded-2xl border border-rose-200 bg-[#FDFBF7] focus:outline-none focus:ring-2 focus:ring-rose-300"
                />
              </div>
            </div>

            <div>
              <label className="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-2">
                Địa chỉ giao hàng chi tiết *
              </label>
              <input
                type="text"
                required
                value={address}
                onChange={(e) => setAddress(e.target.value)}
                placeholder="Số nhà, tên đường, Phường/Xã, Quận/Huyện, Tỉnh/Thành phố"
                className="w-full px-4 py-3 text-sm rounded-2xl border border-rose-200 bg-[#FDFBF7] focus:outline-none focus:ring-2 focus:ring-rose-300"
              />
            </div>

            <div>
              <label className="block text-xs font-semibold uppercase tracking-wider text-stone-600 mb-2">
                Ghi chú đơn hàng (Tùy chọn)
              </label>
              <textarea
                rows={3}
                value={note}
                onChange={(e) => setNote(e.target.value)}
                placeholder="Ghi chú giao giờ hành chính hoặc gói quà tặng..."
                className="w-full px-4 py-3 text-sm rounded-2xl border border-rose-200 bg-[#FDFBF7] focus:outline-none focus:ring-2 focus:ring-rose-300"
              />
            </div>

            <h2 className="font-serif text-xl font-bold text-stone-800 pt-2 pb-3 border-b border-rose-100">
              2. Phương Thức Thanh Toán
            </h2>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <button
                type="button"
                onClick={() => setPaymentMethod("COD")}
                className={`p-4 rounded-2xl border text-left flex items-start gap-3 transition ${
                  paymentMethod === "COD"
                    ? "border-rose-500 bg-rose-50/70"
                    : "border-rose-100 bg-[#FDFBF7]"
                }`}
              >
                <Truck className="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" />
                <div>
                  <p className="text-sm font-bold text-stone-800">
                    Thanh toán khi nhận hàng (COD)
                  </p>
                  <p className="text-xs text-stone-500 mt-0.5">
                    Kiểm tra tem niêm phong mỹ phẩm trước khi thanh toán
                  </p>
                </div>
              </button>

              <button
                type="button"
                onClick={() => setPaymentMethod("BANK_TRANSFER")}
                className={`p-4 rounded-2xl border text-left flex items-start gap-3 transition ${
                  paymentMethod === "BANK_TRANSFER"
                    ? "border-rose-500 bg-rose-50/70"
                    : "border-rose-100 bg-[#FDFBF7]"
                }`}
              >
                <CreditCard className="w-5 h-5 text-rose-500 mt-0.5 flex-shrink-0" />
                <div>
                  <p className="text-sm font-bold text-stone-800">
                    Chuyển khoản Ngân hàng
                  </p>
                  <p className="text-xs text-stone-500 mt-0.5">
                    Giảm thêm 2% hoặc tặng kèm mặt nạ dưỡng ẩm
                  </p>
                </div>
              </button>
            </div>

            <button
              type="submit"
              disabled={loading}
              className="w-full bg-rose-500 hover:bg-rose-600 disabled:bg-rose-300 text-white font-semibold py-4 px-8 rounded-full shadow-lg shadow-rose-200 transition text-base"
            >
              {loading
                ? "Đang xử lý đơn hàng..."
                : `Hoàn Tất Đặt Hàng (${formatVND(finalTotalAmount)})`}
            </button>
          </form>

          {/* Order Summary & Voucher Section */}
          <div className="lg:col-span-5 space-y-6">
            <div className="bg-white p-6 sm:p-8 rounded-3xl border border-rose-100 shadow-xs space-y-6">
              <h2 className="font-serif text-xl font-bold text-stone-800 pb-3 border-b border-rose-100">
                Chi Tiết Giỏ Hàng ({items.length})
              </h2>

              <div className="space-y-4 max-h-80 overflow-y-auto pr-1">
                {items.map((item) => (
                  <div
                    key={item.id}
                    className="flex gap-3.5 items-center pb-4 border-b border-rose-50"
                  >
                    <img
                      src={item.image}
                      alt={item.name}
                      className="w-16 h-16 rounded-2xl object-cover bg-rose-50 flex-shrink-0"
                    />
                    <div className="flex-1 min-w-0">
                      <p className="text-sm font-semibold text-stone-800 line-clamp-1">
                        {item.name}
                      </p>
                      <p className="text-xs text-stone-400">
                        {item.volume} • {item.skinType}
                      </p>
                      <p className="text-sm font-bold text-rose-600 mt-0.5">
                        {formatVND(item.price * item.quantity)}
                      </p>
                    </div>
                    <div className="flex items-center gap-1">
                      <button
                        type="button"
                        onClick={() =>
                          updateQuantity(item.id, item.quantity - 1)
                        }
                        className="p-1 rounded-full bg-rose-50 text-stone-600 hover:text-rose-600"
                      >
                        <Minus className="w-3.5 h-3.5" />
                      </button>
                      <span className="w-6 text-center text-xs font-bold">
                        {item.quantity}
                      </span>
                      <button
                        type="button"
                        onClick={() =>
                          updateQuantity(item.id, item.quantity + 1)
                        }
                        className="p-1 rounded-full bg-rose-50 text-stone-600 hover:text-rose-600"
                      >
                        <Plus className="w-3.5 h-3.5" />
                      </button>
                      <button
                        type="button"
                        onClick={() => removeFromCart(item.id)}
                        className="p-1 text-stone-400 hover:text-red-500 ml-1"
                      >
                        <Trash2 className="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                ))}
              </div>

              {/* VOUCHER / COUPON INPUT BOX */}
              <div className="bg-gradient-to-br from-rose-50/80 to-[#FDFBF7] p-4 rounded-2xl border border-rose-200/80 space-y-3">
                <div className="flex items-center justify-between">
                  <label className="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-rose-600">
                    <TicketPercent className="w-4 h-4" />
                    <span>Mã Giảm Giá / Voucher Flash Sale</span>
                  </label>
                  {appliedCoupon && (
                    <button
                      type="button"
                      onClick={handleRemoveCoupon}
                      className="text-xs text-stone-500 hover:text-red-600 inline-flex items-center gap-1 font-medium"
                    >
                      <X className="w-3.5 h-3.5" /> Gỡ mã
                    </button>
                  )}
                </div>

                <div className="flex gap-2">
                  <input
                    type="text"
                    value={couponInput}
                    onChange={(e) =>
                      setCouponInput(e.target.value.toUpperCase())
                    }
                    placeholder="Nhập mã (VD: LUMINA20, FLASHSALE30)"
                    data-testid="coupon-input"
                    className="flex-1 px-3.5 py-2.5 text-sm font-mono font-bold uppercase rounded-xl border border-rose-200 bg-white focus:outline-none focus:ring-2 focus:ring-rose-300"
                  />
                  <button
                    type="button"
                    onClick={() => handleApplyCoupon()}
                    disabled={couponLoading}
                    data-testid="apply-coupon-btn"
                    className="bg-rose-500 hover:bg-rose-600 disabled:bg-rose-300 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition flex-shrink-0 shadow-xs"
                  >
                    {couponLoading ? "Đang kiểm tra..." : "Áp dụng"}
                  </button>
                </div>

                {couponError && (
                  <p className="text-xs text-red-600 font-medium bg-red-50 px-3 py-2 rounded-xl border border-red-200">
                    {couponError}
                  </p>
                )}

                {couponSuccessMsg && appliedCoupon && (
                  <div className="text-xs text-emerald-700 font-medium bg-emerald-50 px-3 py-2 rounded-xl border border-emerald-200 flex items-center gap-1.5">
                    <CheckCircle2 className="w-4 h-4 flex-shrink-0 text-emerald-600" />
                    <span>{couponSuccessMsg}</span>
                  </div>
                )}

                {/* Quick Pick Available Vouchers from DB */}
                {availableCoupons.length > 0 && (
                  <div className="pt-1 space-y-1.5">
                    <p className="text-[11px] font-semibold text-stone-500 flex items-center gap-1">
                      <Flame className="w-3.5 h-3.5 text-rose-500" /> Chọn nhanh
                      Voucher đang có sẵn:
                    </p>
                    <div className="flex flex-wrap gap-1.5">
                      {availableCoupons.map((c) => {
                        const isSelected = appliedCoupon?.code === c.code;
                        return (
                          <button
                            key={c.id}
                            type="button"
                            onClick={() => {
                              setCouponInput(c.code);
                              handleApplyCoupon(c.code);
                            }}
                            className={`text-left px-2.5 py-1.5 rounded-xl border text-[11px] transition flex items-center gap-1.5 ${
                              isSelected
                                ? "bg-rose-500 text-white border-rose-500 font-bold"
                                : "bg-white hover:bg-rose-50 text-stone-700 border-rose-200"
                            }`}
                            title={c.description || ""}
                          >
                            <span className="font-mono font-bold">
                              {c.code}
                            </span>
                            <span
                              className={`px-1.5 py-0.2 rounded-full text-[10px] font-bold ${
                                isSelected
                                  ? "bg-white/20 text-white"
                                  : "bg-rose-100 text-rose-600"
                              }`}
                            >
                              -{c.discountPercent}%
                            </span>
                          </button>
                        );
                      })}
                    </div>
                  </div>
                )}
              </div>

              {/* Price Summary Breakdown */}
              <div className="space-y-2.5 text-sm pt-2">
                <div className="flex justify-between text-stone-500">
                  <span>Tạm tính</span>
                  <span>{formatVND(totalAmount)}</span>
                </div>

                {appliedCoupon && calculatedDiscount > 0 && (
                  <div className="flex justify-between text-emerald-600 font-semibold">
                    <span className="flex items-center gap-1">
                      <Sparkles className="w-3.5 h-3.5" />
                      Voucher ({appliedCoupon.code} -{appliedCoupon.discountPercent}
                      %)
                    </span>
                    <span>-{formatVND(calculatedDiscount)}</span>
                  </div>
                )}

                <div className="flex justify-between text-stone-500">
                  <span>Phí vận chuyển</span>
                  <span className="text-emerald-600 font-semibold">
                    Miễn phí
                  </span>
                </div>

                <div className="flex justify-between text-lg font-bold text-stone-800 pt-3 border-t border-rose-100">
                  <span>Tổng thanh toán</span>
                  <span className="text-rose-600">
                    {formatVND(finalTotalAmount)}
                  </span>
                </div>
              </div>

              <div className="bg-[#FDFBF7] p-4 rounded-2xl border border-rose-100 flex items-center gap-3 text-xs text-stone-600">
                <ShieldCheck className="w-5 h-5 text-rose-500 flex-shrink-0" />
                <span>
                  Tặng kèm 02 gói Sample Serum dưỡng ẩm miễn phí cho mọi đơn hàng đặt trực tuyến hôm nay.
                </span>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
