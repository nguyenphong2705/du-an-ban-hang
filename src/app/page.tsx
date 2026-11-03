import React from "react";
import Link from "next/link";
import { prisma } from "@/lib/prisma";
import ProductCard from "@/components/ProductCard";
import SkinTypeExplorer from "@/components/SkinTypeExplorer";
import FlashSaleSection from "@/components/FlashSaleSection";
import { SkinQuizTriggerButton } from "@/components/SkinQuizModal";
import {
  Sparkles,
  ArrowRight,
  CheckCircle2,
  Award,
  HeartHandshake,
} from "lucide-react";

export const dynamic = "force-dynamic";

export default async function HomePage() {
  const now = new Date();
  const [categories, brands, allProducts, activeCoupons] = await Promise.all([
    prisma.category.findMany(),
    prisma.brand.findMany(),
    prisma.product.findMany({
      include: {
        brand: true,
        category: true,
      },
      orderBy: {
        createdAt: "desc",
      },
    }),
    prisma.coupon.findMany({
      where: {
        isActive: true,
        expiresAt: { gt: now },
      },
      orderBy: {
        discountPercent: "desc",
      },
    }),
  ]);

  const bestSellers = allProducts.filter((p) => p.isBestSeller).slice(0, 6);

  return (
    <div className="space-y-20">
      {/* 1. HERO BANNER */}
      <section className="relative overflow-hidden bg-gradient-to-br from-rose-100/80 via-[#FDFBF7] to-[#FAF6EE] border-b border-rose-100">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-24">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            {/* Left column */}
            <div className="lg:col-span-7 space-y-6">
              <div className="inline-flex items-center gap-2 bg-white/90 border border-rose-200 text-rose-600 px-4 py-1.5 rounded-full text-xs font-semibold tracking-wide shadow-2xs">
                <Sparkles className="w-3.5 h-3.5" />
                <span>BỘ SƯU TẬP DƯỠNG DA MỚI 2026 — GLOW & RADIANCE</span>
              </div>

              <h1 className="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold text-stone-900 leading-[1.15]">
                Đánh Thức Vẻ Đẹp <br />
                <span className="text-rose-500 italic font-normal">
                  Thuần Khiết & Rạng Ngời
                </span>
              </h1>

              <p className="text-stone-600 text-base sm:text-lg leading-relaxed max-w-2xl">
                Khám phá quy trình chăm sóc da chuẩn khoa học với Niacinamide,
                BHA, Retinol tinh khiết và Hyaluronic Acid đa tầng từ những
                thương hiệu danh tiếng hàng đầu Pháp, Hàn Quốc và Nhật Bản.
              </p>

              <div className="flex flex-wrap items-center gap-4 pt-2">
                <Link
                  href="/products"
                  className="inline-flex items-center gap-2.5 bg-rose-500 hover:bg-rose-600 text-white font-semibold px-8 py-4 rounded-full shadow-lg shadow-rose-200/90 transition transform hover:-translate-y-0.5"
                >
                  <span>Khám Phá Bộ Sưu Tập</span>
                  <ArrowRight className="w-4 h-4" />
                </Link>
                <SkinQuizTriggerButton label="Trắc nghiệm loại da trong 30 giây" />
              </div>

              {/* Trust badges */}
              <div className="grid grid-cols-3 gap-4 pt-6 border-t border-rose-200/60 max-w-lg">
                <div className="flex items-center gap-2 text-xs text-stone-600">
                  <CheckCircle2 className="w-4 h-4 text-rose-500 flex-shrink-0" />
                  <span>100% Chính hãng</span>
                </div>
                <div className="flex items-center gap-2 text-xs text-stone-600">
                  <Award className="w-4 h-4 text-rose-500 flex-shrink-0" />
                  <span>Kiểm nghiệm da liễu</span>
                </div>
                <div className="flex items-center gap-2 text-xs text-stone-600">
                  <HeartHandshake className="w-4 h-4 text-rose-500 flex-shrink-0" />
                  <span>Đổi trả 14 ngày</span>
                </div>
              </div>
            </div>

            {/* Right column visual */}
            <div className="lg:col-span-5 relative">
              <div className="relative rounded-[2.5rem] overflow-hidden shadow-2xl shadow-rose-200/80 border-8 border-white aspect-4/5">
                <img
                  src="https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=1000&q=85"
                  alt="Lumina Beauty Skincare Collection"
                  className="w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-stone-900/50 via-transparent to-transparent flex items-end p-8">
                  <div className="bg-white/95 backdrop-blur-md p-5 rounded-2xl w-full shadow-lg">
                    <p className="text-xs font-bold uppercase tracking-wider text-rose-500">
                      Best Seller Tuần Này
                    </p>
                    <p className="font-serif font-bold text-stone-800 text-base mt-0.5">
                      Serum Hyaluronic Acid 2% + B5 Cấp Ẩm Đa Tầng
                    </p>
                    <p className="text-xs text-stone-500 mt-1">
                      Phục hồi màng ẩm tự nhiên chỉ sau 7 ngày
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* 2. FLASH SALE & SĂN VOUCHER KHUYẾN MÃI */}
      <FlashSaleSection coupons={activeCoupons} />

      {/* 3. DANH MỤC NỔI BẬT */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex flex-col sm:flex-row sm:items-end justify-between mb-10">
          <div>
            <span className="text-xs font-bold uppercase tracking-[0.2em] text-rose-500">
              Danh Mục Làm Đẹp
            </span>
            <h2 className="font-serif text-3xl font-bold text-stone-800 mt-1">
              Khám Phá Theo Danh Mục
            </h2>
          </div>
          <Link
            href="/products"
            className="text-sm font-semibold text-rose-600 hover:text-rose-700 inline-flex items-center gap-1 mt-2 sm:mt-0"
          >
            Xem tất cả danh mục <ArrowRight className="w-4 h-4" />
          </Link>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {categories.map((cat) => (
            <Link
              key={cat.id}
              href={`/products?category=${cat.slug}`}
              className="group relative h-72 rounded-3xl overflow-hidden shadow-sm border border-rose-100"
            >
              <img
                src={cat.image}
                alt={cat.name}
                className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-stone-950/75 via-stone-900/20 to-transparent flex flex-col justify-end p-6">
                <h3 className="font-serif text-2xl font-bold text-white group-hover:text-rose-200 transition">
                  {cat.name}
                </h3>
                <span className="inline-flex items-center gap-1 text-xs font-medium text-rose-200 mt-1">
                  Khám phá ngay <ArrowRight className="w-3.5 h-3.5" />
                </span>
              </div>
            </Link>
          ))}
        </div>
      </section>

      {/* 4. SẢN PHẨM BÁN CHẠY (BEST SELLERS) */}
      <section className="bg-gradient-to-b from-rose-50/60 to-[#FDFBF7] py-16 border-y border-rose-100">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col sm:flex-row sm:items-end justify-between mb-10">
            <div>
              <span className="text-xs font-bold uppercase tracking-[0.2em] text-rose-500">
                Được Yêu Thích Nhất
              </span>
              <h2 className="font-serif text-3xl font-bold text-stone-800 mt-1">
                Sản Phẩm Bán Chạy (Best Sellers)
              </h2>
            </div>
            <Link
              href="/products"
              className="text-sm font-semibold text-rose-600 hover:text-rose-700 inline-flex items-center gap-1 mt-2 sm:mt-0"
            >
              Xem toàn bộ {allProducts.length} sản phẩm{" "}
              <ArrowRight className="w-4 h-4" />
            </Link>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">
            {bestSellers.map((product) => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        </div>
      </section>

      {/* 5. BỘ LỌC NHANH THEO LOẠI DA */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-2xl mx-auto mb-10">
          <span className="text-xs font-bold uppercase tracking-[0.2em] text-rose-500">
            Cá Nhân Hóa Chu Trình Dưỡng Da
          </span>
          <h2 className="font-serif text-3xl font-bold text-stone-800 mt-1">
            Chọn Mỹ Phẩm Theo Loại Da Của Bạn
          </h2>
          <p className="text-sm text-stone-500 mt-2">
            Mỗi làn da có một nhu cầu riêng biệt. Hãy chọn tình trạng da để
            Lumina Beauty gợi ý công thức hoạt chất phù hợp nhất.
          </p>
        </div>

        <SkinTypeExplorer products={allProducts} />
      </section>

      {/* 6. THƯƠNG HIỆU ĐỒNG HÀNH */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="bg-white rounded-3xl border border-rose-100 p-8 sm:p-10 shadow-xs">
          <div className="text-center mb-8">
            <span className="text-xs font-bold uppercase tracking-[0.2em] text-rose-500">
              Đối Tác Chính Hãng
            </span>
            <h3 className="font-serif text-2xl font-bold text-stone-800 mt-1">
              Thương Hiệu Mỹ Phẩm Hàng Đầu Thế Giới
            </h3>
          </div>
          <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
            {brands.map((brand) => (
              <Link
                key={brand.id}
                href={`/products?brand=${encodeURIComponent(brand.name)}`}
                className="flex flex-col items-center justify-center p-6 rounded-2xl bg-[#FDFBF7] hover:bg-rose-50/70 border border-rose-100/80 transition group"
              >
                <span className="font-serif text-xl font-bold text-stone-800 group-hover:text-rose-600">
                  {brand.name}
                </span>
                <span className="text-xs text-stone-400 mt-1">
                  Xuất xứ: {brand.origin}
                </span>
              </Link>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}
