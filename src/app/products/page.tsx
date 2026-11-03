import React, { Suspense } from "react";
import { prisma } from "@/lib/prisma";
import ProductsFilterClient from "@/components/ProductsFilterClient";

export const dynamic = "force-dynamic";

export default async function ProductsPage() {
  const [products, categories, brands] = await Promise.all([
    prisma.product.findMany({
      include: {
        category: true,
        brand: true,
      },
      orderBy: {
        createdAt: "desc",
      },
    }),
    prisma.category.findMany(),
    prisma.brand.findMany(),
  ]);

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
      {/* Page Banner */}
      <div className="bg-gradient-to-r from-rose-100/70 via-[#FAF6EE] to-rose-50 rounded-3xl p-8 sm:p-10 border border-rose-100">
        <span className="text-xs font-bold uppercase tracking-[0.2em] text-rose-500">
          Lumina Beauty Catalog
        </span>
        <h1 className="font-serif text-3xl sm:text-4xl font-bold text-stone-800 mt-1">
          Tất Cả Sản Phẩm Mỹ Phẩm & Chăm Sóc Da
        </h1>
        <p className="text-sm text-stone-600 mt-2 max-w-2xl">
          Dễ dàng tra cứu theo hoạt chất (Niacinamide, BHA, Retinol, Hyaluronic
          Acid), lọc theo danh mục, thương hiệu yêu thích, loại da và mức giá.
        </p>
      </div>

      <Suspense
        fallback={
          <div className="text-center py-12 text-stone-500">
            Đang tải bộ sưu tập mỹ phẩm...
          </div>
        }
      >
        <ProductsFilterClient
          products={products}
          categories={categories}
          brands={brands}
        />
      </Suspense>
    </div>
  );
}
