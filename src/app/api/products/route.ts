import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";

function slugify(str: string): string {
  return str
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/đ/g, "d")
    .replace(/[^a-z0-9\s-]/g, "")
    .trim()
    .replace(/\s+/g, "-")
    .concat("-" + Math.floor(1000 + Math.random() * 9000));
}

export async function GET() {
  try {
    const [products, categories, brands] = await Promise.all([
      prisma.product.findMany({
        include: { category: true, brand: true },
        orderBy: { createdAt: "desc" },
      }),
      prisma.category.findMany(),
      prisma.brand.findMany(),
    ]);
    return NextResponse.json({ products, categories, brands });
  } catch (error) {
    console.error("GET /api/products error:", error);
    return NextResponse.json(
      { error: "Lỗi tải dữ liệu sản phẩm" },
      { status: 500 }
    );
  }
}

export async function POST(req: NextRequest) {
  try {
    const body = await req.json();
    const {
      name,
      price,
      salePrice,
      stock,
      image,
      description,
      ingredients,
      skinType,
      volume,
      isBestSeller,
      categoryId,
      brandId,
    } = body;

    if (!name || !price || !categoryId || !brandId) {
      return NextResponse.json(
        { error: "Vui lòng nhập đầy đủ tên, giá, danh mục và thương hiệu." },
        { status: 400 }
      );
    }

    const slug = slugify(name);

    const newProduct = await prisma.product.create({
      data: {
        name,
        slug,
        price: Number(price),
        salePrice: salePrice ? Number(salePrice) : null,
        stock: stock ? Number(stock) : 50,
        images: [
          image ||
            "https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80",
        ],
        description:
          description ||
          "Sản phẩm mỹ phẩm chính hãng cao cấp phân phối tại Lumina Beauty.",
        ingredients:
          ingredients ||
          "Aqua, Glycerin, Niacinamide, Hyaluronic Acid, Panthenol, Tocopherol.",
        skinType: skinType || "Mọi loại da",
        volume: volume || "30ml",
        isBestSeller: Boolean(isBestSeller),
        categoryId,
        brandId,
      },
      include: {
        category: true,
        brand: true,
      },
    });

    return NextResponse.json(newProduct, { status: 201 });
  } catch (error) {
    console.error("POST /api/products error:", error);
    return NextResponse.json(
      { error: "Không thể thêm sản phẩm mới" },
      { status: 500 }
    );
  }
}
