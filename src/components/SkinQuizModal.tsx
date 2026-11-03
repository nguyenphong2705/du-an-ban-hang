"use client";

import React, { useState, useEffect } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import {
  Sparkles,
  X,
  ArrowRight,
  ArrowLeft,
  CheckCircle2,
  RotateCcw,
  ShoppingBag,
  Clock,
  Droplets,
  Feather,
  ShieldAlert,
  Sun,
  Moon,
  FlaskConical,
  Filter,
  WandSparkles,
} from "lucide-react";
import { useCart } from "@/context/CartContext";
import { formatVND } from "@/lib/prisma";

export function openSkinQuiz() {
  if (typeof window !== "undefined") {
    window.dispatchEvent(new CustomEvent("open-skin-quiz"));
  }
}

export function SkinQuizTriggerButton({
  label = "Trắc nghiệm loại da trong 30 giây",
  className,
}: {
  label?: string;
  className?: string;
}) {
  return (
    <button
      type="button"
      onClick={openSkinQuiz}
      data-testid="hero-skin-quiz-btn"
      className={
        className ||
        "inline-flex items-center gap-2 bg-white hover:bg-rose-50 text-rose-600 font-semibold px-7 py-4 rounded-full border-2 border-rose-300 shadow-sm transition transform hover:-translate-y-0.5"
      }
    >
      <WandSparkles className="w-4 h-4 text-rose-500" />
      <span>{label}</span>
    </button>
  );
}

interface QuizOption {
  label: string;
  subtext: string;
  skinType: "Da dầu" | "Da khô" | "Da nhạy cảm" | "Mọi loại da";
  tag: string;
}

interface QuizQuestion {
  id: number;
  question: string;
  subtitle: string;
  options: QuizOption[];
}

