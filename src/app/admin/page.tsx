"use client";

import React, { useEffect, useState } from "react";
import { formatVND } from "@/lib/prisma";
import {
  PackagePlus,
  Trash2,
  ClipboardList,
  Sparkles,
  RefreshCw,
  TicketPercent,
} from "lucide-react";

interface OrderItemData {
  id: string;
  quantity: number;
  price: number;
  product?: {
    name: string;
    volume: string;
  };
}

interface OrderData {
  id: string;
  customerName: string;
  phone: string;
  address: string;
  note?: string;
  paymentMethod: string;
  couponCode?: string | null;
  discountAmount?: number;
  totalAmount: number;
  status: string;
  createdAt: string;
  items: OrderItemData[];
}

interface ProductData {
  id: string;
  name: string;
  slug: string;
  price: number;
  salePrice: number | null;
  stock: number;
  skinType: string;
  volume: string;
  images: string[];
  category?: { name: string };
  brand?: { name: string };
}

interface CategoryData {
  id: string;
  name: string;
}

interface BrandData {
  id: string;
  name: string;
  origin: string;
}

interface CouponData {
  id: string;
  code: string;
  discountPercent: number;
  description?: string | null;
  minOrderAmount: number;
  expiresAt: string;
  isActive: boolean;
}

export default function AdminDashboardPage() {
  const [orders, setOrders] = useState<OrderData[]>([]);
  const [products, setProducts] = useState<ProductData[]>([]);
  const [categories, setCategories] = useState<CategoryData[]>([]);
  const [brands, setBrands] = useState<BrandData[]>([]);
  const [coupons, setCoupons] = useState<CouponData[]>([]);
  const [loading, setLoading] = useState(true);

  // New Product Form State
  const [name, setName] = useState("");
  const [price, setPrice] = useState("450000");
  const [salePrice, setSalePrice] = useState("380000");
  const [volume, setVolume] = useState("30ml");
  const [skinType, setSkinType] = useState("Da dầu");
  const [categoryId, setCategoryId] = useState("");
  const [brandId, setBrandId] = useState("");
  const [image, setImage] = useState(
    "https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80"
  );
  const [ingredients, setIngredients] = useState(
    "Aqua, Niacinamide 10%, Hyaluronic Acid, Panthenol (Vitamin B5), Zinc PCA 1%."
  );
  const [description, setDescription] = useState(
    "Tinh chất dưỡng sáng và phục hồi màng bảo vệ da chuyên sâu chính hãng."
  );
  const [submitting, setSubmitting] = useState(false);

  // New Coupon Form State
  const [couponCode, setCouponCode] = useState("");
  const [discountPercent, setDiscountPercent] = useState("25");
  const [minOrderAmount, setMinOrderAmount] = useState("200000");
  const [expiresAt, setExpiresAt] = useState("2027-12-31");
  const [couponDesc, setCouponDesc] = useState(
    "Ưu đãi Flash Sale giảm 25% cho đơn hàng mỹ phẩm"
  );
  const [submittingCoupon, setSubmittingCoupon] = useState(false);

  const fetchData = async () => {
    setLoading(true);
    try {
      const [ordersRes, prodRes, couponsRes] = await Promise.all([
        fetch("/api/orders"),
        fetch("/api/products"),
        fetch("/api/coupons"),
      ]);
      const ordersData = await ordersRes.json();
      const prodData = await prodRes.json();
      const couponsData = await couponsRes.json();

      if (Array.isArray(ordersData)) setOrders(ordersData);
      if (Array.isArray(couponsData)) setCoupons(couponsData);
      if (prodData.products) {
        setProducts(prodData.products);
        setCategories(prodData.categories || []);
        setBrands(prodData.brands || []);
        if (prodData.categories?.[0] && !categoryId) {
          setCategoryId(prodData.categories[0].id);
        }
        if (prodData.brands?.[0] && !brandId) {
          setBrandId(prodData.brands[0].id);
        }
      }
    } catch (error) {
      console.error("Failed to load admin data:", error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  const handleAddProduct = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!name || !categoryId || !brandId) return;
    setSubmitting(true);
    try {
      const res = await fetch("/api/products", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          name,
          price: Number(price),
          salePrice: salePrice ? Number(salePrice) : null,
          volume,
          skinType,
          categoryId,
          brandId,
          image,
          ingredients,
          description,
          isBestSeller: true,
        }),
      });
      if (res.ok) {
        const created = await res.json();
        setProducts([created, ...products]);
        setName("");
      }
    } finally {
      setSubmitting(false);
    }
  };

  const handleAddCoupon = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!couponCode || !discountPercent || !expiresAt) return;
    setSubmittingCoupon(true);
    try {
      const res = await fetch("/api/coupons", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          code: couponCode.toUpperCase(),
          discountPercent: Number(discountPercent),
          minOrderAmount: Number(minOrderAmount || 0),
          expiresAt,
          description: couponDesc,
        }),
      });
      if (res.ok) {
        const created = await res.json();
        setCoupons([created, ...coupons]);
        setCouponCode("");
      }
    } finally {
      setSubmittingCoupon(false);
    }
  };

  const handleDeleteProduct = async (id: string) => {
    const res = await fetch(`/api/products/${id}`, { method: "DELETE" });
    if (res.ok) {
      setProducts(products.filter((p) => p.id !== id));
    }
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-8 rounded-3xl border border-rose-100 shadow-xs">
        <div>
          <span className="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-rose-500">
            <Sparkles className="w-3.5 h-3.5" /> Lumina Beauty Admin Portal
          </span>
          <h1 className="font-serif text-3xl font-bold text-stone-800 mt-1">
            Quản Trị Đơn Hàng, Voucher & Mỹ Phẩm
          </h1>
          <p className="text-sm text-stone-500 mt-1">
            Đồng bộ trực tiếp với cơ sở dữ liệu Docker PostgreSQL (shop_db)
          </p>
        </div>
        <button
          onClick={fetchData}
          className="inline-flex items-center gap-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-sm font-semibold px-5 py-2.5 rounded-full border border-rose-200 transition w-fit"
        >
          <RefreshCw className="w-4 h-4" /> Làm mới dữ liệu
        </button>
      </div>

      {/* 1. DANH SÁCH ĐƠN HÀNG VỪA ĐẶT */}
      <section className="bg-white p-6 sm:p-8 rounded-3xl border border-rose-100 shadow-xs space-y-6">
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-2.5">
            <ClipboardList className="w-5 h-5 text-rose-500" />
            <h2 className="font-serif text-2xl font-bold text-stone-800">
              Danh Sách Đơn Hàng ({orders.length})
            </h2>
          </div>
        </div>

        {loading ? (
          <p className="text-sm text-stone-500 py-6">Đang tải đơn hàng...</p>
        ) : orders.length === 0 ? (
          <div className="p-8 rounded-2xl bg-[#FDFBF7] border border-rose-100 text-center text-sm text-stone-500">
            Chưa có đơn hàng nào. Hãy thử đặt hàng tại trang{" "}
            <a href="/checkout" className="text-rose-600 font-semibold underline">
              /checkout
            </a>
            !
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse text-sm">
              <thead>
                <tr className="border-b border-rose-100 text-xs uppercase tracking-wider text-stone-400">
                  <th className="py-3.5 px-4">Mã Đơn</th>
                  <th className="py-3.5 px-4">Khách Hàng</th>
                  <th className="py-3.5 px-4">Địa Chỉ Giao Hàng</th>
                  <th className="py-3.5 px-4">Sản Phẩm Đặt</th>
                  <th className="py-3.5 px-4">Voucher</th>
                  <th className="py-3.5 px-4">Tổng Tiền</th>
                  <th className="py-3.5 px-4">Trạng Thái</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-rose-50">
                {orders.map((order) => (
                  <tr key={order.id} className="hover:bg-rose-50/30">
                    <td className="py-4 px-4 font-mono font-bold text-xs text-stone-700">
                      #{order.id.slice(-6).toUpperCase()}
                    </td>
                    <td className="py-4 px-4">
                      <p className="font-semibold text-stone-800">
                        {order.customerName}
                      </p>
                      <p className="text-xs text-stone-500">{order.phone}</p>
                    </td>
                    <td className="py-4 px-4 text-xs text-stone-600 max-w-xs">
                      {order.address}
                    </td>
                    <td className="py-4 px-4 text-xs text-stone-600">
                      <ul className="space-y-1">
                        {order.items.map((item) => (
                          <li key={item.id}>
                            • {item.product?.name || "Sản phẩm"} (x
                            {item.quantity})
                          </li>
                        ))}
                      </ul>
                    </td>
                    <td className="py-4 px-4 text-xs">
                      {order.couponCode ? (
                        <span className="inline-flex flex-col bg-rose-50 text-rose-700 px-2.5 py-1 rounded-lg font-mono font-bold border border-rose-200">
                          <span>{order.couponCode}</span>
                          <span className="text-[10px] text-emerald-600">
                            -{formatVND(order.discountAmount || 0)}
                          </span>
                        </span>
                      ) : (
                        <span className="text-stone-400">—</span>
                      )}
                    </td>
                    <td className="py-4 px-4 font-bold text-rose-600">
                      {formatVND(order.totalAmount)}
                    </td>
                    <td className="py-4 px-4">
                      <span className="bg-emerald-50 text-emerald-700 text-xs font-semibold px-3 py-1 rounded-full border border-emerald-200">
                        {order.status}
                      </span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </section>

      {/* 2. QUẢN LÝ MÃ GIẢM GIÁ (COUPON / VOUCHER) */}
      <section className="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <form
          onSubmit={handleAddCoupon}
          className="lg:col-span-5 bg-white p-6 sm:p-8 rounded-3xl border border-rose-100 shadow-xs space-y-4 h-fit"
        >
          <div className="flex items-center gap-2 pb-3 border-b border-rose-100">
            <TicketPercent className="w-5 h-5 text-rose-500" />
            <h2 className="font-serif text-xl font-bold text-stone-800">
              Tạo Mã Giảm Giá (Coupon) Mới
            </h2>
          </div>

          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-xs font-semibold text-stone-600 mb-1">
                Mã Code *
              </label>
              <input
                type="text"
                required
                value={couponCode}
                onChange={(e) => setCouponCode(e.target.value.toUpperCase())}
                placeholder="VD: GLOW25"
                className="w-full px-3.5 py-2.5 text-sm font-mono uppercase rounded-xl border border-rose-200 bg-[#FDFBF7]"
              />
            </div>
            <div>
              <label className="block text-xs font-semibold text-stone-600 mb-1">
                Phần trăm giảm (%) *
              </label>
              <input
                type="number"
                min={1}
                max={100}
                required
                value={discountPercent}
                onChange={(e) => setDiscountPercent(e.target.value)}
                className="w-full px-3.5 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]"
              />
            </div>
          </div>

          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-xs font-semibold text-stone-600 mb-1">
                Đơn tối thiểu (VNĐ)
              </label>
              <input
                type="number"
                value={minOrderAmount}
                onChange={(e) => setMinOrderAmount(e.target.value)}
                className="w-full px-3.5 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]"
              />
            </div>
            <div>
              <label className="block text-xs font-semibold text-stone-600 mb-1">
                Hạn sử dụng *
              </label>
              <input
                type="date"
                required
                value={expiresAt}
                onChange={(e) => setExpiresAt(e.target.value)}
                className="w-full px-3.5 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]"
              />
            </div>
          </div>

          <div>
            <label className="block text-xs font-semibold text-stone-600 mb-1">
              Mô tả ưu đãi
            </label>
            <input
              type="text"
              value={couponDesc}
              onChange={(e) => setCouponDesc(e.target.value)}
              className="w-full px-3.5 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]"
            />
          </div>

          <button
            type="submit"
            disabled={submittingCoupon}
            className="w-full bg-rose-500 hover:bg-rose-600 text-white font-semibold py-3 px-6 rounded-full shadow-md shadow-rose-200 transition text-sm"
          >
            {submittingCoupon
              ? "Đang lưu..."
              : "+ Thêm Mã Giảm Giá Vào PostgreSQL"}
          </button>
        </form>

        <div className="lg:col-span-7 bg-white p-6 sm:p-8 rounded-3xl border border-rose-100 shadow-xs space-y-4">
          <h2 className="font-serif text-xl font-bold text-stone-800 pb-3 border-b border-rose-100">
            Danh Sách Mã Giảm Giá Trong Database ({coupons.length})
          </h2>

          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse text-sm">
              <thead>
                <tr className="border-b border-rose-100 text-xs uppercase text-stone-400">
                  <th className="py-3 px-3">Mã Code</th>
                  <th className="py-3 px-3">Mức Giảm</th>
                  <th className="py-3 px-3">Đơn Tối Thiểu</th>
                  <th className="py-3 px-3">Hạn Sử Dụng</th>
                  <th className="py-3 px-3">Trạng Thái</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-rose-50">
                {coupons.map((c) => {
                  const expired = new Date(c.expiresAt) < new Date();
                  return (
                    <tr key={c.id} className="hover:bg-rose-50/30">
                      <td className="py-3.5 px-3 font-mono font-bold text-rose-600">
                        {c.code}
                      </td>
                      <td className="py-3.5 px-3 font-bold text-stone-800">
                        -{c.discountPercent}%
                      </td>
                      <td className="py-3.5 px-3 text-xs text-stone-600">
                        {formatVND(c.minOrderAmount)}
                      </td>
                      <td className="py-3.5 px-3 text-xs text-stone-600">
                        {new Date(c.expiresAt).toLocaleDateString("vi-VN")}
                      </td>
                      <td className="py-3.5 px-3">
                        {expired || !c.isActive ? (
                          <span className="bg-stone-100 text-stone-500 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                            Hết hạn
                          </span>
                        ) : (
                          <span className="bg-emerald-50 text-emerald-700 text-xs font-semibold px-2.5 py-0.5 rounded-full border border-emerald-200">
                            Đang hoạt động
                          </span>
                        )}
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {/* 3. THÊM & QUẢN LÝ SẢN PHẨM MỸ PHẨM */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {/* Form thêm sản phẩm */}
        <form
          onSubmit={handleAddProduct}
          className="lg:col-span-5 bg-white p-6 sm:p-8 rounded-3xl border border-rose-100 shadow-xs space-y-4 h-fit"
        >
          <div className="flex items-center gap-2 pb-3 border-b border-rose-100">
            <PackagePlus className="w-5 h-5 text-rose-500" />
            <h2 className="font-serif text-xl font-bold text-stone-800">
              Thêm Sản Phẩm Mỹ Phẩm Mới
            </h2>
          </div>

          <div>
            <label className="block text-xs font-semibold text-stone-600 mb-1">
              Tên sản phẩm mỹ phẩm *
            </label>
            <input
              type="text"
              required
              value={name}
              onChange={(e) => setName(e.target.value)}
              placeholder="VD: Tinh Chất Phục Hồi Vitamin C 15%"
              className="w-full px-3.5 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]"
            />
          </div>

          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-xs font-semibold text-stone-600 mb-1">
                Giá gốc (VNĐ) *
              </label>
              <input
                type="number"
                required
                value={price}
                onChange={(e) => setPrice(e.target.value)}
                className="w-full px-3.5 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]"
              />
            </div>
            <div>
              <label className="block text-xs font-semibold text-stone-600 mb-1">
                Giá khuyến mãi (VNĐ)
              </label>
              <input
                type="number"
                value={salePrice}
                onChange={(e) => setSalePrice(e.target.value)}
                className="w-full px-3.5 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]"
              />
            </div>
          </div>

          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-xs font-semibold text-stone-600 mb-1">
                Danh mục
              </label>
              <select
                value={categoryId}
                onChange={(e) => setCategoryId(e.target.value)}
                className="w-full px-3 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]"
              >
                {categories.map((c) => (
                  <option key={c.id} value={c.id}>
                    {c.name}
                  </option>
                ))}
              </select>
            </div>
            <div>
              <label className="block text-xs font-semibold text-stone-600 mb-1">
                Thương hiệu
              </label>
              <select
                value={brandId}
                onChange={(e) => setBrandId(e.target.value)}
                className="w-full px-3 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]"
              >
                {brands.map((b) => (
                  <option key={b.id} value={b.id}>
                    {b.name} ({b.origin})
                  </option>
                ))}
              </select>
            </div>
          </div>

          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-xs font-semibold text-stone-600 mb-1">
                Loại da phù hợp
              </label>
              <select
                value={skinType}
                onChange={(e) => setSkinType(e.target.value)}
                className="w-full px-3 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]"
              >
                <option value="Da dầu">Da dầu</option>
                <option value="Da khô">Da khô</option>
                <option value="Da nhạy cảm">Da nhạy cảm</option>
                <option value="Mọi loại da">Mọi loại da</option>
              </select>
            </div>
            <div>
              <label className="block text-xs font-semibold text-stone-600 mb-1">
                Dung tích / Trọng lượng
              </label>
              <input
                type="text"
                value={volume}
                onChange={(e) => setVolume(e.target.value)}
                placeholder="30ml, 50g..."
                className="w-full px-3.5 py-2.5 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]"
              />
            </div>
          </div>

          <div>
            <label className="block text-xs font-semibold text-stone-600 mb-1">
              URL Ảnh sản phẩm (Unsplash)
            </label>
            <input
              type="url"
              value={image}
              onChange={(e) => setImage(e.target.value)}
              className="w-full px-3.5 py-2 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]"
            />
          </div>

          <div>
            <label className="block text-xs font-semibold text-stone-600 mb-1">
              Bảng thành phần (Ingredients)
            </label>
            <textarea
              rows={2}
              value={ingredients}
              onChange={(e) => setIngredients(e.target.value)}
              className="w-full px-3.5 py-2 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]"
            />
          </div>

          <div>
            <label className="block text-xs font-semibold text-stone-600 mb-1">
              Mô tả công dụng
            </label>
            <textarea
              rows={2}
              value={description}
              onChange={(e) => setDescription(e.target.value)}
              className="w-full px-3.5 py-2 text-sm rounded-xl border border-rose-200 bg-[#FDFBF7]"
            />
          </div>

          <button
            type="submit"
            disabled={submitting}
            className="w-full bg-rose-500 hover:bg-rose-600 text-white font-semibold py-3 px-6 rounded-full shadow-md shadow-rose-200 transition text-sm"
          >
            {submitting ? "Đang lưu..." : "+ Thêm Sản Phẩm Vào PostgreSQL"}
          </button>
        </form>

        {/* Bảng danh sách sản phẩm */}
        <div className="lg:col-span-7 bg-white p-6 sm:p-8 rounded-3xl border border-rose-100 shadow-xs space-y-4">
          <h2 className="font-serif text-xl font-bold text-stone-800 pb-3 border-b border-rose-100">
            Kho Sản Phẩm Mỹ Phẩm Hiện Có ({products.length})
          </h2>

          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse text-sm">
              <thead>
                <tr className="border-b border-rose-100 text-xs uppercase text-stone-400">
                  <th className="py-3 px-3">Sản phẩm</th>
                  <th className="py-3 px-3">Loại da / Dung tích</th>
                  <th className="py-3 px-3">Giá bán</th>
                  <th className="py-3 px-3 text-right">Thao tác</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-rose-50">
                {products.map((prod) => (
                  <tr key={prod.id} className="hover:bg-rose-50/30">
                    <td className="py-3.5 px-3 flex items-center gap-3">
                      <img
                        src={prod.images?.[0]}
                        alt={prod.name}
                        className="w-12 h-12 rounded-xl object-cover bg-rose-50 flex-shrink-0"
                      />
                      <div>
                        <p className="font-semibold text-stone-800 line-clamp-1">
                          {prod.name}
                        </p>
                        <p className="text-xs text-stone-400">
                          {prod.brand?.name} • {prod.category?.name}
                        </p>
                      </div>
                    </td>
                    <td className="py-3.5 px-3 text-xs text-stone-600">
                      <span className="bg-rose-50 text-rose-700 px-2.5 py-0.5 rounded-full font-medium">
                        {prod.skinType}
                      </span>
                      <span className="ml-1.5 text-stone-400">
                        ({prod.volume})
                      </span>
                    </td>
                    <td className="py-3.5 px-3 font-bold text-rose-600 text-sm">
                      {formatVND(prod.salePrice ?? prod.price)}
                    </td>
                    <td className="py-3.5 px-3 text-right">
                      <button
                        onClick={() => handleDeleteProduct(prod.id)}
                        className="p-2 text-stone-400 hover:text-red-600 hover:bg-red-50 rounded-full transition"
                        title="Xóa sản phẩm"
                      >
                        <Trash2 className="w-4 h-4" />
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
}
