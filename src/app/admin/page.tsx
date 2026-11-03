"use client";

import React, { useEffect, useState, useMemo } from "react";
import { formatVND } from "@/lib/prisma";
import {
  PackagePlus,
  Trash2,
  ClipboardList,
  Sparkles,
  RefreshCw,
  TicketPercent,
  TrendingUp,
  ShoppingBag,
  Power,
  CheckCircle2,
  Star,
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
  isBestSeller: boolean;
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

const ORDER_STATUS_OPTIONS = [
  { value: "PENDING", label: "Chờ xác nhận", color: "bg-amber-50 text-amber-700 border-amber-200" },
  { value: "CONFIRMED", label: "Đã xác nhận", color: "bg-blue-50 text-blue-700 border-blue-200" },
  { value: "SHIPPING", label: "Đang giao hàng", color: "bg-purple-50 text-purple-700 border-purple-200" },
  { value: "COMPLETED", label: "Hoàn thành", color: "bg-emerald-50 text-emerald-700 border-emerald-200" },
  { value: "CANCELLED", label: "Đã hủy", color: "bg-red-50 text-red-700 border-red-200" },
];

export default function AdminDashboardPage() {
  const [orders, setOrders] = useState<OrderData[]>([]);
  const [products, setProducts] = useState<ProductData[]>([]);
  const [categories, setCategories] = useState<CategoryData[]>([]);
  const [brands, setBrands] = useState<BrandData[]>([]);
  const [coupons, setCoupons] = useState<CouponData[]>([]);
  const [loading, setLoading] = useState(true);
  const [orderStatusFilter, setOrderStatusFilter] = useState<string>("ALL");
  const [toastMsg, setToastMsg] = useState<string>("");

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
  const [couponFormError, setCouponFormError] = useState("");

  const showToast = (msg: string) => {
    setToastMsg(msg);
    setTimeout(() => setToastMsg(""), 3500);
  };

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

  // KPI Metrics Calculation
  const stats = useMemo(() => {
    const validOrders = orders.filter((o) => o.status !== "CANCELLED");
    const totalRevenue = validOrders.reduce((sum, o) => sum + o.totalAmount, 0);
    const totalDiscountGiven = validOrders.reduce(
      (sum, o) => sum + (o.discountAmount || 0),
      0
    );
    const ordersWithVoucher = orders.filter((o) => Boolean(o.couponCode)).length;
    const now = new Date();
    const activeCouponsCount = coupons.filter(
      (c) => c.isActive && new Date(c.expiresAt) > now
    ).length;
    const bestSellersCount = products.filter((p) => p.isBestSeller).length;

    return {
      totalRevenue,
      totalDiscountGiven,
      ordersWithVoucher,
      activeCouponsCount,
      bestSellersCount,
    };
  }, [orders, coupons, products]);

  const filteredOrders = useMemo(() => {
    if (orderStatusFilter === "ALL") return orders;
    return orders.filter((o) => o.status === orderStatusFilter);
  }, [orders, orderStatusFilter]);

  // Order Actions
  const handleUpdateOrderStatus = async (orderId: string, newStatus: string) => {
    try {
      const res = await fetch(`/api/orders/${orderId}`, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ status: newStatus }),
      });
      if (res.ok) {
        const updated = await res.json();
        setOrders((prev) =>
          prev.map((o) => (o.id === orderId ? updated : o))
        );
        showToast(`Đã cập nhật trạng thái đơn #${orderId.slice(-6).toUpperCase()} → ${newStatus}`);
      }
    } catch (err) {
      console.error("Update order status error:", err);
    }
  };

  const handleDeleteOrder = async (orderId: string) => {
    const res = await fetch(`/api/orders/${orderId}`, { method: "DELETE" });
    if (res.ok) {
      setOrders((prev) => prev.filter((o) => o.id !== orderId));
      showToast(`Đã xóa đơn hàng #${orderId.slice(-6).toUpperCase()}`);
    }
  };

  // Coupon Actions
  const handleAddCoupon = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!couponCode || !discountPercent || !expiresAt) return;
    setSubmittingCoupon(true);
    setCouponFormError("");
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
      const data = await res.json();
      if (res.ok) {
        setCoupons([data, ...coupons]);
        setCouponCode("");
        showToast(`Đã thêm mã giảm giá ${data.code} (-${data.discountPercent}%)`);
      } else {
        setCouponFormError(data.error || "Không thể tạo mã giảm giá");
      }
    } finally {
      setSubmittingCoupon(false);
    }
  };

  const handleToggleCouponActive = async (coupon: CouponData) => {
    const res = await fetch(`/api/coupons/${coupon.id}`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ isActive: !coupon.isActive }),
    });
    if (res.ok) {
      const updated = await res.json();
      setCoupons((prev) =>
        prev.map((c) => (c.id === coupon.id ? updated : c))
      );
      showToast(
        `Đã ${updated.isActive ? "kích hoạt" : "tạm khóa"} mã ${updated.code}`
      );
    }
  };

  const handleDeleteCoupon = async (coupon: CouponData) => {
    const res = await fetch(`/api/coupons/${coupon.id}`, {
      method: "DELETE",
    });
    if (res.ok) {
      setCoupons((prev) => prev.filter((c) => c.id !== coupon.id));
      showToast(`Đã xóa mã giảm giá ${coupon.code}`);
    }
  };

  // Product Actions
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
        showToast(`Đã thêm sản phẩm "${created.name}"`);
      }
    } finally {
      setSubmitting(false);
    }
  };

  const handleToggleBestSeller = async (prod: ProductData) => {
    const res = await fetch(`/api/products/${prod.id}`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ isBestSeller: !prod.isBestSeller }),
    });
    if (res.ok) {
      const updated = await res.json();
      setProducts((prev) =>
        prev.map((p) => (p.id === prod.id ? updated : p))
      );
      showToast(
        `Đã ${updated.isBestSeller ? "gắn" : "gỡ"} nhãn Best Seller cho "${
          updated.name
        }"`
      );
    }
  };

  const handleDeleteProduct = async (id: string) => {
    const res = await fetch(`/api/products/${id}`, { method: "DELETE" });
    if (res.ok) {
      setProducts(products.filter((p) => p.id !== id));
      showToast("Đã xóa sản phẩm khỏi kho");
    }
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
      {/* Toast Notification */}
      {toastMsg && (
        <div className="fixed bottom-6 left-6 z-50 flex items-center gap-2 bg-stone-900 text-white text-xs sm:text-sm font-medium px-5 py-3.5 rounded-2xl shadow-xl border border-stone-700">
          <CheckCircle2 className="w-4 h-4 text-emerald-400 flex-shrink-0" />
          <span>{toastMsg}</span>
        </div>
      )}

      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-8 rounded-3xl border border-rose-100 shadow-xs">
        <div>
          <span className="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-rose-500">
            <Sparkles className="w-3.5 h-3.5" /> Lumina Beauty Admin Portal
          </span>
          <h1 className="font-serif text-3xl font-bold text-stone-800 mt-1">
            Trung Tâm Quản Trị Đơn Hàng, Voucher & Mỹ Phẩm
          </h1>
          <p className="text-sm text-stone-500 mt-1">
            Đồng bộ thời gian thực với cơ sở dữ liệu Docker PostgreSQL (shop_db)
          </p>
        </div>
        <button
          onClick={fetchData}
          className="inline-flex items-center gap-2 bg-rose-50 hover:bg-rose-100 text-rose-700 text-sm font-semibold px-5 py-2.5 rounded-full border border-rose-200 transition w-fit"
        >
          <RefreshCw className="w-4 h-4" /> Làm mới dữ liệu
        </button>
      </div>

      {/* KPI SUMMARY CARDS */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div className="bg-white p-6 rounded-3xl border border-rose-100 shadow-2xs space-y-2">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold uppercase tracking-wider text-stone-400">
              Tổng Doanh Thu
            </span>
            <div className="w-9 h-9 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
              <TrendingUp className="w-5 h-5" />
            </div>
          </div>
          <p className="font-serif text-2xl font-bold text-rose-600">
            {formatVND(stats.totalRevenue)}
          </p>
          <p className="text-xs text-stone-500">
            Đã trừ ưu đãi Voucher ({formatVND(stats.totalDiscountGiven)})
          </p>
        </div>

        <div className="bg-white p-6 rounded-3xl border border-rose-100 shadow-2xs space-y-2">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold uppercase tracking-wider text-stone-400">
              Tổng Đơn Hàng
            </span>
            <div className="w-9 h-9 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
              <ClipboardList className="w-5 h-5" />
            </div>
          </div>
          <p className="font-serif text-2xl font-bold text-stone-800">
            {orders.length} đơn
          </p>
          <p className="text-xs text-stone-500">
            {stats.ordersWithVoucher} đơn có sử dụng mã Voucher
          </p>
        </div>

        <div className="bg-white p-6 rounded-3xl border border-rose-100 shadow-2xs space-y-2">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold uppercase tracking-wider text-stone-400">
              Mã Giảm Giá (Voucher)
            </span>
            <div className="w-9 h-9 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
              <TicketPercent className="w-5 h-5" />
            </div>
          </div>
          <p className="font-serif text-2xl font-bold text-emerald-600">
            {stats.activeCouponsCount} / {coupons.length} mã
          </p>
          <p className="text-xs text-stone-500">
            Đang kích hoạt & còn hạn sử dụng
          </p>
        </div>

        <div className="bg-white p-6 rounded-3xl border border-rose-100 shadow-2xs space-y-2">
          <div className="flex items-center justify-between">
            <span className="text-xs font-bold uppercase tracking-wider text-stone-400">
              Kho Mỹ Phẩm
            </span>
            <div className="w-9 h-9 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
              <ShoppingBag className="w-5 h-5" />
            </div>
          </div>
          <p className="font-serif text-2xl font-bold text-stone-800">
            {products.length} sản phẩm
          </p>
          <p className="text-xs text-stone-500">
            {stats.bestSellersCount} sản phẩm gắn nhãn Best Seller
          </p>
        </div>
      </div>

      {/* 1. QUẢN LÝ ĐƠN HÀNG & CẬP NHẬT TRẠNG THÁI */}
      <section className="bg-white p-6 sm:p-8 rounded-3xl border border-rose-100 shadow-xs space-y-6">
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div className="flex items-center gap-2.5">
            <ClipboardList className="w-5 h-5 text-rose-500" />
            <h2 className="font-serif text-2xl font-bold text-stone-800">
              1. Quản Lý Đơn Hàng ({filteredOrders.length})
            </h2>
          </div>

          {/* Filter by Order Status */}
          <div className="flex flex-wrap items-center gap-1.5">
            <button
              type="button"
              onClick={() => setOrderStatusFilter("ALL")}
              className={`px-3 py-1.5 rounded-full text-xs font-semibold transition ${
                orderStatusFilter === "ALL"
                  ? "bg-rose-500 text-white"
                  : "bg-[#FDFBF7] text-stone-600 border border-rose-100 hover:bg-rose-50"
              }`}
            >
              Tất cả ({orders.length})
            </button>
            {ORDER_STATUS_OPTIONS.map((st) => (
              <button
                key={st.value}
                type="button"
                onClick={() => setOrderStatusFilter(st.value)}
                className={`px-3 py-1.5 rounded-full text-xs font-semibold transition ${
                  orderStatusFilter === st.value
                    ? "bg-rose-500 text-white"
                    : "bg-[#FDFBF7] text-stone-600 border border-rose-100 hover:bg-rose-50"
                }`}
              >
                {st.label}
              </button>
            ))}
          </div>
        </div>

        {loading ? (
          <p className="text-sm text-stone-500 py-6">Đang tải đơn hàng...</p>
        ) : filteredOrders.length === 0 ? (
          <div className="p-8 rounded-2xl bg-[#FDFBF7] border border-rose-100 text-center text-sm text-stone-500">
            Chưa có đơn hàng nào khớp bộ lọc. Hãy thử đặt hàng tại trang{" "}
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
                  <th className="py-3.5 px-3">Mã Đơn</th>
                  <th className="py-3.5 px-3">Khách Hàng</th>
                  <th className="py-3.5 px-3">Địa Chỉ Giao Hàng</th>
                  <th className="py-3.5 px-3">Sản Phẩm Đặt</th>
                  <th className="py-3.5 px-3">Voucher</th>
                  <th className="py-3.5 px-3">Tổng Tiền</th>
                  <th className="py-3.5 px-3">Đổi Trạng Thái</th>
                  <th className="py-3.5 px-3 text-right">Xóa</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-rose-50">
                {filteredOrders.map((order) => {
                  const statusMeta =
                    ORDER_STATUS_OPTIONS.find((s) => s.value === order.status) ||
                    ORDER_STATUS_OPTIONS[1];
                  return (
                    <tr key={order.id} className="hover:bg-rose-50/30">
                      <td className="py-4 px-3 font-mono font-bold text-xs text-stone-700">
                        #{order.id.slice(-6).toUpperCase()}
                      </td>
                      <td className="py-4 px-3">
                        <p className="font-semibold text-stone-800">
                          {order.customerName}
                        </p>
                        <p className="text-xs text-stone-500">{order.phone}</p>
                      </td>
                      <td className="py-4 px-3 text-xs text-stone-600 max-w-xs">
                        {order.address}
                      </td>
                      <td className="py-4 px-3 text-xs text-stone-600">
                        <ul className="space-y-1">
                          {order.items.map((item) => (
                            <li key={item.id}>
                              • {item.product?.name || "Sản phẩm"} (x
                              {item.quantity})
                            </li>
                          ))}
                        </ul>
                      </td>
                      <td className="py-4 px-3 text-xs">
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
                      <td className="py-4 px-3 font-bold text-rose-600">
                        {formatVND(order.totalAmount)}
                      </td>
                      <td className="py-4 px-3">
                        <select
                          value={order.status}
                          onChange={(e) =>
                            handleUpdateOrderStatus(order.id, e.target.value)
                          }
                          className={`text-xs font-semibold px-3 py-1.5 rounded-full border cursor-pointer focus:outline-none ${statusMeta.color}`}
                        >
                          {ORDER_STATUS_OPTIONS.map((opt) => (
                            <option
                              key={opt.value}
                              value={opt.value}
                              className="bg-white text-stone-800"
                            >
                              {opt.label} ({opt.value})
                            </option>
                          ))}
                        </select>
                      </td>
                      <td className="py-4 px-3 text-right">
                        <button
                          type="button"
                          onClick={() => handleDeleteOrder(order.id)}
                          className="p-2 text-stone-400 hover:text-red-600 hover:bg-red-50 rounded-full transition"
                          title="Xóa đơn hàng"
                        >
                          <Trash2 className="w-4 h-4" />
                        </button>
                      </td>
                    </tr>
                  );
                })}
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
              2. Tạo Mã Giảm Giá (Voucher) Mới
            </h2>
          </div>

          {couponFormError && (
            <div className="p-3 rounded-xl bg-red-50 text-red-600 text-xs font-medium border border-red-200">
              {couponFormError}
            </div>
          )}

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
                  <th className="py-3 px-3 text-right">Thao Tác</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-rose-50">
                {coupons.map((c) => {
                  const expired = new Date(c.expiresAt) < new Date();
                  return (
                    <tr key={c.id} className="hover:bg-rose-50/30">
                      <td className="py-3.5 px-3">
                        <span className="font-mono font-bold text-rose-600">
                          {c.code}
                        </span>
                        {c.description && (
                          <p className="text-[11px] text-stone-400 line-clamp-1 max-w-[180px]">
                            {c.description}
                          </p>
                        )}
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
                        {expired ? (
                          <span className="bg-stone-100 text-stone-500 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                            Quá hạn
                          </span>
                        ) : !c.isActive ? (
                          <span className="bg-amber-50 text-amber-700 text-xs font-semibold px-2.5 py-0.5 rounded-full border border-amber-200">
                            Tạm khóa
                          </span>
                        ) : (
                          <span className="bg-emerald-50 text-emerald-700 text-xs font-semibold px-2.5 py-0.5 rounded-full border border-emerald-200">
                            Đang hoạt động
                          </span>
                        )}
                      </td>
                      <td className="py-3.5 px-3 text-right">
                        <div className="inline-flex items-center gap-1">
                          <button
                            type="button"
                            onClick={() => handleToggleCouponActive(c)}
                            className={`p-2 rounded-full transition ${
                              c.isActive
                                ? "text-emerald-600 hover:bg-emerald-50"
                                : "text-stone-400 hover:bg-stone-100"
                            }`}
                            title={
                              c.isActive
                                ? "Tạm khóa mã giảm giá"
                                : "Kích hoạt lại mã giảm giá"
                            }
                          >
                            <Power className="w-4 h-4" />
                          </button>
                          <button
                            type="button"
                            onClick={() => handleDeleteCoupon(c)}
                            className="p-2 text-stone-400 hover:text-red-600 hover:bg-red-50 rounded-full transition"
                            title="Xóa mã giảm giá"
                          >
                            <Trash2 className="w-4 h-4" />
                          </button>
                        </div>
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
              3. Thêm Sản Phẩm Mỹ Phẩm Mới
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
                        <div className="flex items-center gap-1.5">
                          <p className="font-semibold text-stone-800 line-clamp-1">
                            {prod.name}
                          </p>
                          {prod.isBestSeller && (
                            <span className="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full flex-shrink-0">
                              Best Seller
                            </span>
                          )}
                        </div>
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
                      <div className="inline-flex items-center gap-1">
                        <button
                          type="button"
                          onClick={() => handleToggleBestSeller(prod)}
                          className={`p-2 rounded-full transition ${
                            prod.isBestSeller
                              ? "text-amber-500 hover:bg-amber-50"
                              : "text-stone-300 hover:bg-stone-100"
                          }`}
                          title={
                            prod.isBestSeller
                              ? "Gỡ nhãn Best Seller"
                              : "Đặt làm Best Seller"
                          }
                        >
                          <Star
                            className={`w-4 h-4 ${
                              prod.isBestSeller ? "fill-amber-400" : ""
                            }`}
                          />
                        </button>
                        <button
                          type="button"
                          onClick={() => handleDeleteProduct(prod.id)}
                          className="p-2 text-stone-400 hover:text-red-600 hover:bg-red-50 rounded-full transition"
                          title="Xóa sản phẩm"
                        >
                          <Trash2 className="w-4 h-4" />
                        </button>
                      </div>
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
