"use client";

import React, { useState } from "react";
import ProductCard, { ProductCardProps } from "./ProductCard";
import { Droplets, Feather, ShieldAlert, Sparkles } from "lucide-react";

const SKIN_TABS = [
  {
    key: "Tất cả",
    label: "Tất cả loại da",
    desc: "Bộ sưu tập chăm sóc toàn diện",
    icon: Sparkles,
  },
  {
    key: "Da dầu",
    label: "Da dầu & Hỗn hợp",
    desc: "Kiểm soát bã nhờn, thu nhỏ lỗ chân lông (Niacinamide, BHA)",
    icon: Droplets,
  },
  {
    key: "Da khô",
    label: "Da khô & Thiếu ẩm",
    desc: "Cấp ẩm đa tầng, khóa ẩm chuyên sâu (Hyaluronic Acid, Nhân sâm)",
    icon: Feather,
  },
  {
    key: "Da nhạy cảm",
    label: "Da nhạy cảm",
    desc: "Làm dịu kích ứng, phục hồi màng ẩm (Vitamin B5, Rau má)",
    icon: ShieldAlert,
  },
];

export default function SkinTypeExplorer({
  products,
}: {
  products: ProductCardProps[];
}) {
  const [selectedSkin, setSelectedSkin] = useState<string>("Tất cả");

  const filtered =
    selectedSkin === "Tất cả"
      ? products.slice(0, 8)
      : products.filter(
          (p) =>
            p.skinType.toLowerCase() === selectedSkin.toLowerCase() ||
            p.skinType === "Mọi loại da"
        );

  return (
    <div className="space-y-8">
      {/* Tabs */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {SKIN_TABS.map((tab) => {
          const Icon = tab.icon;
          const active = selectedSkin === tab.key;
          return (
            <button
              key={tab.key}
              onClick={() => setSelectedSkin(tab.key)}
              className={`text-left p-5 rounded-3xl border transition-all duration-200 ${
                active
                  ? "bg-rose-500 text-white border-rose-500 shadow-lg shadow-rose-200"
                  : "bg-white hover:bg-rose-50/60 text-stone-800 border-rose-100"
              }`}
            >
              <div className="flex items-center gap-3 mb-2">
                <div
                  className={`w-9 h-9 rounded-2xl flex items-center justify-center ${
                    active
                      ? "bg-white/20 text-white"
                      : "bg-rose-100 text-rose-600"
                  }`}
                >
                  <Icon className="w-5 h-5" />
                </div>
                <span className="font-serif font-bold text-base">
                  {tab.label}
                </span>
              </div>
              <p
                className={`text-xs leading-relaxed ${
                  active ? "text-rose-50" : "text-stone-500"
                }`}
              >
                {tab.desc}
              </p>
            </button>
          );
        })}
      </div>

      {/* Filtered Products Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {filtered.map((product) => (
          <ProductCard key={product.id} product={product} />
        ))}
      </div>
    </div>
  );
}
