"use client";

import React, { useState } from "react";
import Link from "next/link";
import { usePathname } from "next/navigation";
import {
  ShoppingBag,
  Sparkles,
  Search,
  Menu,
  X,
  WandSparkles,
} from "lucide-react";
import { useCart } from "@/context/CartContext";
import { openSkinQuiz } from "./SkinQuizModal";

export default function Navbar() {
  const { totalItems, setIsCartOpen } = useCart();
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const pathname = usePathname();

  const navLinks = [
    { href: "/", label: "Trang chủ" },
    { href: "/products", label: "Tất cả sản phẩm" },
    { href: "/products?category=cham-soc-da", label: "Chăm sóc da" },
    { href: "/products?category=trang-diem", label: "Trang điểm" },
    { href: "/products?category=nuoc-hoa", label: "Nước hoa" },
    { href: "/admin", label: "Quản trị" },
  ];

  return (
    <header className="sticky top-0 z-40 bg-[#FDFBF7]/95 backdrop-blur-md border-b border-rose-100">
      {/* Top announcement bar */}
      <div className="bg-gradient-to-r from-rose-200/80 via-rose-100 to-rose-200/80 text-rose-900 text-xs py-2 px-4 text-center font-medium tracking-wide">
        ✨ Miễn phí vận chuyển toàn quốc cho đơn hàng từ 500.000₫ — Cam kết 100% Mỹ phẩm Chính hãng ✨
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-20">
          {/* Brand Logo */}
          <Link href="/" className="flex items-center gap-2.5 group">
            <div className="w-10 h-10 rounded-full bg-gradient-to-tr from-rose-400 to-rose-200 flex items-center justify-center text-white shadow-sm group-hover:scale-105 transition">
              <Sparkles className="w-5 h-5" />
            </div>
            <div>
              <span className="font-serif text-2xl font-bold tracking-tight text-stone-800">
                Lumina <span className="text-rose-500 font-normal italic">Beauty</span>
              </span>
              <p className="text-[10px] uppercase tracking-[0.2em] text-stone-400 -mt-1">
                Luxury Skincare & Cosmetics
              </p>
            </div>
          </Link>

          {/* Desktop Navigation */}
          <nav className="hidden lg:flex items-center gap-6">
            {navLinks.map((link) => {
              const isActive = pathname === link.href;
              return (
                <Link
                  key={link.href}
                  href={link.href}
                  className={`text-sm font-medium transition-colors hover:text-rose-500 ${
                    isActive
                      ? "text-rose-600 font-semibold"
                      : "text-stone-700"
                  }`}
                >
                  {link.label}
                </Link>
              );
            })}
          </nav>

          {/* Actions */}
          <div className="flex items-center gap-2.5">
            <button
              type="button"
              onClick={openSkinQuiz}
              data-testid="navbar-skin-quiz-btn"
              className="hidden sm:inline-flex items-center gap-1.5 bg-gradient-to-r from-rose-500 to-rose-400 hover:from-rose-600 hover:to-rose-500 text-white px-4 py-2 rounded-full font-semibold text-xs transition shadow-sm"
            >
              <WandSparkles className="w-3.5 h-3.5" />
              <span>Trắc nghiệm da 30s</span>
            </button>

            <Link
              href="/products"
              className="p-2.5 rounded-full text-stone-600 hover:text-rose-600 hover:bg-rose-50 transition"
              title="Tìm kiếm mỹ phẩm"
            >
              <Search className="w-5 h-5" />
            </Link>

            <button
              onClick={() => setIsCartOpen(true)}
              className="relative flex items-center gap-2 bg-rose-50 hover:bg-rose-100 text-rose-700 px-4 py-2.5 rounded-full font-medium text-sm transition shadow-sm border border-rose-200/60"
              aria-label="Giỏ hàng"
            >
              <ShoppingBag className="w-4 h-4" />
              <span className="hidden sm:inline">Giỏ hàng</span>
              <span className="bg-rose-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
                {totalItems}
              </span>
            </button>

            <button
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              className="lg:hidden p-2 rounded-lg text-stone-700 hover:bg-rose-50"
              aria-label="Menu"
            >
              {mobileMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
            </button>
          </div>
        </div>
      </div>

      {/* Mobile Navigation */}
      {mobileMenuOpen && (
        <div className="lg:hidden bg-[#FDFBF7] border-b border-rose-100 px-4 pt-2 pb-5 space-y-2">
          {navLinks.map((link) => (
            <Link
              key={link.href}
              href={link.href}
              onClick={() => setMobileMenuOpen(false)}
              className="block py-2.5 px-3 rounded-xl text-stone-700 hover:bg-rose-50 hover:text-rose-600 font-medium text-sm"
            >
              {link.label}
            </Link>
          ))}
          <button
            type="button"
            onClick={() => {
              setMobileMenuOpen(false);
              openSkinQuiz();
            }}
            className="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-rose-500 text-white font-semibold text-sm"
          >
            <WandSparkles className="w-4 h-4" />
            <span>AI Trắc nghiệm loại da trong 30 giây</span>
          </button>
        </div>
      )}
    </header>
  );
}
