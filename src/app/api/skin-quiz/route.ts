import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";

export interface SkinProfileInfo {
  skinType: string;
  title: string;
  badge: string;
  description: string;
  recommendedActives: string[];
  avoidNote: string;
  morningRoutine: string;
  eveningRoutine: string;
}

const SKIN_PROFILES: Record<string, SkinProfileInfo> = {
  "Da dầu": {
    skinType: "Da dầu",
    title: "Da Dầu & Hỗn Hợp Thiên Dầu",
    badge: "Kiểm soát bã nhờn & Thu nhỏ lỗ chân lông",
    description:
      "Tuyến bã nhờn của bạn hoạt động mạnh, đặc biệt ở vùng chữ T (trán, mũi, cằm), khiến lỗ chân lông dễ giãn nở và bít tắc gây mụn ẩn, mụn đầu đen. Làn da cần các hoạt chất làm sạch sâu cổ nang lông và điều tiết dầu thừa nhưng vẫn cấp nước mỏng nhẹ.",
    recommendedActives: [
      "Niacinamide 10%",
      "Zinc PCA 1%",
      "BHA 2% (Salicylic Acid)",
      "Muối Hồng & Tinh dầu Hoa Trà",
    ],
    avoidNote:
      "Tránh các loại kem dưỡng gốc dầu khoáng đặc bí (heavy occlusives) dễ gây bít tắc lỗ chân lông.",
    morningRoutine:
      "Sữa rửa mặt dịu nhẹ → Toner cấp ẩm → Tinh chất Niacinamide 10% + Zinc 1% kiềm dầu → Kem chống nắng kiềm dầu.",
    eveningRoutine:
      "Tẩy trang kỹ → Dung dịch BHA 2% (2-3 lần/tuần) làm sạch lỗ chân lông → Serum phục hồi mỏng nhẹ.",
  },
  "Da khô": {
    skinType: "Da khô",
    title: "Da Khô & Thiếu Hụt Độ Ẩm",
    badge: "Cấp ẩm đa tầng & Khóa ẩm chuyên sâu",
    description:
      "Làn da của bạn thiếu cả nước (dehydrated) lẫn dầu tự nhiên (lipid), dẫn đến cảm giác khô căng sau khi rửa mặt, bề mặt kém mịn màng và dễ xuất hiện nếp nhăn li ti. Bạn cần bổ sung các phân tử giữ nước đa tầng kết hợp thành phần khóa ẩm giàu dưỡng chất.",
    recommendedActives: [
      "Hyaluronic Acid 2%",
      "Pro-Vitamin B5",
      "Chiết xuất Nhân Sâm cô đặc",
      "Squalane & Ceramide",
    ],
    avoidNote:
      "Hạn chế dùng chất tẩy rửa có tính kiềm mạnh hoặc cồn khô nồng độ cao làm mất màng ẩm tự nhiên.",
    morningRoutine:
      "Rửa mặt bằng nước ấm nhẹ → Serum Hyaluronic Acid 2% + B5 trên nền da ẩm → Kem dưỡng khóa ẩm → Kem nền/Chống nắng dưỡng ẩm.",
    eveningRoutine:
      "Làm sạch dịu nhẹ → Serum cấp nước đa tầng → Kem dưỡng Nhân Sâm cô đặc Sulwhasoo khóa ẩm sâu qua đêm.",
  },
  "Da nhạy cảm": {
    skinType: "Da nhạy cảm",
    title: "Da Nhạy Cảm & Màng Ẩm Yếu",
    badge: "Làm dịu kích ứng & Phục hồi hàng rào bảo vệ da",
    description:
      "Hàng rào bảo vệ tự nhiên của da đang mỏng yếu, dễ phản ứng đỏ rát, châm chích trước sự thay đổi của thời tiết, khói bụi hoặc sau khi dùng hoạt chất mạnh (treatment). Ưu tiên số một là làm dịu tức thì và tái thiết lập lớp màng hydrolipid khỏe mạnh.",
    recommendedActives: [
      "Panthenol 5% (Vitamin B5)",
      "Madecassoside (Chiết xuất Rau Má)",
      "Bơ Hạt Mỡ (Shea Butter 10%)",
      "Tribioma & Niacinamide nồng độ dịu nhẹ",
    ],
    avoidNote:
      "Tránh tẩy tế bào chết vật lý hạt to, hương liệu nồng hoặc kết hợp nhiều acid mạnh cùng lúc.",
    morningRoutine:
      "Làm sạch cực dịu nhẹ → Kem dưỡng làm dịu Cicaplast Baume B5+ → Chống nắng vật lý lành tính.",
    eveningRoutine:
      "Làm sạch dịu nhẹ → Thoa lớp kem dưỡng phục hồi B5+ & Rau má giúp tái tạo màng ẩm suốt đêm.",
  },
  "Mọi loại da": {
    skinType: "Mọi loại da",
    title: "Da Thường / Hỗn Hợp Cân Bằng",
    badge: "Duy trì độ rạng rỡ & Ngăn ngừa lão hóa sớm",
    description:
      "Xin chúc mừng! Làn da của bạn có độ cân bằng dầu - nước khá lý tưởng, ít gặp tình trạng kích ứng hay bít tắc nặng. Đây là nền tảng tuyệt vời để bạn tập trung vào việc duy trì vẻ căng bóng, làm đều màu da và chống lão hóa chuyên sâu.",
    recommendedActives: [
      "Retinol 0.5% tinh khiết",
      "Peptide & Ceramide NP",
      "Hyaluronic Acid",
      "Chiết xuất Hoa Hồng & Vitamin E",
    ],
    avoidNote:
      "Khi sử dụng Retinol vào ban đêm, luôn nhớ thoa kem chống nắng đầy đủ vào ban ngày để bảo vệ da.",
    morningRoutine:
      "Làm sạch da → Dưỡng ẩm cân bằng → Phấn nước dưỡng da chống nắng SPF50+ PA+++.",
    eveningRoutine:
      "Làm sạch sâu → Kem dưỡng trẻ hóa da ban đêm Retinol 0.5% kết hợp Peptide & Ceramide.",
  },
};

