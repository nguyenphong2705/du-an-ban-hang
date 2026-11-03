import { prisma, formatVND } from "@/lib/prisma";

export async function validateCouponCode(code: string, subtotal: number) {
  const normalizedCode = code.trim().toUpperCase();
  if (!normalizedCode) {
    return {
      valid: false,
      error: "Vui lòng nhập mã giảm giá.",
      status: 400,
    };
  }

  const coupon = await prisma.coupon.findUnique({
    where: { code: normalizedCode },
  });

  if (!coupon || !coupon.isActive) {
    return {
      valid: false,
      error: `Mã giảm giá "${normalizedCode}" không tồn tại hoặc đã ngừng áp dụng.`,
      status: 404,
    };
  }

  const now = new Date();
  if (new Date(coupon.expiresAt) < now) {
    return {
      valid: false,
      error: `Mã giảm giá "${normalizedCode}" đã hết hạn sử dụng (${new Date(
        coupon.expiresAt
      ).toLocaleDateString("vi-VN")}).`,
      status: 400,
    };
  }

  if (subtotal < coupon.minOrderAmount) {
    return {
      valid: false,
      error: `Mã "${normalizedCode}" yêu cầu đơn hàng tối thiểu ${formatVND(
        coupon.minOrderAmount
      )}.`,
      status: 400,
    };
  }

  let discountAmount = Math.round((subtotal * coupon.discountPercent) / 100);
  if (coupon.maxDiscount && discountAmount > coupon.maxDiscount) {
    discountAmount = coupon.maxDiscount;
  }

  const finalTotal = Math.max(0, subtotal - discountAmount);

  return {
    valid: true,
    coupon,
    discountAmount,
    finalTotal,
    status: 200,
  };
}
