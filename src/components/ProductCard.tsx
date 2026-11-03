"use client";

import React from "react";
import Link from "next/link";
import { ShoppingBag, Sparkles, Star } from "lucide-react";
import { useCart } from "@/context/CartContext";
import { formatVND } from "@/lib/prisma";

export interface ProductCardProps {
  id: string;
  name: string;
  slug: string;
  price: number;
  salePrice?: number | null;
  images: string[];
  skinType: string;
  volume: string;
  isBestSeller: boolean;
  brand?: {
    name: string;
    origin: string;
  };
  category?: {
    name: string;
  };
}

export default function ProductCard({ product }: { product: ProductCardProps }) {
  const { addToCart } = useCart();
  const finalPrice = product.salePrice ?? product.price;
  const discountPercent =
    product.salePrice && product.salePrice < product.price
      ? Math.round(((product.price - product.salePrice) / product.price) * 100)
      : null;

  const handleAdd = (e: React.MouseEvent) => {
    e.preventDefault();
    addToCart({
      id: product.id,
      name: product.name,
      slug: product.slug,
      price: finalPrice,
      image:
        product.images?.[0] ||
        "https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=800&q=80",
      volume: product.volume,
      skinType: product.skinType,
      brandName: product.brand?.name,
    });
  };

  return (
    <div className="group bg-white rounded-3xl border border-rose-100/90 overflow-hidden shadow-xs hover:shadow-xl hover:shadow-rose-100/60 transition-all duration-300 flex flex-col justify-between">
      <div>
        {/* Image Wrapper */}
        <Link
          href={`/products/${product.slug}`}
          className="block relative aspect-4/4 overflow-hidden bg-rose-50/50"
        >
          <img
            src={
              product.images?.[0] ||
              "https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=800&q=80"
            }
            alt={product.name}
            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
          />

          {/* Top Left Badges */}
          <div className="absolute top-3 left-3 flex flex-col gap-1.5">
            {product.isBestSeller && (
              <span className="inline-flex items-center gap-1 bg-rose-500 text-white text-[11px] font-semibold px-2.5 py-1 rounded-full shadow-xs">
                <Sparkles className="w-3 h-3" /> Best Seller
              </span>
            )}
            {discountPercent && (
              <span className="inline-block bg-stone-900/80 backdrop-blur-xs text-white text-[11px] font-bold px-2.5 py-0.5 rounded-full w-fit">
                -{discountPercent}%
              </span>
            )}
          </div>

          {/* Skin Type Pill */}
          <span className="absolute bottom-3 left-3 bg-white/90 backdrop-blur-xs text-rose-700 text-[11px] font-medium px-3 py-1 rounded-full border border-rose-100 shadow-2xs">
            {product.skinType}
          </span>
        </Link>

        {/* Content */}
        <div className="p-5">
          <div className="flex items-center justify-between text-xs text-stone-400 mb-1.5">
            <span className="font-semibold uppercase tracking-wider text-rose-500">
              {product.brand?.name || "Lumina"}
            </span>
            <span>{product.volume}</span>
          </div>

          <Link
            href={`/products/${product.slug}`}
            className="font-serif font-bold text-stone-800 group-hover:text-rose-600 transition line-clamp-2 text-base leading-snug min-h-[2.75rem]"
          >
            {product.name}
          </Link>

          <div className="flex items-center gap-1 mt-2 text-amber-400">
            {[...Array(5)].map((_, i) => (
              <Star key={i} className="w-3.5 h-3.5 fill-amber-400" />
            ))}
            <span className="text-xs text-stone-400 ml-1">(4.9)</span>
          </div>
        </div>
      </div>

      {/* Price & Add to Cart */}
      <div className="px-5 pb-5 pt-2 flex items-center justify-between border-t border-rose-50">
        <div>
          <div className="text-lg font-bold text-rose-600">
            {formatVND(finalPrice)}
          </div>
          {product.salePrice && product.salePrice < product.price && (
            <div className="text-xs text-stone-400 line-through">
              {formatVND(product.price)}
            </div>
          )}
        </div>

        <button
          onClick={handleAdd}
          className="inline-flex items-center gap-1.5 bg-rose-50 hover:bg-rose-500 text-rose-700 hover:text-white font-medium text-xs px-3.5 py-2.5 rounded-full transition-colors duration-200 border border-rose-200/70"
        >
          <ShoppingBag className="w-3.5 h-3.5" />
          <span>Thêm giỏ</span>
        </button>
      </div>
    </div>
  );
}
