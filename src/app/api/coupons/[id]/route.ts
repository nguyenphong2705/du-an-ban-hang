import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";

export async function PATCH(
  req: NextRequest,
  { params }: { params: { id: string } }
) {
  try {
    const body = await req.json();
    const { isActive, discountPercent, minOrderAmount, expiresAt, description } =
      body;

    const updated = await prisma.coupon.update({
      where: { id: params.id },
      data: {
        ...(typeof isActive === "boolean" ? { isActive } : {}),
        ...(discountPercent !== undefined
          ? { discountPercent: Number(discountPercent) }
          : {}),
        ...(minOrderAmount !== undefined
          ? { minOrderAmount: Number(minOrderAmount) }
          : {}),
        ...(expiresAt !== undefined ? { expiresAt: new Date(expiresAt) } : {}),
        ...(description !== undefined ? { description } : {}),
      },
    });

    return NextResponse.json(updated);
  } catch (error) {
    console.error("PATCH /api/coupons/[id] error:", error);
    return NextResponse.json(
      { error: "Không thể cập nhật trạng thái mã giảm giá" },
      { status: 500 }
    );
  }
}

export async function DELETE(
  _req: NextRequest,
  { params }: { params: { id: string } }
) {
  try {
    await prisma.coupon.delete({
      where: { id: params.id },
    });
    return NextResponse.json({ success: true });
  } catch (error) {
    console.error("DELETE /api/coupons/[id] error:", error);
    return NextResponse.json(
      { error: "Không thể xóa mã giảm giá" },
      { status: 500 }
    );
  }
}