const QUIZ_QUESTIONS: QuizQuestion[] = [
  {
    id: 1,
    question:
      "Sau khi rửa mặt khoảng 30 phút (không thoa toner hay kem dưỡng), da bạn có cảm giác gì?",
    subtitle: "Câu 1/4 • Đánh giá độ cân bằng dầu và nước tự nhiên",
    options: [
      {
        label: "Bóng nhờn rõ rệt ở vùng chữ T (trán, mũi, cằm) và hai bên má",
        subtext: "Tuyến bã nhờn tiết dầu nhanh, da có độ bóng thấy rõ",
        skinType: "Da dầu",
        tag: "Dư thừa bã nhờn",
      },
      {
        label: "Khô căng, hơi ráp hoặc có cảm giác thiếu độ ẩm",
        subtext: "Da kém mềm mại nếu không cấp ẩm ngay sau khi làm sạch",
        skinType: "Da khô",
        tag: "Thiếu hụt độ ẩm",
      },
      {
        label: "Hơi ửng đỏ, châm chích nhẹ hoặc dễ ngứa rát",
        subtext: "Da phản ứng nhanh với nhiệt độ nước hoặc sữa rửa mặt",
        skinType: "Da nhạy cảm",
        tag: "Màng ẩm mỏng yếu",
      },
      {
        label: "Mềm mại, thoải mái, không quá khô cũng không bóng dầu",
        subtext: "Độ ẩm tự nhiên ổn định và dễ chịu",
        skinType: "Mọi loại da",
        tag: "Nền da cân bằng",
      },
    ],
  },
  {
    id: 2,
    question: "Tình trạng lỗ chân lông và bề mặt da hiện tại của bạn như thế nào?",
    subtitle: "Câu 2/4 • Quan sát cấu trúc biểu bì & cổ nang lông",
    options: [
      {
        label: "Lỗ chân lông to, dễ bít tắc, hay có mụn ẩn hoặc mụn đầu đen",
        subtext: "Đặc biệt rõ ở vùng cánh mũi và hai bên má trong",
        skinType: "Da dầu",
        tag: "Cần BHA & Niacinamide",
      },
      {
        label: "Lỗ chân lông nhỏ nhưng bề mặt da thô ráp, dễ có vân khô li ti",
        subtext: "Da thiếu độ căng bóng ngậm nước, đôi khi bong vảy nhẹ",
        skinType: "Da khô",
        tag: "Cần Hyaluronic Acid",
      },
      {
        label: "Da mỏng, dễ thấy mao mạch hoặc hay nổi mẩn đỏ khi kích ứng",
        subtext: "Đang cần phục hồi hàng rào bảo vệ da hoặc sau treatment",
        skinType: "Da nhạy cảm",
        tag: "Cần Vitamin B5 & Rau má",
      },
      {
        label: "Lỗ chân lông nhỏ mịn, bề mặt da tương đối đồng đều",
        subtext: "Ít khuyết điểm lớn, muốn duy trì độ săn chắc lâu dài",
        skinType: "Mọi loại da",
        tag: "Cần Retinol & Peptide",
      },
    ],
  },
  {
    id: 3,
    question:
      "Vào giữa ngày (sau 4–6 tiếng làm việc), làn da của bạn thường phản ứng ra sao?",
    subtitle: "Câu 3/4 • Độ bền màng ẩm trước môi trường & thời tiết",
    options: [
      {
        label: "Đổ dầu nhiều, lớp kem chống nắng/trang điểm nhanh bị bóng hoặc xuống tông",
        subtext: "Phải dùng giấy thấm dầu thường xuyên trong ngày",
        skinType: "Da dầu",
        tag: "Tiết dầu liên tục",
      },
      {
        label: "Da khô mốc (cakey) khi ngồi điều hòa, cảm giác da bị rút nước",
        subtext: "Vùng quanh miệng và mắt dễ lộ nếp nhăn mảnh",
        skinType: "Da khô",
        tag: "Mất nước qua biểu bì",
      },
      {
        label: "Dễ đỏ ửng, nóng rát khi ra nắng, gặp khói bụi hoặc thay đổi thời tiết",
        subtext: "Khó chọn mỹ phẩm vì dễ bị châm chích với hương liệu/cồn",
        skinType: "Da nhạy cảm",
        tag: "Dễ kích ứng ngoại cảnh",
      },
      {
        label: "Da giữ trạng thái ổn định cả ngày, chỉ hơi bóng nhẹ vùng đầu mũi",
        subtext: "Thích nghi tốt với cả môi trường văn phòng và ngoài trời",
        skinType: "Mọi loại da",
        tag: "Độ đàn hồi tốt",
      },
    ],
  },
  {
    id: 4,
    question:
      "Mục tiêu ưu tiên hàng đầu bạn muốn cải thiện cho làn da ngay lúc này là gì?",
    subtitle: "Câu 4/4 • Định hướng hoạt chất điều trị & nuôi dưỡng chuyên sâu",
    options: [
      {
        label: "Kiểm soát dầu thừa, làm sạch sâu lỗ chân lông & giảm thâm mụn",
        subtext: "Ưu tiên Niacinamide 10%, Zinc PCA 1%, Salicylic Acid (BHA 2%)",
        skinType: "Da dầu",
        tag: "Kiềm dầu & Sạch mụn",
      },
      {
        label: "Cấp nước đa tầng, khóa ẩm chuyên sâu & phục hồi độ căng bóng",
        subtext: "Ưu tiên Hyaluronic Acid 2%, Nhân sâm cô đặc, Squalane",
        skinType: "Da khô",
        tag: "Cấp ẩm & Căng bóng",
      },
      {
        label: "Làm dịu kích ứng tức thì, phục hồi màng ẩm tự nhiên khỏe mạnh",
        subtext: "Ưu tiên Panthenol 5% (Vitamin B5), Madecassoside, Bơ Hạt Mỡ",
        skinType: "Da nhạy cảm",
        tag: "Làm dịu & Phục hồi B5",
      },
      {
        label: "Trẻ hóa làn da, mờ nếp nhăn, trang điểm rạng rỡ & lưu hương",
        subtext: "Ưu tiên Retinol 0.5% tinh khiết, Cushion dưỡng ẩm & Nước hoa",
        skinType: "Mọi loại da",
        tag: "Chống lão hóa & Toàn diện",
      },
    ],
  },
];

