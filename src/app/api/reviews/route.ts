import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";

export async function POST(req: NextRequest) {
  try {
    const body = await req.json();
    const { productId, customerName, rating, comment } = body;

    if (!productId || !customerName || !comment) {
      return NextResponse.json(
        { error: "Vui lòng nhập tên và nội dung đánh giá" },
        { status: 400 }
      );
    }

    const review = await prisma.review.create({
      data: {
        productId,
        customerName,
        rating: Number(rating) || 5,
        comment,
      },
    });

    return NextResponse.json(review, { status: 201 });
  } catch (error) {
    console.error("POST /api/reviews error:", error);
    return NextResponse.json(
      { error: "Không thể gửi đánh giá" },
      { status: 500 }
    );
  }
}
