import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";

export async function PATCH(
  req: NextRequest,
  { params }: { params: { id: string } }
) {
  try {
    const body = await req.json();
    const { status } = body;

    if (!status) {
      return NextResponse.json(
        { error: "Vui lòng chọn trạng thái đơn hàng" },
        { status: 400 }
      );
    }

    const updated = await prisma.order.update({
      where: { id: params.id },
      data: { status: String(status) },
      include: {
        items: {
          include: {
            product: true,
          },
        },
      },
    });

    return NextResponse.json(updated);
  } catch (error) {
    console.error("PATCH /api/orders/[id] error:", error);
    return NextResponse.json(
      { error: "Không thể cập nhật trạng thái đơn hàng" },
      { status: 500 }
    );
  }
}

export async function DELETE(
  _req: NextRequest,
  { params }: { params: { id: string } }
) {
  try {
    await prisma.order.delete({
      where: { id: params.id },
    });
    return NextResponse.json({ success: true });
  } catch (error) {
    console.error("DELETE /api/orders/[id] error:", error);
    return NextResponse.json(
      { error: "Không thể xóa đơn hàng" },
      { status: 500 }
    );
  }
}