interface RecommendedProduct {
  id: string;
  name: string;
  slug: string;
  price: number;
  salePrice: number | null;
  images: string[];
  description: string;
  ingredients: string;
  skinType: string;
  volume: string;
  isBestSeller: boolean;
  matchScore: number;
  isExactMatch: boolean;
  aiReason: string;
  brand: {
    name: string;
    origin: string;
  };
  category: {
    name: string;
    slug: string;
  };
}

interface QuizResultData {
  skinType: string;
  exactMatchCount: number;
  totalRecommended: number;
  profile: {
    skinType: string;
    title: string;
    badge: string;
    description: string;
    recommendedActives: string[];
    avoidNote: string;
    morningRoutine: string;
    eveningRoutine: string;
  };
  recommendedProducts: RecommendedProduct[];
}

export default function SkinQuizModal() {
  const [isOpen, setIsOpen] = useState(false);
  const [currentStep, setCurrentStep] = useState(0);
  const [answers, setAnswers] = useState<string[]>([]);
  const [isAnalyzing, setIsAnalyzing] = useState(false);
  const [result, setResult] = useState<QuizResultData | null>(null);
  const [onlyExactSkinType, setOnlyExactSkinType] = useState(false);

  const { addToCart } = useCart();
  const router = useRouter();

  useEffect(() => {
    const handleOpen = () => {
      setIsOpen(true);
    };
    window.addEventListener("open-skin-quiz", handleOpen);
    return () => window.removeEventListener("open-skin-quiz", handleOpen);
  }, []);

  const submitQuizAnswers = async (selectedAnswers: string[]) => {
    setIsAnalyzing(true);
    try {
      const res = await fetch("/api/skin-quiz", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ answers: selectedAnswers }),
      });
      if (!res.ok) throw new Error("Failed to analyze skin type");
      const data: QuizResultData = await res.json();
      setResult(data);
      setOnlyExactSkinType(false);

      // Notify homepage SkinTypeExplorer & ProductsFilterClient so they auto-filter
      window.dispatchEvent(
        new CustomEvent("skin-quiz-completed", {
          detail: { skinType: data.skinType },
        })
      );
    } catch (error) {
      console.error("Skin quiz error:", error);
    } finally {
      setIsAnalyzing(false);
    }
  };

  const handleSelectOption = (skinType: string) => {
    const updated = [...answers];
    updated[currentStep] = skinType;
    setAnswers(updated);

    if (currentStep < QUIZ_QUESTIONS.length - 1) {
      setCurrentStep(currentStep + 1);
    } else {
      submitQuizAnswers(updated);
    }
  };

  const handleReset = () => {
    setCurrentStep(0);
    setAnswers([]);
    setResult(null);
    setOnlyExactSkinType(false);
  };

  const handleApplyFilterPage = () => {
    if (!result) return;
    setIsOpen(false);
    router.push(
      `/products?skinType=${encodeURIComponent(result.skinType)}`
    );
  };

  const displayedProducts = result
    ? onlyExactSkinType
      ? result.recommendedProducts.filter((p) => p.isExactMatch)
      : result.recommendedProducts
    : [];

  const getSkinIcon = (type: string) => {
    if (type === "Da dầu") return Droplets;
    if (type === "Da khô") return Feather;
    if (type === "Da nhạy cảm") return ShieldAlert;
    return Sparkles;
  };

  return (
    <>
      {/* Floating Quick Trigger Button */}
      <button
        onClick={() => setIsOpen(true)}
        data-testid="floating-skin-quiz-btn"
        className="fixed bottom-6 right-6 z-40 flex items-center gap-2.5 bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white font-semibold px-5 py-3.5 rounded-full shadow-xl shadow-rose-300/70 border-2 border-white transition transform hover:scale-105"
      >
        <Sparkles className="w-4 h-4 animate-pulse" />
        <span className="text-xs sm:text-sm">Trắc nghiệm da 30s (AI)</span>
      </button>

      {/* Modal Overlay */}
      {isOpen && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-stone-950/60 backdrop-blur-sm overflow-y-auto"
          role="dialog"
          aria-modal="true"
          aria-labelledby="skin-quiz-title"
        >
          <div className="relative w-full max-w-4xl bg-[#FDFBF7] rounded-3xl border border-rose-200 shadow-2xl overflow-hidden my-auto max-h-[92vh] flex flex-col">
            {/* Modal Header */}
            <div className="bg-gradient-to-r from-rose-500 via-rose-500 to-rose-400 text-white px-6 py-5 flex items-center justify-between flex-shrink-0">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-xs flex items-center justify-center">
                  <Sparkles className="w-5 h-5 text-white" />
                </div>
                <div>
                  <div className="flex items-center gap-2">
                    <span className="text-[11px] uppercase tracking-widest font-bold bg-white/20 px-2.5 py-0.5 rounded-full inline-flex items-center gap-1">
                      <Clock className="w-3 h-3" /> AI Skin Advisor • 30 Giây
                    </span>
                  </div>
                  <h2
                    id="skin-quiz-title"
                    className="font-serif text-xl sm:text-2xl font-bold mt-0.5"
                  >
                    Trắc Nghiệm Loại Da Trong 30 Giây
                  </h2>
                </div>
              </div>

              <button
                onClick={() => setIsOpen(false)}
                aria-label="Đóng popup"
                className="w-9 h-9 rounded-full bg-white/15 hover:bg-white/30 flex items-center justify-center text-white transition"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            {/* Modal Body */}
            <div className="p-6 sm:p-8 overflow-y-auto flex-1 space-y-6">
              {/* STATE 1: ANALYZING */}
              {isAnalyzing && (
                <div className="py-16 text-center space-y-4">
                  <div className="w-16 h-16 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto animate-pulse">
                    <FlaskConical className="w-8 h-8" />
                  </div>
                  <h3 className="font-serif text-2xl font-bold text-stone-800">
                    AI Đang Phân Tích Hồ Sơ Làn Da Của Bạn...
                  </h3>
                  <p className="text-sm text-stone-500 max-w-md mx-auto">
                    Hệ thống đang đối chiếu 4 câu trả lời với cơ sở dữ liệu hoạt chất
                    (Niacinamide, BHA, Hyaluronic Acid, B5, Retinol) để chọn sản phẩm
                    phù hợp nhất.
                  </p>
                </div>
              )}

              {/* STATE 2: QUESTION STEPPER */}
              {!isAnalyzing && !result && (
                <div className="space-y-6">
                  {/* Progress Bar */}
                  <div className="space-y-2">
                    <div className="flex items-center justify-between text-xs font-semibold text-stone-500">
                      <span>
                        Tiến độ trắc nghiệm: Câu {currentStep + 1} /{" "}
                        {QUIZ_QUESTIONS.length}
                      </span>
                      <span className="text-rose-600">
                        {Math.round(
                          ((currentStep + 1) / QUIZ_QUESTIONS.length) * 100
                        )}
                        % hoàn thành
                      </span>
                    </div>
                    <div className="w-full h-2.5 bg-rose-100 rounded-full overflow-hidden">
                      <div
                        className="h-full bg-gradient-to-r from-rose-400 to-rose-600 rounded-full transition-all duration-300"
                        style={{
                          width: `${
                            ((currentStep + 1) / QUIZ_QUESTIONS.length) * 100
                          }%`,
                        }}
                      />
                    </div>
                  </div>

                  {/* Active Question */}
                  <div className="bg-white p-6 sm:p-7 rounded-3xl border border-rose-100 shadow-2xs space-y-6">
                    <div>
                      <span className="text-xs font-bold uppercase tracking-wider text-rose-500">
                        {QUIZ_QUESTIONS[currentStep].subtitle}
                      </span>
                      <h3 className="font-serif text-xl sm:text-2xl font-bold text-stone-800 mt-1 leading-snug">
                        {QUIZ_QUESTIONS[currentStep].question}
                      </h3>
                    </div>

                    <div className="grid grid-cols-1 gap-3.5">
                      {QUIZ_QUESTIONS[currentStep].options.map((opt, idx) => {
                        const isSelected = answers[currentStep] === opt.skinType;
                        return (
                          <button
                            key={idx}
                            type="button"
                            onClick={() => handleSelectOption(opt.skinType)}
                            data-testid={`quiz-option-${idx}`}
                            className={`group w-full text-left p-4 sm:p-5 rounded-2xl border transition-all duration-200 flex items-start justify-between gap-4 ${
                              isSelected
                                ? "bg-rose-50/90 border-rose-500 ring-2 ring-rose-300/60"
                                : "bg-[#FDFBF7] hover:bg-rose-50/50 border-rose-100 hover:border-rose-300"
                            }`}
                          >
                            <div className="space-y-1">
                              <div className="flex flex-wrap items-center gap-2">
                                <span className="inline-flex items-center justify-center w-6 h-6 rounded-full bg-rose-100 text-rose-700 font-bold text-xs">
                                  {String.fromCharCode(65 + idx)}
                                </span>
                                <span className="font-semibold text-stone-800 group-hover:text-rose-700 text-sm sm:text-base">
                                  {opt.label}
                                </span>
                              </div>
                              <p className="text-xs text-stone-500 pl-8">
                                {opt.subtext}
                              </p>
                            </div>

                            <span className="hidden sm:inline-block flex-shrink-0 text-[11px] font-semibold bg-white text-rose-600 px-3 py-1 rounded-full border border-rose-200">
                              {opt.tag}
                            </span>
                          </button>
                        );
                      })}
                    </div>

                    {/* Stepper Footer */}
                    <div className="flex items-center justify-between pt-2 border-t border-rose-100">
                      <button
                        type="button"
                        onClick={() =>
                          setCurrentStep((prev) => Math.max(0, prev - 1))
                        }
                        disabled={currentStep === 0}
                        className="inline-flex items-center gap-1.5 text-xs font-semibold text-stone-600 hover:text-rose-600 disabled:opacity-40 disabled:pointer-events-none"
                      >
                        <ArrowLeft className="w-4 h-4" /> Câu trước
                      </button>

                      <span className="text-xs text-stone-400">
                        Chọn 1 đáp án đúng nhất với tình trạng da hiện tại của bạn
                      </span>
                    </div>
                  </div>
                </div>
              )}

              {/* STATE 3: AI DIAGNOSIS & PRODUCT RECOMMENDATIONS */}
              {!isAnalyzing && result && (
                <div className="space-y-7" data-testid="skin-quiz-result">
                  {/* Diagnosis Summary Banner */}
                  <div className="bg-gradient-to-br from-rose-50 via-white to-rose-50/70 rounded-3xl border border-rose-200 p-6 sm:p-7 shadow-2xs space-y-5">
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-rose-100 pb-5">
                      <div className="flex items-start gap-4">
                        {(() => {
                          const Icon = getSkinIcon(result.skinType);
                          return (
                            <div className="w-14 h-14 rounded-2xl bg-rose-500 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-rose-200">
                              <Icon className="w-7 h-7" />
                            </div>
                          );
                        })()}
                        <div>
                          <div className="flex flex-wrap items-center gap-2">
                            <span className="text-xs font-bold uppercase tracking-wider text-rose-600 bg-rose-100 px-3 py-0.5 rounded-full">
                              Kết Quả Phân Tích AI
                            </span>
                            <span className="text-xs font-semibold text-stone-500">
                              • {result.profile.badge}
                            </span>
                          </div>
                          <h3 className="font-serif text-2xl sm:text-3xl font-bold text-stone-900 mt-1">
                            Loại Da Của Bạn:{" "}
                            <span className="text-rose-600">
                              {result.profile.title}
                            </span>
                          </h3>
                        </div>
                      </div>

                      <button
                        type="button"
                        onClick={handleReset}
                        className="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600 hover:text-rose-700 bg-white px-4 py-2.5 rounded-full border border-rose-200 shadow-2xs self-start"
                      >
                        <RotateCcw className="w-3.5 h-3.5" /> Làm lại (30s)
                      </button>
                    </div>

                    <p className="text-sm text-stone-600 leading-relaxed">
                      {result.profile.description}
                    </p>

                    {/* Active Ingredients Pills */}
                    <div>
                      <p className="text-xs font-bold uppercase tracking-wider text-stone-500 mb-2 flex items-center gap-1.5">
                        <FlaskConical className="w-3.5 h-3.5 text-rose-500" />
                        Hoạt Chất Vàng Khuyên Dùng Cho {result.skinType}:
                      </p>
                      <div className="flex flex-wrap gap-2">
                        {result.profile.recommendedActives.map((active, i) => (
                          <span
                            key={i}
                            className="inline-flex items-center gap-1.5 bg-white text-rose-700 border border-rose-200 px-3.5 py-1.5 rounded-full text-xs font-semibold shadow-2xs"
                          >
                            <CheckCircle2 className="w-3.5 h-3.5 text-rose-500" />
                            {active}
                          </span>
                        ))}
                      </div>
                    </div>

                    {/* Routine Morning / Evening */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-3.5 pt-1">
                      <div className="bg-white/90 p-4 rounded-2xl border border-rose-100 space-y-1">
                        <div className="flex items-center gap-1.5 text-xs font-bold text-amber-600 uppercase tracking-wider">
                          <Sun className="w-4 h-4" /> Chu trình Buổi Sáng
                        </div>
                        <p className="text-xs text-stone-600 leading-relaxed">
                          {result.profile.morningRoutine}
                        </p>
                      </div>
                      <div className="bg-white/90 p-4 rounded-2xl border border-rose-100 space-y-1">
                        <div className="flex items-center gap-1.5 text-xs font-bold text-indigo-600 uppercase tracking-wider">
                          <Moon className="w-4 h-4" /> Chu trình Buổi Tối
                        </div>
                        <p className="text-xs text-stone-600 leading-relaxed">
                          {result.profile.eveningRoutine}
                        </p>
                      </div>
                    </div>
                  </div>

                  {/* Recommended Products from Database */}
                  <div className="space-y-4">
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                      <div>
                        <span className="text-xs font-bold uppercase tracking-wider text-rose-500">
                          Đã Lọc Tự Động Từ Cơ Sở Dữ Liệu
                        </span>
                        <h4 className="font-serif text-xl font-bold text-stone-800">
                          Mỹ Phẩm Phù Hợp Nhất Với {result.skinType} (
                          {displayedProducts.length} sản phẩm)
                        </h4>
                      </div>

                      {result.skinType !== "Mọi loại da" && (
                        <div className="flex items-center gap-2 bg-white p-1 rounded-full border border-rose-200 self-start">
                          <button
                            type="button"
                            onClick={() => setOnlyExactSkinType(false)}
                            className={`text-xs font-semibold px-3.5 py-1.5 rounded-full transition ${
                              !onlyExactSkinType
                                ? "bg-rose-500 text-white"
                                : "text-stone-600 hover:text-rose-600"
                            }`}
                          >
                            Tất cả phù hợp ({result.totalRecommended})
                          </button>
                          <button
                            type="button"
                            onClick={() => setOnlyExactSkinType(true)}
                            className={`text-xs font-semibold px-3.5 py-1.5 rounded-full transition ${
                              onlyExactSkinType
                                ? "bg-rose-500 text-white"
                                : "text-stone-600 hover:text-rose-600"
                            }`}
                          >
                            Chuyên biệt {result.skinType} (
                            {result.exactMatchCount})
                          </button>
                        </div>
                      )}
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      {displayedProducts.map((product) => {
                        const finalPrice = product.salePrice ?? product.price;
                        return (
                          <div
                            key={product.id}
                            className="bg-white rounded-2xl border border-rose-100 p-4 flex gap-4 hover:shadow-md hover:border-rose-200 transition"
                          >
                            <Link
                              href={`/products/${product.slug}`}
                              onClick={() => setIsOpen(false)}
                              className="w-24 h-28 sm:w-28 sm:h-32 rounded-xl overflow-hidden bg-rose-50 flex-shrink-0 relative"
                            >
                              <img
                                src={
                                  product.images?.[0] ||
                                  "https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=800&q=80"
                                }
                                alt={product.name}
                                className="w-full h-full object-cover"
                              />
                              <span className="absolute top-1.5 left-1.5 bg-rose-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                                {product.matchScore}% Hợp
                              </span>
                            </Link>

                            <div className="flex-1 flex flex-col justify-between min-w-0">
                              <div>
                                <div className="flex items-center justify-between gap-2">
                                  <span className="text-[11px] font-bold uppercase tracking-wider text-rose-500 truncate">
                                    {product.brand?.name}
                                  </span>
                                  <span className="text-[11px] bg-rose-50 text-rose-700 px-2 py-0.5 rounded-full font-medium flex-shrink-0">
                                    {product.skinType}
                                  </span>
                                </div>

                                <Link
                                  href={`/products/${product.slug}`}
                                  onClick={() => setIsOpen(false)}
                                  className="font-serif font-bold text-stone-800 hover:text-rose-600 text-sm line-clamp-2 mt-0.5"
                                >
                                  {product.name}
                                </Link>

                                <p className="text-[11px] text-stone-500 mt-1 line-clamp-2">
                                  {product.aiReason}
                                </p>
                              </div>

                              <div className="flex items-center justify-between gap-2 pt-2 mt-2 border-t border-rose-50">
                                <div>
                                  <span className="text-sm font-bold text-rose-600">
                                    {formatVND(finalPrice)}
                                  </span>
                                  {product.salePrice &&
                                    product.salePrice < product.price && (
                                      <span className="block text-[10px] text-stone-400 line-through">
                                        {formatVND(product.price)}
                                      </span>
                                    )}
                                </div>

                                <button
                                  type="button"
                                  onClick={() =>
                                    addToCart({
                                      id: product.id,
                                      name: product.name,
                                      slug: product.slug,
                                      price: finalPrice,
                                      image: product.images?.[0] || "",
                                      volume: product.volume,
                                      skinType: product.skinType,
                                      brandName: product.brand?.name,
                                    })
                                  }
                                  className="inline-flex items-center gap-1 bg-rose-500 hover:bg-rose-600 text-white text-xs font-semibold px-3 py-2 rounded-full transition"
                                >
                                  <ShoppingBag className="w-3.5 h-3.5" />
                                  <span>Thêm giỏ</span>
                                </button>
                              </div>
                            </div>
                          </div>
                        );
                      })}
                    </div>

                    {/* Bottom CTA bar */}
                    <div className="bg-rose-50/80 border border-rose-200/80 rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                      <div className="flex items-center gap-2 text-xs text-stone-600">
                        <Filter className="w-4 h-4 text-rose-500 flex-shrink-0" />
                        <span>
                          Bạn có thể áp dụng trực tiếp bộ lọc{" "}
                          <strong className="text-rose-700">
                            {result.skinType}
                          </strong>{" "}
                          trên trang danh sách sản phẩm.
                        </span>
                      </div>

                      <button
                        type="button"
                        onClick={handleApplyFilterPage}
                        className="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-stone-900 hover:bg-stone-800 text-white text-xs font-semibold px-5 py-2.5 rounded-full transition flex-shrink-0"
                      >
                        <span>Xem trên trang Sản phẩm ({result.skinType})</span>
                        <ArrowRight className="w-3.5 h-3.5" />
                      </button>
                    </div>
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>
      )}
    </>
  );
}
