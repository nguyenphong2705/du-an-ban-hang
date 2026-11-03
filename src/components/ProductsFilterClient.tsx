"use client";

import React, { useState, useMemo, useEffect } from "react";
import { useSearchParams } from "next/navigation";
import ProductCard, { ProductCardProps } from "./ProductCard";
import { Search, SlidersHorizontal, RotateCcw } from "lucide-react";

interface CategoryItem {
  id: string;
  name: string;
  slug: string;
}

interface BrandItem {
  id: string;
  name: string;
  origin: string;
}

interface Props {
  products: (ProductCardProps & {
    categoryId: string;
    brandId: string;
    category: CategoryItem;
    brand: BrandItem;
  })[];
  categories: CategoryItem[];
  brands: BrandItem[];
}

const SKIN_TYPES = ["Tất cả", "Da dầu", "Da khô", "Da nhạy cảm", "Mọi loại da"];

const PRICE_RANGES = [
  { key: "ALL", label: "Tất cả mức giá" },
  { key: "UNDER_500", label: "Dưới 500.000₫" },
  { key: "500_TO_1000", label: "Từ 500.000₫ - 1.000.000₫" },
  { key: "ABOVE_1000", label: "Trên 1.000.000₫" },
];

export default function ProductsFilterClient({
  products,
  categories,
  brands,
}: Props) {
  const searchParams = useSearchParams();
  const initialCategory = searchParams.get("category") || "ALL";
  const initialBrand = searchParams.get("brand") || "ALL";
  const initialSkinType = searchParams.get("skinType") || "Tất cả";

  const [searchQuery, setSearchQuery] = useState("");
  const [selectedCategory, setSelectedCategory] = useState(initialCategory);
  const [selectedBrand, setSelectedBrand] = useState(initialBrand);
  const [selectedSkinType, setSelectedSkinType] = useState(initialSkinType);
  const [selectedPriceRange, setSelectedPriceRange] = useState("ALL");

  useEffect(() => {
    setSelectedCategory(searchParams.get("category") || "ALL");
    setSelectedBrand(searchParams.get("brand") || "ALL");
    setSelectedSkinType(searchParams.get("skinType") || "Tất cả");
  }, [searchParams]);

  const filteredProducts = useMemo(() => {
    return products.filter((item) => {
      const effectivePrice = item.salePrice ?? item.price;

      // Search by name
      if (
        searchQuery.trim() !== "" &&
        !item.name.toLowerCase().includes(searchQuery.toLowerCase())
      ) {
        return false;
      }

      // Filter by category slug or id
      if (
        selectedCategory !== "ALL" &&
        item.category.slug !== selectedCategory &&
        item.categoryId !== selectedCategory
      ) {
        return false;
      }

      // Filter by brand name or id
      if (
        selectedBrand !== "ALL" &&
        item.brand.name !== selectedBrand &&
        item.brandId !== selectedBrand
      ) {
        return false;
      }

      // Filter by skin type
      if (
        selectedSkinType !== "Tất cả" &&
        item.skinType.toLowerCase() !== selectedSkinType.toLowerCase()
      ) {
        return false;
      }

      // Filter by price range
      if (selectedPriceRange === "UNDER_500" && effectivePrice >= 500000) {
        return false;
      }
      if (
        selectedPriceRange === "500_TO_1000" &&
        (effectivePrice < 500000 || effectivePrice > 1000000)
      ) {
        return false;
      }
      if (selectedPriceRange === "ABOVE_1000" && effectivePrice <= 1000000) {
        return false;
      }

      return true;
    });
  }, [
    products,
    searchQuery,
    selectedCategory,
    selectedBrand,
    selectedSkinType,
    selectedPriceRange,
  ]);

  const resetFilters = () => {
    setSearchQuery("");
    setSelectedCategory("ALL");
    setSelectedBrand("ALL");
    setSelectedSkinType("Tất cả");
    setSelectedPriceRange("ALL");
  };

  return (
    <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
      {/* Sidebar Filters */}
      <aside className="lg:col-span-3 space-y-6">
        <div className="bg-white p-6 rounded-3xl border border-rose-100 shadow-xs space-y-6 sticky top-28">
          <div className="flex items-center justify-between pb-4 border-b border-rose-100">
            <div className="flex items-center gap-2 font-serif font-bold text-stone-800">
              <SlidersHorizontal className="w-4 h-4 text-rose-500" />
              <span>Bộ Lọc Tìm Kiếm</span>
            </div>
            <button
              onClick={resetFilters}
              className="text-xs text-rose-600 hover:text-rose-700 inline-flex items-center gap-1 font-medium"
            >
              <RotateCcw className="w-3 h-3" /> Đặt lại
            </button>
          </div>

          {/* Search input */}
          <div>
            <label className="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2">
              Tìm theo tên sản phẩm
            </label>
            <div className="relative">
              <Search className="w-4 h-4 text-stone-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="VD: Niacinamide, BHA, Son..."
                className="w-full pl-10 pr-4 py-2.5 text-sm rounded-2xl border border-rose-200 bg-[#FDFBF7] focus:outline-none focus:ring-2 focus:ring-rose-300"
              />
            </div>
          </div>

          {/* Category filter */}
          <div>
            <label className="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2.5">
              Danh mục mỹ phẩm
            </label>
            <div className="space-y-1.5">
              <button
                onClick={() => setSelectedCategory("ALL")}
                className={`w-full text-left px-3.5 py-2 rounded-xl text-sm transition ${
                  selectedCategory === "ALL"
                    ? "bg-rose-500 text-white font-semibold"
                    : "text-stone-600 hover:bg-rose-50"
                }`}
              >
                Tất cả danh mục
              </button>
              {categories.map((cat) => (
                <button
                  key={cat.id}
                  onClick={() => setSelectedCategory(cat.slug)}
                  className={`w-full text-left px-3.5 py-2 rounded-xl text-sm transition ${
                    selectedCategory === cat.slug
                      ? "bg-rose-500 text-white font-semibold"
                      : "text-stone-600 hover:bg-rose-50"
                  }`}
                >
                  {cat.name}
                </button>
              ))}
            </div>
          </div>

          {/* Brand filter */}
          <div>
            <label className="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2.5">
              Thương hiệu
            </label>
            <select
              value={selectedBrand}
              onChange={(e) => setSelectedBrand(e.target.value)}
              className="w-full px-3.5 py-2.5 text-sm rounded-2xl border border-rose-200 bg-[#FDFBF7] text-stone-700 focus:outline-none focus:ring-2 focus:ring-rose-300"
            >
              <option value="ALL">Tất cả thương hiệu</option>
              {brands.map((b) => (
                <option key={b.id} value={b.name}>
                  {b.name} ({b.origin})
                </option>
              ))}
            </select>
          </div>

          {/* Skin Type filter */}
          <div>
            <label className="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2.5">
              Loại da phù hợp
            </label>
            <div className="flex flex-wrap gap-2">
              {SKIN_TYPES.map((skin) => (
                <button
                  key={skin}
                  onClick={() => setSelectedSkinType(skin)}
                  className={`px-3 py-1.5 rounded-full text-xs font-medium border transition ${
                    selectedSkinType === skin
                      ? "bg-rose-500 text-white border-rose-500"
                      : "bg-[#FDFBF7] text-stone-600 border-rose-200 hover:bg-rose-50"
                  }`}
                >
                  {skin}
                </button>
              ))}
            </div>
          </div>

          {/* Price Range filter */}
          <div>
            <label className="block text-xs font-semibold uppercase tracking-wider text-stone-500 mb-2.5">
              Khoảng giá
            </label>
            <div className="space-y-1.5">
              {PRICE_RANGES.map((range) => (
                <button
                  key={range.key}
                  onClick={() => setSelectedPriceRange(range.key)}
                  className={`w-full text-left px-3.5 py-2 rounded-xl text-sm transition ${
                    selectedPriceRange === range.key
                      ? "bg-rose-500 text-white font-semibold"
                      : "text-stone-600 hover:bg-rose-50"
                  }`}
                >
                  {range.label}
                </button>
              ))}
            </div>
          </div>
        </div>
      </aside>

      {/* Products Grid */}
      <div className="lg:col-span-9 space-y-6">
        <div className="bg-white px-6 py-4 rounded-2xl border border-rose-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
          <p className="text-sm text-stone-600">
            Hiển thị{" "}
            <span className="font-bold text-rose-600">
              {filteredProducts.length}
            </span>{" "}
            sản phẩm phù hợp
          </p>
          {selectedSkinType !== "Tất cả" && (
            <span className="text-xs bg-rose-50 text-rose-700 px-3 py-1 rounded-full font-medium">
              Loại da: {selectedSkinType}
            </span>
          )}
        </div>

        {filteredProducts.length === 0 ? (
          <div className="bg-white rounded-3xl border border-rose-100 p-16 text-center space-y-4">
            <p className="font-serif text-xl font-bold text-stone-700">
              Không tìm thấy sản phẩm phù hợp với bộ lọc
            </p>
            <p className="text-sm text-stone-500">
              Hãy thử thay đổi từ khóa tìm kiếm hoặc đặt lại bộ lọc để xem toàn bộ mỹ phẩm.
            </p>
            <button
              onClick={resetFilters}
              className="inline-flex items-center gap-2 bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold px-6 py-2.5 rounded-full transition"
            >
              Xem tất cả sản phẩm
            </button>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
            {filteredProducts.map((product) => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
