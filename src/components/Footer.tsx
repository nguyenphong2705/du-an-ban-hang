import React from "react";
import Link from "next/link";
import { Sparkles, MapPin, Phone, Mail, ShieldCheck, Truck, RefreshCw } from "lucide-react";

export default function Footer() {
  return (
    <footer className="bg-[#FAF6EE] border-t border-rose-100 text-stone-700 mt-20">
      {/* Highlights */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 border-b border-rose-200/50">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div className="flex items-center gap-4 bg-white/80 p-5 rounded-2xl border border-rose-100">
            <div className="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
              <ShieldCheck className="w-6 h-6" />
            </div>
            <div>
              <h4 className="font-serif font-bold text-stone-800">100% Chính Hãng</h4>
              <p className="text-xs text-stone-500 mt-0.5">
                Nhập khẩu trực tiếp từ Pháp, Hàn Quốc, Nhật Bản có tem phụ tiếng Việt.
              </p>
            </div>
          </div>

          <div className="flex items-center gap-4 bg-white/80 p-5 rounded-2xl border border-rose-100">
            <div className="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
              <Truck className="w-6 h-6" />
            </div>
            <div>
              <h4 className="font-serif font-bold text-stone-800">Giao Nhanh 2H Nội Thành</h4>
              <p className="text-xs text-stone-500 mt-0.5">
                Đóng gói chống sốc cao cấp, miễn phí giao hàng đơn từ 500.000₫.
              </p>
            </div>
          </div>

          <div className="flex items-center gap-4 bg-white/80 p-5 rounded-2xl border border-rose-100">
            <div className="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
              <RefreshCw className="w-6 h-6" />
            </div>
            <div>
              <h4 className="font-serif font-bold text-stone-800">Đổi Trả Trong 14 Ngày</h4>
              <p className="text-xs text-stone-500 mt-0.5">
                Hỗ trợ đổi trả miễn phí nếu sản phẩm lỗi hoặc kích ứng da.
              </p>
            </div>
          </div>
        </div>
      </div>

      {/* Main Footer */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div className="space-y-4">
            <div className="flex items-center gap-2">
              <div className="w-8 h-8 rounded-full bg-rose-400 text-white flex items-center justify-center">
                <Sparkles className="w-4 h-4" />
              </div>
              <span className="font-serif text-xl font-bold text-stone-800">
                Lumina <span className="text-rose-500 italic">Beauty</span>
              </span>
            </div>
            <p className="text-sm text-stone-500 leading-relaxed">
              Điểm đến tin cậy của tín đồ làm đẹp Việt Nam. Chuyên phân phối dược mỹ phẩm, skincare & nước hoa cao cấp chính hãng.
            </p>
          </div>

          <div>
            <h4 className="font-serif font-bold text-stone-800 mb-4">Danh Mục Nổi Bật</h4>
            <ul className="space-y-2.5 text-sm text-stone-600">
              <li>
                <Link href="/products?category=cham-soc-da" className="hover:text-rose-600">
                  Chăm sóc da chuyên sâu
                </Link>
              </li>
              <li>
                <Link href="/products?category=trang-diem" className="hover:text-rose-600">
                  Trang điểm cao cấp
                </Link>
              </li>
              <li>
                <Link href="/products?category=cham-soc-co-the" className="hover:text-rose-600">
                  Chăm sóc cơ thể
                </Link>
              </li>
              <li>
                <Link href="/products?category=nuoc-hoa" className="hover:text-rose-600">
                  Nước hoa Pháp chính hãng
                </Link>
              </li>
            </ul>
          </div>

          <div>
            <h4 className="font-serif font-bold text-stone-800 mb-4">Theo Loại Da</h4>
            <ul className="space-y-2.5 text-sm text-stone-600">
              <li>
                <Link href="/products?skinType=Da+dầu" className="hover:text-rose-600">
                  Dành cho Da dầu & Mụn
                </Link>
              </li>
              <li>
                <Link href="/products?skinType=Da+khô" className="hover:text-rose-600">
                  Dành cho Da khô & Lão hóa
                </Link>
              </li>
              <li>
                <Link href="/products?skinType=Da+nhạy+cảm" className="hover:text-rose-600">
                  Dành cho Da nhạy cảm
                </Link>
              </li>
              <li>
                <Link href="/products?skinType=Mọi+loại+da" className="hover:text-rose-600">
                  Phù hợp Mọi loại da
                </Link>
              </li>
            </ul>
          </div>

          <div>
            <h4 className="font-serif font-bold text-stone-800 mb-4">Liên Hệ</h4>
            <ul className="space-y-3 text-sm text-stone-600">
              <li className="flex items-start gap-2.5">
                <MapPin className="w-4 h-4 text-rose-500 mt-1 flex-shrink-0" />
                <span>88 Đồng Khởi, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh</span>
              </li>
              <li className="flex items-center gap-2.5">
                <Phone className="w-4 h-4 text-rose-500 flex-shrink-0" />
                <span>1900 6886 (8:00 - 22:00)</span>
              </li>
              <li className="flex items-center gap-2.5">
                <Mail className="w-4 h-4 text-rose-500 flex-shrink-0" />
                <span>cskh@luminabeauty.vn</span>
              </li>
            </ul>
          </div>
        </div>

        <div className="mt-10 pt-6 border-t border-rose-200/60 text-center text-xs text-stone-400">
          © {new Date().getFullYear()} Lumina Beauty Cosmetics. All rights reserved.
        </div>
      </div>
    </footer>
  );
}
