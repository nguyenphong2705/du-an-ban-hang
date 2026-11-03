import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";

export async function GET() {
  try {
    const orders = await prisma.order.findMany({
      include: {
        items: {
          include: {
            product: true,
          },
        },
      },
      orderBy: {
        createdAt: "desc",
      },
    });
    return NextResponse.json(orders);
  } catch (error) {
    console.error("GET /api/orders error:", error);
    return NextResponse.json(
      { error: "Không thể tải danh sách đơn hàng" },
      { status: 500 }
    );
  }
}

export async function POST(req: NextRequest) {
  try {
    const body = await req.json();
    const {
      customerName,
      phone,
      address,
      note,
      paymentMethod,
      items,
    } = body;

    if (!customerName || !phone || !address || !items || items.length === 0) {
      return NextResponse.json(
        { error: "Vui lòng điền đầy đủ thông tin giao hàng và giỏ hàng." },
        { status: 400 }
      );
    }

    const totalAmount = items.reduce(
      (sum: number, item: { price: number; quantity: number }) =>
        sum + Number(item.price) * Number(item.quantity),
      0
    );

    const order = await prisma.order.create({
      data: {
        customerName,
        phone,
        address,
        note: note || "",
        paymentMethod: paymentMethod || "COD",
        totalAmount,
        status: "CONFIRMED",
        items: {
          create: items.map(
            (item: { productId: string; quantity: number; price: number }) => ({
              productId: item.productId,
              quantity: Number(item.quantity),
              price: Number(item.price),
            })
          ),
        },
      },
      include: {
        items: {
          include: {
            product: true,
          },
        },
      },
    });

    return NextResponse.json(order, { status: 201 });
  } catch (error) {
    console.error("POST /api/orders error:", error);
    return NextResponse.json(
      { error: "Có lỗi xảy ra khi tạo đơn hàng" },
      { status: 500 }
    );
  }
}
