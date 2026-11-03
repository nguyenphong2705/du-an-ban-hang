"use client";

import React, { useState, useEffect } from "react";
import ProductCard, { ProductCardProps } from "./ProductCard";
import {
  Droplets,
  Feather,
  ShieldAlert,
  Sparkles,
  WandSparkles,
  CheckCircle2,
} from "lucide-react";
import { openSkinQuiz } from "./SkinQuizModal";

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
  const [aiDiagnosedSkin, setAiDiagnosedSkin] = useState<string | null>(null);

  useEffect(() => {
    const handleQuizCompleted = (e: Event) => {
      const customEvent = e as CustomEvent<{ skinType: string }>;
      const diagnosed = customEvent.detail?.skinType;
      if (diagnosed) {
        setAiDiagnosedSkin(diagnosed);
        if (SKIN_TABS.some((t) => t.key === diagnosed)) {
          setSelectedSkin(diagnosed);
        } else {
          setSelectedSkin("Tất cả");
        }
      }
    };
    window.addEventListener("skin-quiz-completed", handleQuizCompleted);
    return () =>
      window.removeEventListener("skin-quiz-completed", handleQuizCompleted);
  }, []);

  const filtered =
    selectedSkin === "Tất cả"
      ? products.slice(0, 8)
      : [...products]
          .filter(
            (p) =>
              p.skinType.toLowerCase() === selectedSkin.toLowerCase() ||
              p.skinType === "Mọi loại da"
          )
          .sort((a, b) => {
            const aExact =
              a.skinType.toLowerCase() === selectedSkin.toLowerCase() ? 1 : 0;
            const bExact =
              b.skinType.toLowerCase() === selectedSkin.toLowerCase() ? 1 : 0;
            return bExact - aExact;
          });

  return (
    <div className="space-y-8">
      {/* AI Quiz Callout Banner */}
      <div className="bg-gradient-to-r from-rose-50 via-white to-rose-50 rounded-3xl border border-rose-200 p-6 sm:p-7 flex flex-col md:flex-row items-center justify-between gap-6 shadow-2xs">
        <div className="space-y-1.5 text-center md:text-left">
          <div className="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-rose-600 bg-rose-100 px-3 py-1 rounded-full">
            <WandSparkles className="w-3.5 h-3.5" />
            <span>AI Tư Vấn Chọn Mỹ Phẩm Theo Da</span>
          </div>
          <h3 className="font-serif text-xl sm:text-2xl font-bold text-stone-800">
            Bạn chưa chắc mình thuộc loại da nào?
          </h3>
          <p className="text-xs sm:text-sm text-stone-600 max-w-xl">
            Trả lời 4 câu hỏi nhanh trong 30 giây về tình trạng da hiện tại để AI
            chẩn đoán và tự động lọc bộ sản phẩm phù hợp nhất từ Database.
          </p>
          {aiDiagnosedSkin && (
            <p className="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full mt-2">
              <CheckCircle2 className="w-3.5 h-3.5" />
              Đã tự động lọc theo kết quả AI Trắc nghiệm:{" "}
              <strong>{aiDiagnosedSkin}</strong>
            </p>
          )}
        </div>

        <button
          type="button"
          onClick={openSkinQuiz}
          data-testid="explorer-skin-quiz-btn"
          className="inline-flex items-center gap-2.5 bg-rose-500 hover:bg-rose-600 text-white font-semibold text-sm px-7 py-4 rounded-full shadow-lg shadow-rose-200 transition transform hover:-translate-y-0.5 flex-shrink-0"
        >
          <Sparkles className="w-4 h-4" />
          <span>Trắc nghiệm loại da trong 30 giây</span>
        </button>
      </div>

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
