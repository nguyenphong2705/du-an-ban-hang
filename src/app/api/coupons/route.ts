import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { validateCouponCode } from "@/lib/coupon";

export async function GET(req: NextRequest) {
  try {
    const { searchParams } = new URL(req.url);
    const code = searchParams.get("code");
    const subtotalParam = searchParams.get("subtotal");

    if (code) {
      const subtotal = subtotalParam ? Number(subtotalParam) : 0;
      const validation = await validateCouponCode(code, subtotal);
      if (!validation.valid) {
        return NextResponse.json(
          { valid: false, error: validation.error },
          { status: validation.status }
        );
      }
      return NextResponse.json({
        valid: true,
        coupon: validation.coupon,
        discountAmount: validation.discountAmount,
        finalTotal: validation.finalTotal,
      });
    }

    const coupons = await prisma.coupon.findMany({
      orderBy: [{ isActive: "desc" }, { discountPercent: "desc" }],
    });

    return NextResponse.json(coupons);
  } catch (error) {
    console.error("GET /api/coupons error:", error);
    return NextResponse.json(
      { error: "Không thể tải danh sách mã giảm giá" },
      { status: 500 }
    );
  }
}

export async function POST(req: NextRequest) {
  try {
    const body = await req.json();

    // Case 1: Validate coupon from /checkout
    if (body.action === "validate" || body.subtotal !== undefined) {
      const result = await validateCouponCode(
        String(body.code || ""),
        Number(body.subtotal || 0)
      );

      if (!result.valid) {
        return NextResponse.json(
          { valid: false, error: result.error },
          { status: result.status }
        );
      }

      return NextResponse.json({
        valid: true,
        coupon: result.coupon,
        discountAmount: result.discountAmount,
        finalTotal: result.finalTotal,
      });
    }

    // Case 2: Create a new coupon from /admin
    const {
      code,
      discountPercent,
      description,
      minOrderAmount,
      maxDiscount,
      expiresAt,
    } = body;

    if (!code || !discountPercent || !expiresAt) {
      return NextResponse.json(
        { error: "Vui lòng nhập mã code, phần trăm giảm và hạn sử dụng." },
        { status: 400 }
      );
    }

    const normalizedCode = String(code).trim().toUpperCase();
    const created = await prisma.coupon.create({
      data: {
        code: normalizedCode,
        discountPercent: Math.min(100, Math.max(1, Number(discountPercent))),
        description:
          description || `Voucher giảm ${discountPercent}% tại Lumina Beauty`,
        minOrderAmount: minOrderAmount ? Number(minOrderAmount) : 0,
        maxDiscount: maxDiscount ? Number(maxDiscount) : null,
        expiresAt: new Date(expiresAt),
        isActive: true,
      },
    });

    return NextResponse.json(created, { status: 201 });
  } catch (error) {
    console.error("POST /api/coupons error:", error);
    return NextResponse.json(
      { error: "Không thể xử lý mã giảm giá (Mã có thể đã tồn tại)" },
      { status: 500 }
    );
  }
}
