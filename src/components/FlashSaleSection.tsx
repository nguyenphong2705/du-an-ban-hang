"use client";

import React, { useState, useEffect } from "react";
import Link from "next/link";
import {
  Flame,
  TicketPercent,
  Check,
  Copy,
  Clock,
  ArrowRight,
} from "lucide-react";
import { formatVND } from "@/lib/prisma";

export interface CouponCardProps {
  id: string;
  code: string;
  discountPercent: number;
  description?: string | null;
  minOrderAmount: number;
  maxDiscount?: number | null;
  expiresAt: Date | string;
}

export default function FlashSaleSection({
  coupons,
}: {
  coupons: CouponCardProps[];
}) {
  const [copiedCode, setCopiedCode] = useState<string | null>(null);
  const [timeLeft, setTimeLeft] = useState({
    hours: 5,
    minutes: 42,
    seconds: 18,
  });

  useEffect(() => {
    const timer = setInterval(() => {
      setTimeLeft((prev) => {
        if (prev.seconds > 0) {
          return { ...prev, seconds: prev.seconds - 1 };
        }
        if (prev.minutes > 0) {
          return { ...prev, minutes: prev.minutes - 1, seconds: 59 };
        }
        if (prev.hours > 0) {
          return { hours: prev.hours - 1, minutes: 59, seconds: 59 };
        }
        return { hours: 5, minutes: 59, seconds: 59 };
      });
    }, 1000);
    return () => clearInterval(timer);
  }, []);

  const handleCopyCoupon = (code: string) => {
    try {
      navigator.clipboard?.writeText(code);
      localStorage.setItem("lumina_saved_voucher", code);
    } catch {
      // ignore
    }
    setCopiedCode(code);
    setTimeout(() => setCopiedCode(null), 3000);
  };

  if (!coupons || coupons.length === 0) return null;

  return (
    <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div className="relative overflow-hidden rounded-3xl bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 p-6 sm:p-10 text-white shadow-xl shadow-rose-200/70">
        {/* Header & Countdown */}
        <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-8 border-b border-white/20">
          <div className="space-y-2">
            <div className="inline-flex items-center gap-2 bg-white/20 backdrop-blur-xs px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
              <Flame className="w-4 h-4 text-amber-200 fill-amber-200" />
              <span>Flash Sale & Trạm Săn Voucher Độc Quyền</span>
            </div>
            <h2 className="font-serif text-3xl sm:text-4xl font-bold">
              Săn Mã Giảm Giá Mỹ Phẩm Lên Tới 30%
            </h2>
            <p className="text-rose-50 text-sm sm:text-base max-w-2xl">
              Lưu mã giảm giá ngay dưới đây và áp dụng trực tiếp tại trang{" "}
              <Link
                href="/checkout"
                className="underline font-bold text-white hover:text-amber-100"
              >
                /checkout
              </Link>{" "}
              để nhận ưu đãi tức thì!
            </p>
          </div>

          {/* Countdown Box */}
          <div className="flex items-center gap-3 bg-stone-950/25 backdrop-blur-md px-5 py-4 rounded-2xl border border-white/20 self-start lg:self-center">
            <div className="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-amber-100 mr-1">
              <Clock className="w-4 h-4" />
              <span>Kết thúc sau:</span>
            </div>
            <div className="flex items-center gap-2 font-mono font-bold text-lg">
              <span className="bg-white text-rose-600 px-2.5 py-1 rounded-xl min-w-[2.5rem] text-center">
                {String(timeLeft.hours).padStart(2, "0")}
              </span>
              <span>:</span>
              <span className="bg-white text-rose-600 px-2.5 py-1 rounded-xl min-w-[2.5rem] text-center">
                {String(timeLeft.minutes).padStart(2, "0")}
              </span>
              <span>:</span>
              <span className="bg-white text-rose-600 px-2.5 py-1 rounded-xl min-w-[2.5rem] text-center">
                {String(timeLeft.seconds).padStart(2, "0")}
              </span>
            </div>
          </div>
        </div>

        {/* Voucher Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-7">
          {coupons.map((coupon) => {
            const isCopied = copiedCode === coupon.code;
            return (
              <div
                key={coupon.id}
                className="bg-white text-stone-800 rounded-2xl p-5 flex flex-col justify-between shadow-md border border-rose-100 relative overflow-hidden"
              >
                <div className="space-y-2">
                  <div className="flex items-center justify-between gap-2">
                    <span className="inline-flex items-center gap-1 bg-rose-100 text-rose-700 font-mono font-bold text-xs px-2.5 py-1 rounded-lg">
                      <TicketPercent className="w-3.5 h-3.5" />
                      {coupon.code}
                    </span>
                    <span className="bg-gradient-to-r from-rose-500 to-amber-500 text-white text-xs font-extrabold px-2.5 py-1 rounded-full">
                      Giảm {coupon.discountPercent}%
                    </span>
                  </div>

                  <p className="text-xs font-semibold text-stone-700 line-clamp-2 min-h-[2rem]">
                    {coupon.description ||
                      `Giảm ${coupon.discountPercent}% cho đơn hàng mỹ phẩm`}
                  </p>

                  <div className="text-[11px] text-stone-400 space-y-0.5 pt-1 border-t border-rose-50">
                    <p>
                      • Đơn tối thiểu:{" "}
                      <strong className="text-stone-600">
                        {coupon.minOrderAmount > 0
                          ? formatVND(coupon.minOrderAmount)
                          : "0₫ (Mọi đơn)"}
                      </strong>
                    </p>
                    <p>
                      • HSD:{" "}
                      {new Date(coupon.expiresAt).toLocaleDateString("vi-VN")}
                    </p>
                  </div>
                </div>

                <div className="pt-3 mt-3 border-t border-dashed border-rose-200 flex items-center justify-between gap-2">
                  <button
                    type="button"
                    onClick={() => handleCopyCoupon(coupon.code)}
                    className={`w-full py-2 px-3 rounded-xl font-bold text-xs inline-flex items-center justify-center gap-1.5 transition ${
                      isCopied
                        ? "bg-emerald-600 text-white"
                        : "bg-rose-50 hover:bg-rose-500 text-rose-600 hover:text-white border border-rose-200"
                    }`}
                  >
                    {isCopied ? (
                      <>
                        <Check className="w-3.5 h-3.5" />
                        <span>Đã lưu mã {coupon.code}</span>
                      </>
                    ) : (
                      <>
                        <Copy className="w-3.5 h-3.5" />
                        <span>Lưu mã giảm giá</span>
                      </>
                    )}
                  </button>
                </div>
              </div>
            );
          })}
        </div>

        <div className="pt-6 flex justify-end">
          <Link
            href="/checkout"
            className="inline-flex items-center gap-1.5 text-xs font-bold text-white hover:text-amber-100"
          >
            <span>Đến trang Thanh Toán (/checkout) để dùng mã ngay</span>
            <ArrowRight className="w-4 h-4" />
          </Link>
        </div>
      </div>
    </section>
  );
}
