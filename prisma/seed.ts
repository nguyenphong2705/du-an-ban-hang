import { PrismaClient } from "@prisma/client";

const prisma = new PrismaClient();

async function main() {
  console.log("🌱 Bắt đầu khởi tạo dữ liệu mẫu cho Lumina Beauty...");

  // Xóa dữ liệu cũ theo thứ tự khóa ngoại
  await prisma.orderItem.deleteMany();
  await prisma.order.deleteMany();
  await prisma.coupon.deleteMany();
  await prisma.review.deleteMany();
  await prisma.product.deleteMany();
  await prisma.category.deleteMany();
  await prisma.brand.deleteMany();

  // 1. Tạo 4 Danh mục (Categories)
  const catSkincare = await prisma.category.create({
    data: {
      name: "Chăm sóc da",
      slug: "cham-soc-da",
      image: "https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80",
    },
  });

  const catMakeup = await prisma.category.create({
    data: {
      name: "Trang điểm",
      slug: "trang-diem",
      image: "https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=800&q=80",
    },
  });

  const catBodycare = await prisma.category.create({
    data: {
      name: "Chăm sóc cơ thể",
      slug: "cham-soc-co-the",
      image: "https://images.unsplash.com/photo-1608248597359-0e6d526a6c18?auto=format&fit=crop&w=800&q=80",
    },
  });

  const catPerfume = await prisma.category.create({
    data: {
      name: "Nước hoa",
      slug: "nuoc-hoa",
      image: "https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=800&q=80",
    },
  });

  // 2. Tạo 4 Thương hiệu nổi tiếng (Brands)
  const brandLaRoche = await prisma.brand.create({
    data: {
      name: "La Roche-Posay",
      logo: "https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=400&q=80",
      origin: "Pháp",
    },
  });

  const brandSulwhasoo = await prisma.brand.create({
    data: {
      name: "Sulwhasoo",
      logo: "https://images.unsplash.com/photo-1612817288484-6f916006741a?auto=format&fit=crop&w=400&q=80",
      origin: "Hàn Quốc",
    },
  });

  const brandShiseido = await prisma.brand.create({
    data: {
      name: "Shiseido",
      logo: "https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=400&q=80",
      origin: "Nhật Bản",
    },
  });

  const brandLancome = await prisma.brand.create({
    data: {
      name: "Lancôme",
      logo: "https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=400&q=80",
      origin: "Pháp",
    },
  });

  // 3. Tạo 12 Sản phẩm mỹ phẩm thực tế
  const productsData = [
    {
      name: "Tinh Chất Sáng Da Mờ Thâm Niacinamide 10% + Zinc 1%",
      slug: "tinh-chat-sang-da-mo-tham-niacinamide-10-zinc-1",
      price: 420000,
      salePrice: 345000,
      stock: 85,
      images: [
        "https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80",
        "https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80",
      ],
      description:
        "Tinh chất cô đặc chứa 10% Niacinamide tinh khiết kết hợp 1% Zinc PCA giúp kiểm soát dầu thừa tối ưu, thu nhỏ lỗ chân lông, làm dịu nốt mụn sưng đỏ và cải thiện sắc tố da sáng mịn đều màu sau 4 tuần sử dụng.",
      ingredients:
        "Aqua (Water), Niacinamide (10%), Pentylene Glycol, Zinc PCA (1%), Dimethyl Isosorbide, Tamarindus Indica Seed Gum, Xanthan Gum, Isoceteth-20, Ethoxydiglycol, Phenoxyethanol, Chlorphenesin.",
      skinType: "Da dầu",
      volume: "30ml",
      isBestSeller: true,
      categoryId: catSkincare.id,
      brandId: brandLaRoche.id,
    },
    {
      name: "Dung Dịch Loại Bỏ Tế Bào Chết BHA 2% Salicylic Acid",
      slug: "dung-dich-loai-bo-te-bao-chet-bha-2-salicylic-acid",
      price: 890000,
      salePrice: 750000,
      stock: 60,
      images: [
        "https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80",
        "https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80",
      ],
      description:
        "Dung dịch tẩy tế bào chết hóa học chứa 2% BHA (Salicylic Acid) thẩm thấu sâu vào lỗ chân lông giúp đẩy mụn ẩn, làm sạch sợi bã nhờn, ngăn ngừa mụn đầu đen và tái tạo bề mặt da láng mịn.",
      ingredients:
        "Water, Methylpropanediol, Butylene Glycol, Salicylic Acid (BHA 2%), Polysorbate 20, Camellia Oleifera (Green Tea) Leaf Extract, Sodium Hydroxide, Tetrasodium EDTA.",
      skinType: "Da dầu",
      volume: "118ml",
      isBestSeller: true,
      categoryId: catSkincare.id,
      brandId: brandLaRoche.id,
    },
    {
      name: "Kem Dưỡng Trẻ Hóa Da Ban Đêm Retinol 0.5% Tinh Khiết",
      slug: "kem-duong-tre-hoa-da-ban-dem-retinol-0-5-tinh-khiet",
      price: 1450000,
      salePrice: 1190000,
      stock: 40,
      images: [
        "https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=900&q=80",
        "https://images.unsplash.com/photo-1612817288484-6f916006741a?auto=format&fit=crop&w=900&q=80",
      ],
      description:
        "Kem dưỡng chống lão hóa chuyên sâu với công thức Retinol 0.5% bọc vi nang kết hợp Peptide và Ceramide giúp kích thích tăng sinh Collagen, làm mờ nếp nhăn li ti và nâng cơ săn chắc mà không gây bong tróc.",
      ingredients:
        "Aqua, Glycerin, Caprylic/Capric Triglyceride, Retinol (0.5%), Ceramide NP, Niacinamide, Hyaluronic Acid, Tocopherol (Vitamin E), Panthenol, Squalane.",
      skinType: "Mọi loại da",
      volume: "50ml",
      isBestSeller: true,
      categoryId: catSkincare.id,
      brandId: brandShiseido.id,
    },
    {
      name: "Serum Cấp Ẩm Đa Tầng Hyaluronic Acid 2% + Vitamin B5",
      slug: "serum-cap-am-da-tang-hyaluronic-acid-2-vitamin-b5",
      price: 680000,
      salePrice: 550000,
      stock: 110,
      images: [
        "https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&w=900&q=80",
        "https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80",
      ],
      description:
        "Serum cấp nước chuyên sâu với phân tử Hyaluronic Acid đa kích thước kết hợp Pro-Vitamin B5 giúp phục hồi màng bảo vệ da, làm dịu tình trạng khô căng bong tróc và mang lại làn da căng bóng ngậm nước.",
      ingredients:
        "Aqua (Water), Sodium Hyaluronate, Sodium Hyaluronate Crosspolymer, Panthenol (Vitamin B5), Ahnfeltia Concinna Extract, Glycerin, Pentylene Glycol, Propanediol.",
      skinType: "Da khô",
      volume: "30ml",
      isBestSeller: true,
      categoryId: catSkincare.id,
      brandId: brandLaRoche.id,
    },
    {
      name: "Kem Dưỡng Phục Hồi Nhân Sâm Cô Đặc Sulwhasoo Concentrated Ginseng",
      slug: "kem-duong-phuc-hoi-nhan-sam-co-dac-sulwhasoo",
      price: 2650000,
      salePrice: 2290000,
      stock: 25,
      images: [
        "https://images.unsplash.com/photo-1612817288484-6f916006741a?auto=format&fit=crop&w=900&q=80",
        "https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80",
      ],
      description:
        "Tuyệt tác dưỡng da hoàng cung Hàn Quốc chiết xuất từ tinh chất Nhân Sâm cô đặc gấp 6000 lần kết hợp Squalane và Hyaluronic Acid nuôi dưỡng làn da khô lão hóa trở nên hồng hào, săn chắc từ sâu bên trong.",
      ingredients:
        "Panax Ginseng Root Water, Squalane, Butylene Glycol, Glycerin, Hyaluronic Acid, Niacinamide, Shea Butter, Adenosine, Tocopherol, Honey Extract.",
      skinType: "Da khô",
      volume: "60ml",
      isBestSeller: false,
      categoryId: catSkincare.id,
      brandId: brandSulwhasoo.id,
    },
    {
      name: "Kem Dưỡng Làm Dịu Phục Hồi Da Nhạy Cảm Cicaplast Baume B5+",
      slug: "kem-duong-lam-diu-phuc-hoi-da-nhay-cam-cicaplast-baume-b5",
      price: 495000,
      salePrice: 415000,
      stock: 150,
      images: [
        "https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80",
        "https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80",
      ],
      description:
        "Kem dưỡng quốc dân dành riêng cho làn da nhạy cảm, kích ứng hoặc sau treatment. Chứa 5% Panthenol (Vitamin B5) và Madecassoside chiết xuất rau má giúp làm dịu vết đỏ tức thì và tái tạo hàng rào ẩm tự nhiên.",
      ingredients:
        "Aqua, Hydrogenated Polyisobutene, Dimethicone, Glycerin, Butyrospermum Parkii Butter, Panthenol (5%), Madecassoside, Tribioma, Zinc Gluconate, Silica.",
      skinType: "Da nhạy cảm",
      volume: "100ml",
      isBestSeller: true,
      categoryId: catSkincare.id,
      brandId: brandLaRoche.id,
    },
    {
      name: "Phấn Nước Trang Điểm Căng Bóng Sulwhasoo Perfecting Cushion",
      slug: "phan-nuoc-trang-diem-cang-bong-sulwhasoo-perfecting-cushion",
      price: 1350000,
      salePrice: 1150000,
      stock: 45,
      images: [
        "https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80",
        "https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=900&q=80",
      ],
      description:
        "Phấn nước cao cấp kết hợp dưỡng chất skincare giúp che phủ hoàn hảo khuyết điểm suốt 24 giờ mà vẫn giữ lớp nền mỏng nhẹ, căng bóng tự nhiên chuẩn Hàn Quốc cùng chỉ số chống nắng SPF50+ PA+++.",
      ingredients:
        "Water, Titanium Dioxide, Cyclopentasiloxane, Ethylhexyl Methoxycinnamate, Niacinamide, Hyaluronic Acid, Schizophyllum Commune Mycelium Ferment Filtrate, Adenosine.",
      skinType: "Mọi loại da",
      volume: "15g x 2",
      isBestSeller: true,
      categoryId: catMakeup.id,
      brandId: brandSulwhasoo.id,
    },
    {
      name: "Son Thỏi Lì Mịn Môi Lancôme L'Absolu Rouge Drama Matte",
      slug: "son-thoi-li-min-moi-lancome-labsolu-rouge-drama-matte",
      price: 980000,
      salePrice: 860000,
      stock: 70,
      images: [
        "https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=900&q=80",
        "https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80",
      ],
      description:
        "Son thỏi lì cao cấp sắc hồng đất thời thượng, bổ sung chiết xuất hoa hồng Grand Rose và Hyaluronic Acid giúp đôi môi lên màu chuẩn sắc ngay từ lần chạm đầu tiên, mềm mượt không gây khô môi.",
      ingredients:
        "Dimethicone, Bis-Diglyceryl Polyacyladipate-2, Hydrogenated Polyisobutene, Rosa Centifolia Flower Extract, Hyaluronic Acid, Simmondsia Chinensis (Jojoba) Seed Oil, Tocopherol.",
      skinType: "Mọi loại da",
      volume: "3.4g",
      isBestSeller: false,
      categoryId: catMakeup.id,
      brandId: brandLancome.id,
    },
    {
      name: "Kem Nền Mỏng Nhẹ Bắt Sáng Shiseido Synchro Skin Radiant Lifting",
      slug: "kem-nen-mong-nhe-bat-sang-shiseido-synchro-skin",
      price: 1290000,
      salePrice: 1090000,
      stock: 50,
      images: [
        "https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=900&q=80",
        "https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80",
      ],
      description:
        "Ứng dụng công nghệ điều chỉnh ánh sáng thông minh từ Nhật Bản, giúp che phủ nếp nhăn và vùng da không đều màu, đồng thời khóa ẩm suốt 24 giờ cho làn da khô luôn rạng rỡ.",
      ingredients:
        "Water, Dimethicone, Titanium Dioxide, Glycerin, Niacinamide, Citrus Unshiu Peel Extract, Fagus Sylvatica Bud Extract, Sodium Acetylated Hyaluronate.",
      skinType: "Da khô",
      volume: "30ml",
      isBestSeller: false,
      categoryId: catMakeup.id,
      brandId: brandShiseido.id,
    },
    {
      name: "Sữa Dưỡng Thể Trắng Sáng Niacinamide & Bơ Hạt Mỡ Lipikar Lait",
      slug: "sua-duong-the-trang-sang-niacinamide-bo-hat-mo-lipikar",
      price: 560000,
      salePrice: 465000,
      stock: 90,
      images: [
        "https://images.unsplash.com/photo-1608248597359-0e6d526a6c18?auto=format&fit=crop&w=900&q=80",
        "https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80",
      ],
      description:
        "Sữa dưỡng thể giàu Niacinamide tinh khiết và 10% Bơ Hạt Mỡ (Shea Butter) giúp nuôi dưỡng làn da cơ thể mềm mại, dưỡng sáng đều màu và làm dịu cảm giác khô ngứa trên da nhạy cảm suốt 48 giờ.",
      ingredients:
        "Aqua, Butyrospermum Parkii Butter (10%), Glycerin, Sorbitan Stearate, Paraffinum Liquidum, Niacinamide (4%), Brassica Campestris Oleifera Oil, Panthenol.",
      skinType: "Da nhạy cảm",
      volume: "400ml",
      isBestSeller: false,
      categoryId: catBodycare.id,
      brandId: brandLaRoche.id,
    },
    {
      name: "Tẩy Tế Bào Chết Toàn Thân Muối Hồng & BHA Sáng Mịn Cơ Thể",
      slug: "tay-te-bao-chet-toan-than-muoi-hong-bha-sang-min",
      price: 520000,
      salePrice: 430000,
      stock: 65,
      images: [
        "https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=900&q=80",
        "https://images.unsplash.com/photo-1608248597359-0e6d526a6c18?auto=format&fit=crop&w=900&q=80",
      ],
      description:
        "Sự kết hợp hoàn hảo giữa muối hồng Himalaya mịn nhuyễn, 0.5% BHA (Salicylic Acid) và tinh dầu hoa trà Nhật Bản giúp làm sạch sâu lỗ chân lông, hỗ trợ giảm viêm nang lông và mang lại làn da body mướt mịn.",
      ingredients:
        "Maris Sal (Himalayan Pink Salt), Glycerin, Cocamidopropyl Betaine, Salicylic Acid (BHA), Camellia Japonica Seed Oil, Niacinamide, Tocopheryl Acetate, Rosa Damascena Flower Oil.",
      skinType: "Da dầu",
      volume: "250g",
      isBestSeller: false,
      categoryId: catBodycare.id,
      brandId: brandShiseido.id,
    },
    {
      name: "Nước Hoa Nữ Lancôme La Vie Est Belle Rose Extraordinaire EDP",
      slug: "nuoc-hoa-nu-lancome-la-vie-est-belle-rose-edp",
      price: 3250000,
      salePrice: 2790000,
      stock: 30,
      images: [
        "https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=900&q=80",
        "https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?auto=format&fit=crop&w=900&q=80",
      ],
      description:
        "Biểu tượng hương thơm ngọt ngào, kiêu sa từ bộ ba chiết xuất hoa hồng Damask quý giá quyện cùng hương cam Bergamot thanh mát, hoa Diên Vĩ và xạ hương gỗ ấm áp, lưu hương bền bỉ đến 12 giờ.",
      ingredients:
        "Alcohol, Parfum / Fragrance, Aqua / Water, Rosa Damascena Flower Water,Rosa Damascena Flower Oil, Iris Pallida Root Extract, Linalool, Limonene, Citronellol.",
      skinType: "Mọi loại da",
      volume: "100ml",
      isBestSeller: true,
      categoryId: catPerfume.id,
      brandId: brandLancome.id,
    },
  ];

  for (const item of productsData) {
    const createdProduct = await prisma.product.create({
      data: item,
    });

    await prisma.review.createMany({
      data: [
        {
          rating: 5,
          customerName: "Nguyễn Ngọc Bảo Trâm",
          comment: "Sản phẩm chính hãng, bao bì sang trọng, dùng hợp da và thẩm thấu rất nhanh!",
          productId: createdProduct.id,
        },
        {
          rating: 5,
          customerName: "Trần Minh Anh",
          comment: "Mùi hương dễ chịu, kết cấu mỏng nhẹ, giao hàng cực kỳ nhanh và đóng gói cẩn thận.",
          productId: createdProduct.id,
        },
      ],
    });
  }

  // 4. Tạo 5 Mã giảm giá (Coupons) cho tính năng Voucher & Flash Sale
  await prisma.coupon.createMany({
    data: [
      {
        code: "LUMINA20",
        discountPercent: 20,
        description: "Giảm 20% mừng đại tiệc Flash Sale Lumina Beauty (Áp dụng mọi đơn hàng)",
        minOrderAmount: 0,
        maxDiscount: 500000,
        expiresAt: new Date("2027-12-31T23:59:59.000Z"),
        isActive: true,
      },
      {
        code: "SKINCARE15",
        discountPercent: 15,
        description: "Giảm 15% cho đơn hàng chăm sóc da từ 300.000₫",
        minOrderAmount: 300000,
        maxDiscount: 350000,
        expiresAt: new Date("2027-12-31T23:59:59.000Z"),
        isActive: true,
      },
      {
        code: "FLASHSALE30",
        discountPercent: 30,
        description: "Siêu Voucher Flash Sale giảm 30% cho đơn hàng từ 1.000.000₫",
        minOrderAmount: 1000000,
        maxDiscount: 700000,
        expiresAt: new Date("2027-12-31T23:59:59.000Z"),
        isActive: true,
      },
      {
        code: "NEWMEMBER10",
        discountPercent: 10,
        description: "Ưu đãi 10% dành tặng khách hàng mới",
        minOrderAmount: 0,
        maxDiscount: 250000,
        expiresAt: new Date("2027-12-31T23:59:59.000Z"),
        isActive: true,
      },
      {
        code: "EXPIRED50",
        discountPercent: 50,
        description: "Mã giảm giá sự kiện cũ (Đã hết hạn sử dụng)",
        minOrderAmount: 0,
        maxDiscount: 500000,
        expiresAt: new Date("2025-01-01T00:00:00.000Z"),
        isActive: true,
      },
    ],
  });

  console.log("✅ Đã khởi tạo thành công: 4 Danh mục, 4 Thương hiệu, 12 Sản phẩm, 24 Đánh giá và 5 Mã giảm giá (Coupons)!");
}

main()
  .catch((e) => {
    console.error("❌ Lỗi khi seed dữ liệu:", e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
