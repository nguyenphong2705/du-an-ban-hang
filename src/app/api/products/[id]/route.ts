import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";

export async function PATCH(
  req: NextRequest,
  { params }: { params: { id: string } }
) {
  try {
    const body = await req.json();
    const { isBestSeller, price, salePrice, stock } = body;

    const updated = await prisma.product.update({
      where: { id: params.id },
      data: {
        ...(typeof isBestSeller === "boolean" ? { isBestSeller } : {}),
        ...(price !== undefined ? { price: Number(price) } : {}),
        ...(salePrice !== undefined
          ? { salePrice: salePrice ? Number(salePrice) : null }
          : {}),
        ...(stock !== undefined ? { stock: Number(stock) } : {}),
      },
      include: {
        category: true,
        brand: true,
      },
    });

    return NextResponse.json(updated);
  } catch (error) {
    console.error("PATCH /api/products/[id] error:", error);
    return NextResponse.json(
      { error: "Không thể cập nhật sản phẩm" },
      { status: 500 }
    );
  }
}

export async function DELETE(
  _req: NextRequest,
  { params }: { params: { id: string } }
) {
  try {
    await prisma.product.delete({
      where: { id: params.id },
    });
    return NextResponse.json({ success: true });
  } catch (error) {
    console.error("DELETE /api/products/[id] error:", error);
    return NextResponse.json(
      { error: "Không thể xóa sản phẩm" },
      { status: 500 }
    );
  }
}