function getRecommendationReason(
  productSkinType: string,
  targetSkinType: string,
  ingredients: string
): string {
  if (productSkinType === targetSkinType) {
    if (targetSkinType === "Da dầu") {
      return "Công thức chuyên biệt cho da dầu giúp kiểm soát bã nhờn, thông thoáng lỗ chân lông.";
    }
    if (targetSkinType === "Da khô") {
      return "Thiết kế riêng cho da khô giúp cấp nước đa tầng và khóa ẩm suốt 24 giờ.";
    }
    if (targetSkinType === "Da nhạy cảm") {
      return "Bảng thành phần lành tính tối ưu cho da nhạy cảm, làm dịu đỏ rát và phục hồi màng ẩm.";
    }
    return "Phù hợp hoàn hảo với nền da cân bằng, giúp trẻ hóa và tôn lên vẻ rạng rỡ.";
  }

  return `Phù hợp cho mọi loại da (kể cả ${targetSkinType.toLowerCase()}) với thành phần ${ingredients
    .split(",")
    .slice(0, 3)
    .join(", ")}.`;
}

function diagnoseSkinType(answers: string[]): string {
  const counts: Record<string, number> = {
    "Da dầu": 0,
    "Da khô": 0,
    "Da nhạy cảm": 0,
    "Mọi loại da": 0,
  };

  answers.forEach((ans, index) => {
    const weight = index === 3 ? 1.5 : 1;
    if (counts[ans] !== undefined) {
      counts[ans] += weight;
    }
  });

  let bestType = "Da dầu";
  let maxScore = -1;
  for (const [type, score] of Object.entries(counts)) {
    if (score > maxScore) {
      maxScore = score;
      bestType = type;
    }
  }
  return bestType;
}

async function buildQuizResponse(skinType: string) {
  const normalizedType = SKIN_PROFILES[skinType] ? skinType : "Da dầu";
  const profile = SKIN_PROFILES[normalizedType];

  const dbProducts = await prisma.product.findMany({
    where: {
      skinType: {
        in:
          normalizedType === "Mọi loại da"
            ? ["Mọi loại da"]
            : [normalizedType, "Mọi loại da"],
      },
    },
    include: {
      brand: true,
      category: true,
    },
    orderBy: [{ isBestSeller: "desc" }, { createdAt: "desc" }],
  });

  // Sort exact skinType matches first, then Skincare category, then Best Sellers
  const sortedProducts = [...dbProducts].sort((a, b) => {
    const aExact = a.skinType === normalizedType ? 1 : 0;
    const bExact = b.skinType === normalizedType ? 1 : 0;
    if (aExact !== bExact) return bExact - aExact;

    const aSkincare = a.category?.slug === "cham-soc-da" ? 1 : 0;
    const bSkincare = b.category?.slug === "cham-soc-da" ? 1 : 0;
    if (aSkincare !== bSkincare) return bSkincare - aSkincare;

    const aBest = a.isBestSeller ? 1 : 0;
    const bBest = b.isBestSeller ? 1 : 0;
    return bBest - aBest;
  });

  const recommendedProducts = sortedProducts.map((product, idx) => {
    const isExactMatch = product.skinType === normalizedType;
    const matchScore = isExactMatch
      ? Math.max(94, 99 - idx * 2)
      : Math.max(86, 91 - idx);

    return {
      ...product,
      matchScore,
      isExactMatch,
      aiReason: getRecommendationReason(
        product.skinType,
        normalizedType,
        product.ingredients
      ),
    };
  });

  const exactMatchCount = recommendedProducts.filter(
    (p) => p.skinType === normalizedType
  ).length;

  return {
    skinType: normalizedType,
    profile,
    exactMatchCount,
    totalRecommended: recommendedProducts.length,
    recommendedProducts,
  };
}

export async function GET(req: NextRequest) {
  try {
    const { searchParams } = new URL(req.url);
    const skinType = searchParams.get("skinType") || "Da dầu";
    const result = await buildQuizResponse(skinType);
    return NextResponse.json(result);
  } catch (error) {
    console.error("GET /api/skin-quiz error:", error);
    return NextResponse.json(
      { error: "Không thể phân tích dữ liệu loại da" },
      { status: 500 }
    );
  }
}

export async function POST(req: NextRequest) {
  try {
    const body = await req.json();
    const { answers, skinType: explicitSkinType } = body;

    let diagnosedType = explicitSkinType;
    if (
      !diagnosedType &&
      Array.isArray(answers) &&
      answers.length > 0
    ) {
      diagnosedType = diagnoseSkinType(answers);
    }

    const result = await buildQuizResponse(diagnosedType || "Da dầu");
    return NextResponse.json(result);
  } catch (error) {
    console.error("POST /api/skin-quiz error:", error);
    return NextResponse.json(
      { error: "Lỗi trong quá trình AI phân tích loại da" },
      { status: 500 }
    );
  }
}
