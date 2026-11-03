"use client";

import React, { useState } from "react";
import { useCart } from "@/context/CartContext";
import { formatVND } from "@/lib/prisma";
import {
  ShoppingBag,
  Star,
  Check,
  Sparkles,
  ShieldCheck,
  Droplets,
  Package,
  Plus,
  Minus,
} from "lucide-react";

interface ReviewItem {
  id: string;
  rating: number;
  customerName: string;
  comment: string;
  createdAt: string | Date;
}

interface ProductDetailProps {
  product: {
    id: string;
    name: string;
    slug: string;
    price: number;
    salePrice: number | null;
    stock: number;
    images: string[];
    description: string;
    ingredients: string;
    skinType: string;
    volume: string;
    isBestSeller: boolean;
    category: { name: string; slug: string };
    brand: { name: string; origin: string };
    reviews: ReviewItem[];
  };
}

export default function ProductDetailClient({ product }: ProductDetailProps) {
  const { addToCart } = useCart();
  const [selectedImage, setSelectedImage] = useState(
    product.images?.[0] ||
      "https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80"
  );
  const [quantity, setQuantity] = useState(1);
  const [activeTab, setActiveTab] = useState<"DESC" | "INGREDIENTS">("DESC");
  const [reviews, setReviews] = useState<ReviewItem[]>(product.reviews || []);
  const [customerName, setCustomerName] = useState("");
  const [rating, setRating] = useState(5);
  const [comment, setComment] = useState("");
  const [submittingReview, setSubmittingReview] = useState(false);

  const finalPrice = product.salePrice ?? product.price;

  const handleAddToCart = () => {
    addToCart(
      {
        id: product.id,
        name: product.name,
        slug: product.slug,
        price: finalPrice,
        image: selectedImage,
        volume: product.volume,
        skinType: product.skinType,
        brandName: product.brand.name,
      },
      quantity
    );
  };

  const handleReviewSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!customerName.trim() || !comment.trim()) return;
    setSubmittingReview(true);
    try {
      const res = await fetch("/api/reviews", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          productId: product.id,
          customerName,
          rating,
          comment,
        }),
      });
      if (res.ok) {
        const created = await res.json();
        setReviews([created, ...reviews]);
        setCustomerName("");
        setComment("");
        setRating(5);
      }
    } finally {
      setSubmittingReview(false);
    }
  };

  return (
    <div className="space-y-14">
      {/* Top Product Overview */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 bg-white p-6 sm:p-10 rounded-3xl border border-rose-100 shadow-xs">
        {/* Left Gallery */}
        <div className="lg:col-span-6 space-y-4">
          <div className="aspect-square rounded-3xl overflow-hidden bg-rose-50/40 border border-rose-100">
            <img
              src={selectedImage}
              alt={product.name}
              className="w-full h-full object-cover"
            />
          </div>
          {product.images.length > 1 && (
            <div className="flex gap-3">
              {product.images.map((img, idx) => (
                <button
                  key={idx}
                  onClick={() => setSelectedImage(img)}
                  className={`w-20 h-20 rounded-2xl overflow-hidden border-2 transition ${
                    selectedImage === img
                      ? "border-rose-500 scale-105"
                      : "border-rose-100 opacity-70 hover:opacity-100"
                  }`}
                >
                  <img
                    src={img}
                    alt={`${product.name} ${idx + 1}`}
                    className="w-full h-full object-cover"
                  />
                </button>
              ))}
            </div>
          )}
        </div>

        {/* Right Details */}
        <div className="lg:col-span-6 flex flex-col justify-between space-y-6">
          <div className="space-y-4">
            <div className="flex flex-wrap items-center gap-2">
              <span className="bg-rose-100 text-rose-700 text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full">
                {product.brand.name} • {product.brand.origin}
              </span>
              <span className="bg-[#FAF6EE] text-stone-700 text-xs font-medium px-3 py-1 rounded-full border border-rose-200">
                {product.category.name}
              </span>
              {product.isBestSeller && (
                <span className="inline-flex items-center gap-1 bg-rose-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                  <Sparkles className="w-3 h-3" /> Best Seller
                </span>
              )}
            </div>

            <h1 className="font-serif text-2xl sm:text-3xl font-bold text-stone-900 leading-snug">
              {product.name}
            </h1>

            {/* Rating summary */}
            <div className="flex items-center gap-2">
              <div className="flex text-amber-400">
                {[...Array(5)].map((_, i) => (
                  <Star key={i} className="w-4 h-4 fill-amber-400" />
                ))}
              </div>
              <span className="text-sm font-semibold text-stone-700">5.0</span>
              <span className="text-xs text-stone-400">
                ({reviews.length} đánh giá từ khách hàng)
              </span>
            </div>

            {/* Price Box */}
            <div className="bg-rose-50/60 p-5 rounded-2xl border border-rose-100 flex items-baseline gap-4">
              <span className="text-3xl font-bold text-rose-600">
                {formatVND(finalPrice)}
              </span>
              {product.salePrice && product.salePrice < product.price && (
                <>
                  <span className="text-base text-stone-400 line-through">
                    {formatVND(product.price)}
                  </span>
                  <span className="bg-rose-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                    Tiết kiệm {formatVND(product.price - product.salePrice)}
                  </span>
                </>
              )}
            </div>

            {/* Cosmetics Spec Badges */}
            <div className="grid grid-cols-2 gap-4 pt-2">
              <div className="p-4 rounded-2xl bg-[#FDFBF7] border border-rose-100 flex items-center gap-3">
                <div className="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                  <Droplets className="w-5 h-5" />
                </div>
                <div>
                  <p className="text-[11px] uppercase tracking-wider text-stone-400">
                    Loại da phù hợp
                  </p>
                  <p className="text-sm font-bold text-stone-800">
                    {product.skinType}
                  </p>
                </div>
              </div>

              <div className="p-4 rounded-2xl bg-[#FDFBF7] border border-rose-100 flex items-center gap-3">
                <div className="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                  <Package className="w-5 h-5" />
                </div>
                <div>
                  <p className="text-[11px] uppercase tracking-wider text-stone-400">
                    Dung tích / Trọng lượng
                  </p>
                  <p className="text-sm font-bold text-stone-800">
                    {product.volume}
                  </p>
                </div>
              </div>
            </div>
          </div>

          {/* Quantity & Add to Cart */}
          <div className="space-y-4 pt-4 border-t border-rose-100">
            <div className="flex flex-wrap items-center gap-4">
              <div className="inline-flex items-center border border-rose-200 rounded-full bg-[#FDFBF7] px-2 py-1">
                <button
                  onClick={() => setQuantity(Math.max(1, quantity - 1))}
                  className="p-2 text-stone-600 hover:text-rose-600"
                >
                  <Minus className="w-4 h-4" />
                </button>
                <span className="px-5 font-bold text-stone-800">{quantity}</span>
                <button
                  onClick={() => setQuantity(quantity + 1)}
                  className="p-2 text-stone-600 hover:text-rose-600"
                >
                  <Plus className="w-4 h-4" />
                </button>
              </div>

              <button
                onClick={handleAddToCart}
                className="flex-1 inline-flex items-center justify-center gap-2.5 bg-rose-500 hover:bg-rose-600 text-white font-semibold py-4 px-8 rounded-full shadow-lg shadow-rose-200 transition"
              >
                <ShoppingBag className="w-5 h-5" />
                <span>Thêm Vào Giỏ Hàng</span>
              </button>
            </div>

            <div className="flex flex-wrap items-center gap-6 text-xs text-stone-500 pt-2">
              <span className="inline-flex items-center gap-1.5">
                <Check className="w-4 h-4 text-emerald-600" /> Còn hàng ({product.stock} sản phẩm)
              </span>
              <span className="inline-flex items-center gap-1.5">
                <ShieldCheck className="w-4 h-4 text-rose-500" /> Cam kết chính hãng 100%
              </span>
            </div>
          </div>
        </div>
      </div>

      {/* Tabs: Mô tả chi tiết & Bảng thành phần (Ingredients) */}
      <div className="bg-white rounded-3xl border border-rose-100 p-6 sm:p-10 shadow-xs">
        <div className="flex border-b border-rose-100 gap-6 mb-6">
          <button
            onClick={() => setActiveTab("DESC")}
            className={`pb-4 text-base font-serif font-bold border-b-2 transition ${
              activeTab === "DESC"
                ? "border-rose-500 text-rose-600"
                : "border-transparent text-stone-400 hover:text-stone-700"
            }`}
          >
            Mô Tả Chi Tiết Công Dụng
          </button>
          <button
            onClick={() => setActiveTab("INGREDIENTS")}
            className={`pb-4 text-base font-serif font-bold border-b-2 transition ${
              activeTab === "INGREDIENTS"
                ? "border-rose-500 text-rose-600"
                : "border-transparent text-stone-400 hover:text-stone-700"
            }`}
          >
            Bảng Thành Phần (Ingredients)
          </button>
        </div>

        {activeTab === "DESC" ? (
          <div className="space-y-4 text-stone-600 leading-relaxed">
            <p className="text-base">{product.description}</p>
            <div className="bg-[#FDFBF7] p-5 rounded-2xl border border-rose-100 mt-4">
              <h4 className="font-serif font-bold text-stone-800 mb-2">
                Hướng dẫn sử dụng chuẩn khoa học:
              </h4>
              <ul className="list-disc list-inside text-sm space-y-1.5 text-stone-600">
                <li>Làm sạch da mặt với sữa rửa mặt dịu nhẹ và cân bằng ẩm với Toner.</li>
                <li>Lấy một lượng vừa đủ thoa đều lên da và vỗ nhẹ để dưỡng chất thẩm thấu.</li>
                <li>Sử dụng kem chống nắng phổ rộng SPF50+ vào ban ngày để bảo vệ da tối ưu.</li>
              </ul>
            </div>
          </div>
        ) : (
          <div className="space-y-4">
            <div className="bg-rose-50/50 p-6 rounded-2xl border border-rose-100">
              <h4 className="text-xs font-bold uppercase tracking-wider text-rose-600 mb-2">
                Bảng thành phần đầy đủ (Full Ingredients List)
              </h4>
              <p className="text-sm text-stone-700 font-mono leading-relaxed">
                {product.ingredients}
              </p>
            </div>
            <p className="text-xs text-stone-400">
              * Không chứa Paraben, không dầu khoáng, an toàn và lành tính cho{" "}
              {product.skinType.toLowerCase()}.
            </p>
          </div>
        )}
      </div>

      {/* Customer Reviews Section */}
      <div className="bg-white rounded-3xl border border-rose-100 p-6 sm:p-10 shadow-xs space-y-8">
        <h3 className="font-serif text-2xl font-bold text-stone-800">
          Đánh Giá Từ Khách Hàng ({reviews.length})
        </h3>

        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
          <div className="lg:col-span-7 space-y-4">
            {reviews.map((rev) => (
              <div
                key={rev.id}
                className="p-5 rounded-2xl bg-[#FDFBF7] border border-rose-100 space-y-2"
              >
                <div className="flex items-center justify-between">
                  <span className="font-bold text-sm text-stone-800">
                    {rev.customerName}
                  </span>
                  <div className="flex text-amber-400">
                    {[...Array(rev.rating)].map((_, idx) => (
                      <Star key={idx} className="w-3.5 h-3.5 fill-amber-400" />
                    ))}
                  </div>
                </div>
                <p className="text-sm text-stone-600">{rev.comment}</p>
              </div>
            ))}
          </div>

          <form
            onSubmit={handleReviewSubmit}
            className="lg:col-span-5 bg-rose-50/40 p-6 rounded-2xl border border-rose-100 space-y-4 h-fit"
          >
            <h4 className="font-serif font-bold text-stone-800">
              Gửi đánh giá của bạn
            </h4>
            <div>
              <label className="block text-xs font-medium text-stone-600 mb-1">
                Họ và tên
              </label>
              <input
                type="text"
                required
                value={customerName}
                onChange={(e) => setCustomerName(e.target.value)}
                placeholder="Nhập tên của bạn"
                className="w-full px-3.5 py-2 text-sm rounded-xl border border-rose-200 bg-white"
              />
            </div>
            <div>
              <label className="block text-xs font-medium text-stone-600 mb-1">
                Số sao đánh giá
              </label>
              <select
                value={rating}
                onChange={(e) => setRating(Number(e.target.value))}
                className="w-full px-3.5 py-2 text-sm rounded-xl border border-rose-200 bg-white"
              >
                <option value={5}>⭐⭐⭐⭐⭐ (5 sao - Tuyệt vời)</option>
                <option value={4}>⭐⭐⭐⭐ (4 sao - Hài lòng)</option>
                <option value={3}>⭐⭐⭐ (3 sao - Bình thường)</option>
              </select>
            </div>
            <div>
              <label className="block text-xs font-medium text-stone-600 mb-1">
                Cảm nhận về sản phẩm
              </label>
              <textarea
                rows={3}
                required
                value={comment}
                onChange={(e) => setComment(e.target.value)}
                placeholder="Chia sẻ trải nghiệm dưỡng da của bạn..."
                className="w-full px-3.5 py-2 text-sm rounded-xl border border-rose-200 bg-white"
              />
            </div>
            <button
              type="submit"
              disabled={submittingReview}
              className="w-full bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold py-2.5 rounded-full transition"
            >
              {submittingReview ? "Đang gửi..." : "Gửi Đánh Giá"}
            </button>
          </form>
        </div>
      </div>
    </div>
  );
}
