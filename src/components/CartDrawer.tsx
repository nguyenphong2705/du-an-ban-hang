"use client";

import React from "react";
import Link from "next/link";
import { X, Plus, Minus, Trash2, ShoppingBag, ArrowRight } from "lucide-react";
import { useCart } from "@/context/CartContext";
import { formatVND } from "@/lib/prisma";

export default function CartDrawer() {
  const {
    items,
    isCartOpen,
    setIsCartOpen,
    updateQuantity,
    removeFromCart,
    totalAmount,
    totalItems,
  } = useCart();

  if (!isCartOpen) return null;

  return (
    <div className="fixed inset-0 z-50 overflow-hidden">
      {/* Backdrop */}
      <div
        onClick={() => setIsCartOpen(false)}
        className="fixed inset-0 bg-stone-900/40 backdrop-blur-xs transition-opacity"
      />

      <div className="fixed inset-y-0 right-0 max-w-full flex pl-10">
        <div className="w-screen max-w-md bg-[#FDFBF7] shadow-2xl flex flex-col justify-between">
          {/* Header */}
          <div className="p-6 bg-white border-b border-rose-100 flex items-center justify-between">
            <div className="flex items-center gap-2.5">
              <div className="w-9 h-9 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center">
                <ShoppingBag className="w-5 h-5" />
              </div>
              <div>
                <h2 className="font-serif text-lg font-bold text-stone-800">
                  Giỏ Hàng Của Bạn
                </h2>
                <p className="text-xs text-stone-500">
                  Hiện có {totalItems} sản phẩm làm đẹp
                </p>
              </div>
            </div>
            <button
              onClick={() => setIsCartOpen(false)}
              className="p-2 rounded-full text-stone-400 hover:text-stone-700 hover:bg-rose-50 transition"
            >
              <X className="w-5 h-5" />
            </button>
          </div>

          {/* Cart Items */}
          <div className="flex-1 overflow-y-auto p-6 space-y-4">
            {items.length === 0 ? (
              <div className="text-center py-16 space-y-4">
                <div className="w-16 h-16 mx-auto rounded-full bg-rose-50 text-rose-300 flex items-center justify-center">
                  <ShoppingBag className="w-8 h-8" />
                </div>
                <p className="text-stone-600 font-medium">
                  Giỏ hàng của bạn đang trống
                </p>
                <p className="text-xs text-stone-400 max-w-xs mx-auto">
                  Hãy khám phá các dòng tinh chất dưỡng da, kem dưỡng và nước hoa cao cấp tại Lumina Beauty.
                </p>
                <Link
                  href="/products"
                  onClick={() => setIsCartOpen(false)}
                  className="inline-flex items-center gap-2 bg-rose-500 hover:bg-rose-600 text-white text-sm font-medium px-6 py-2.5 rounded-full transition shadow-sm"
                >
                  Khám phá ngay
                  <ArrowRight className="w-4 h-4" />
                </Link>
              </div>
            ) : (
              items.map((item) => (
                <div
                  key={item.id}
                  className="flex gap-4 bg-white p-4 rounded-2xl border border-rose-100/80 shadow-xs"
                >
                  <img
                    src={item.image}
                    alt={item.name}
                    className="w-20 h-20 rounded-xl object-cover bg-rose-50 flex-shrink-0"
                  />
                  <div className="flex-1 min-w-0">
                    <Link
                      href={`/products/${item.slug}`}
                      onClick={() => setIsCartOpen(false)}
                      className="text-sm font-semibold text-stone-800 hover:text-rose-600 line-clamp-2"
                    >
                      {item.name}
                    </Link>
                    <p className="text-xs text-stone-400 mt-0.5">
                      {item.volume} • {item.skinType}
                    </p>
                    <p className="text-sm font-bold text-rose-600 mt-1">
                      {formatVND(item.price)}
                    </p>

                    <div className="flex items-center justify-between mt-3">
                      <div className="inline-flex items-center border border-rose-200 rounded-full bg-rose-50/40">
                        <button
                          onClick={() =>
                            updateQuantity(item.id, item.quantity - 1)
                          }
                          className="p-1.5 text-stone-600 hover:text-rose-600"
                          aria-label="Giảm số lượng"
                        >
                          <Minus className="w-3.5 h-3.5" />
                        </button>
                        <span className="px-3 text-xs font-semibold text-stone-800">
                          {item.quantity}
                        </span>
                        <button
                          onClick={() =>
                            updateQuantity(item.id, item.quantity + 1)
                          }
                          className="p-1.5 text-stone-600 hover:text-rose-600"
                          aria-label="Tăng số lượng"
                        >
                          <Plus className="w-3.5 h-3.5" />
                        </button>
                      </div>

                      <button
                        onClick={() => removeFromCart(item.id)}
                        className="p-1.5 text-stone-400 hover:text-red-500 transition"
                        title="Xóa sản phẩm"
                      >
                        <Trash2 className="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                </div>
              ))
            )}
          </div>

          {/* Footer */}
          {items.length > 0 && (
            <div className="p-6 bg-white border-t border-rose-100 space-y-4">
              <div className="space-y-1.5 text-sm">
                <div className="flex justify-between text-stone-500">
                  <span>Tạm tính ({totalItems} sản phẩm)</span>
                  <span>{formatVND(totalAmount)}</span>
                </div>
                <div className="flex justify-between text-stone-500">
                  <span>Phí vận chuyển</span>
                  <span className="text-emerald-600 font-medium">Miễn phí</span>
                </div>
                <div className="flex justify-between text-base font-bold text-stone-800 pt-2 border-t border-rose-100">
                  <span>Tổng cộng</span>
                  <span className="text-rose-600 text-lg">
                    {formatVND(totalAmount)}
                  </span>
                </div>
              </div>

              <Link
                href="/checkout"
                onClick={() => setIsCartOpen(false)}
                className="w-full flex items-center justify-center gap-2 bg-rose-500 hover:bg-rose-600 text-white font-semibold py-3.5 px-6 rounded-full shadow-md shadow-rose-200 transition"
              >
                <span>Tiến Hành Thanh Toán</span>
                <ArrowRight className="w-4 h-4" />
              </Link>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
