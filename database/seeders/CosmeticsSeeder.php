<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CosmeticsSeeder extends Seeder
{
    public function run(): void
    {
        CartItem::query()->delete();
        OrderItem::query()->delete();
        Order::query()->delete();
        Coupon::query()->delete();
        Product::query()->delete();
        Category::query()->delete();
        Brand::query()->delete();
        User::query()->delete();

        // 0. Tạo tài khoản Quản trị viên Chủ Shop (Shop Admin Accounts)
        User::create([
            'name' => 'Chủ Shop Lumina Beauty',
            'username' => 'admin',
            'email' => 'admin@luminabeauty.vn',
            'phone' => '0901000888',
            'address' => 'Showroom 128 Nguyễn Huệ, Quận 1, TP.HCM',
            'password' => Hash::make('Admin@123456'),
            'is_admin' => true,
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Nguyễn Phong (Shop Owner)',
            'username' => 'nguyenphong2705',
            'email' => 'phongdev3979@gmail.com',
            'phone' => '0909270599',
            'address' => 'Showroom Lumina Beauty Quận 1, TP.HCM',
            'password' => Hash::make('Admin@123456'),
            'is_admin' => true,
            'role' => 'admin',
        ]);

        // 0.05. Tạo tài khoản Khách Hàng Mẫu để theo dõi Đơn hàng, Giỏ hàng & Voucher
        $customerUser = User::create([
            'name' => 'Tuyền Thanh Thanh',
            'username' => 'tuyenthanhthanh3979',
            'email' => 'tuyenthanhthanh3979@gmail.com',
            'phone' => '0909397979',
            'address' => '215 Lê Thánh Tôn, Phường Bến Thành, Quận 1, TP. Hồ Chí Minh',
            'password' => Hash::make('Phong2705@@'),
            'is_admin' => false,
            'role' => 'customer',
        ]);

        // 0.1. Tạo các Mã giảm giá (Coupons / Vouchers) dành cho Khách hàng
        $coupons = [
            [
                'code' => 'LUMINA20',
                'discount_percent' => 20,
                'description' => 'Giảm 20% toàn bộ dòng Serum & Kem dưỡng chăm sóc da cao cấp',
                'min_order_amount' => 300000,
                'expires_at' => now()->addMonths(6),
                'is_active' => true,
            ],
            [
                'code' => 'FLASHSALE30',
                'discount_percent' => 30,
                'description' => 'Siêu ưu đãi Flash Sale giảm sâu 30% cho thành viên thân thiết',
                'min_order_amount' => 800000,
                'expires_at' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'SKINCARE15',
                'discount_percent' => 15,
                'description' => 'Giảm 15% cho các sản phẩm phục hồi da nhạy cảm B5 & Ceramide',
                'min_order_amount' => 250000,
                'expires_at' => now()->addMonths(6),
                'is_active' => true,
            ],
            [
                'code' => 'NEWMEMBER10',
                'discount_percent' => 10,
                'description' => 'Quà tặng chào mừng khách hàng mới giảm ngay 10% mọi đơn hàng',
                'min_order_amount' => 0,
                'expires_at' => now()->addYear(),
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $c) {
            Coupon::create($c);
        }

        // 1. Tạo 4 Danh mục (Categories)
        $catSkincare = Category::create([
            'name' => 'Chăm sóc da',
            'slug' => 'cham-soc-da',
            'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80',
        ]);

        $catMakeup = Category::create([
            'name' => 'Trang điểm',
            'slug' => 'trang-diem',
            'image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=800&q=80',
        ]);

        $catBodycare = Category::create([
            'name' => 'Chăm sóc cơ thể',
            'slug' => 'cham-soc-co-the',
            'image' => 'https://images.unsplash.com/photo-1608248597359-0e6d526a6c18?auto=format&fit=crop&w=800&q=80',
        ]);

        $catPerfume = Category::create([
            'name' => 'Nước hoa',
            'slug' => 'nuoc-hoa',
            'image' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=800&q=80',
        ]);

        // 2. Tạo 4 Thương hiệu nổi tiếng (Brands)
        $brandLaRoche = Brand::create([
            'name' => 'La Roche-Posay',
            'logo' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=400&q=80',
            'origin' => 'Pháp',
        ]);

        $brandSulwhasoo = Brand::create([
            'name' => 'Sulwhasoo',
            'logo' => 'https://images.unsplash.com/photo-1612817288484-6f916006741a?auto=format&fit=crop&w=400&q=80',
            'origin' => 'Hàn Quốc',
        ]);

        $brandShiseido = Brand::create([
            'name' => 'Shiseido',
            'logo' => 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=400&q=80',
            'origin' => 'Nhật Bản',
        ]);

        $brandLancome = Brand::create([
            'name' => 'Lancôme',
            'logo' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=400&q=80',
            'origin' => 'Pháp',
        ]);

        // 3. Tạo 12 Sản phẩm mỹ phẩm thực tế
        $products = [
            [
                'category_id' => $catSkincare->id,
                'brand_id' => $brandLaRoche->id,
                'name' => 'Tinh Chất Sáng Da Mờ Thâm Niacinamide 10% + Zinc 1%',
                'slug' => 'tinh-chat-sang-da-mo-tham-niacinamide-10-zinc-1',
                'price' => 420000,
                'sale_price' => 345000,
                'stock' => 85,
                'images' => [
                    'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Tinh chất cô đặc chứa 10% Niacinamide tinh khiết kết hợp 1% Zinc PCA giúp kiểm soát dầu thừa tối ưu, thu nhỏ lỗ chân lông, làm dịu nốt mụn sưng đỏ và cải thiện sắc tố da sáng mịn đều màu sau 4 tuần sử dụng.',
                'ingredients' => 'Aqua (Water), Niacinamide (10%), Pentylene Glycol, Zinc PCA (1%), Dimethyl Isosorbide, Tamarindus Indica Seed Gum, Xanthan Gum, Isoceteth-20, Ethoxydiglycol, Phenoxyethanol, Chlorphenesin.',
                'skin_type' => 'Da dầu',
                'volume' => '30ml',
                'is_best_seller' => true,
            ],
            [
                'category_id' => $catSkincare->id,
                'brand_id' => $brandLaRoche->id,
                'name' => 'Dung Dịch Loại Bỏ Tế Bào Chết BHA 2% Salicylic Acid',
                'slug' => 'dung-dich-loai-bo-te-bao-chet-bha-2-salicylic-acid',
                'price' => 890000,
                'sale_price' => 750000,
                'stock' => 60,
                'images' => [
                    'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Dung dịch tẩy tế bào chết hóa học chứa 2% BHA (Salicylic Acid) thẩm thấu sâu vào lỗ chân lông giúp đẩy mụn ẩn, làm sạch sợi bã nhờn, ngăn ngừa mụn đầu đen và tái tạo bề mặt da láng mịn.',
                'ingredients' => 'Water, Methylpropanediol, Butylene Glycol, Salicylic Acid (BHA 2%), Polysorbate 20, Camellia Oleifera (Green Tea) Leaf Extract, Sodium Hydroxide, Tetrasodium EDTA.',
                'skin_type' => 'Da dầu',
                'volume' => '118ml',
                'is_best_seller' => true,
            ],
            [
                'category_id' => $catSkincare->id,
                'brand_id' => $brandShiseido->id,
                'name' => 'Kem Dưỡng Trẻ Hóa Da Ban Đêm Retinol 0.5% Tinh Khiết',
                'slug' => 'kem-duong-tre-hoa-da-ban-dem-retinol-0-5-tinh-khiet',
                'price' => 1450000,
                'sale_price' => 1190000,
                'stock' => 40,
                'images' => [
                    'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1612817288484-6f916006741a?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Kem dưỡng chống lão hóa chuyên sâu với công thức Retinol 0.5% bọc vi nang kết hợp Peptide và Ceramide giúp kích thích tăng sinh Collagen, làm mờ nếp nhăn li ti và nâng cơ săn chắc mà không gây bong tróc.',
                'ingredients' => 'Aqua, Glycerin, Caprylic/Capric Triglyceride, Retinol (0.5%), Ceramide NP, Niacinamide, Hyaluronic Acid, Tocopherol (Vitamin E), Panthenol, Squalane.',
                'skin_type' => 'Mọi loại da',
                'volume' => '50ml',
                'is_best_seller' => true,
            ],
            [
                'category_id' => $catSkincare->id,
                'brand_id' => $brandLaRoche->id,
                'name' => 'Serum Cấp Ẩm Đa Tầng Hyaluronic Acid 2% + Vitamin B5',
                'slug' => 'serum-cap-am-da-tang-hyaluronic-acid-2-vitamin-b5',
                'price' => 680000,
                'sale_price' => 550000,
                'stock' => 110,
                'images' => [
                    'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Serum cấp nước chuyên sâu với phân tử Hyaluronic Acid đa kích thước kết hợp Pro-Vitamin B5 giúp phục hồi màng bảo vệ da, làm dịu tình trạng khô căng bong tróc và mang lại làn da căng bóng ngậm nước.',
                'ingredients' => 'Aqua (Water), Sodium Hyaluronate, Sodium Hyaluronate Crosspolymer, Panthenol (Vitamin B5), Ahnfeltia Concinna Extract, Glycerin, Pentylene Glycol, Propanediol.',
                'skin_type' => 'Da khô',
                'volume' => '30ml',
                'is_best_seller' => true,
            ],
            [
                'category_id' => $catSkincare->id,
                'brand_id' => $brandSulwhasoo->id,
                'name' => 'Kem Dưỡng Phục Hồi Nhân Sâm Cô Đặc Sulwhasoo Concentrated Ginseng',
                'slug' => 'kem-duong-phuc-hoi-nhan-sam-co-dac-sulwhasoo',
                'price' => 2650000,
                'sale_price' => 2290000,
                'stock' => 25,
                'images' => [
                    'https://images.unsplash.com/photo-1612817288484-6f916006741a?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Tuyệt tác dưỡng da hoàng cung Hàn Quốc chiết xuất từ tinh chất Nhân Sâm cô đặc gấp 6000 lần kết hợp Squalane và Hyaluronic Acid nuôi dưỡng làn da khô lão hóa trở nên hồng hào, săn chắc từ sâu bên trong.',
                'ingredients' => 'Panax Ginseng Root Water, Squalane, Butylene Glycol, Glycerin, Hyaluronic Acid, Niacinamide, Shea Butter, Adenosine, Tocopherol, Honey Extract.',
                'skin_type' => 'Da khô',
                'volume' => '60ml',
                'is_best_seller' => false,
            ],
            [
                'category_id' => $catSkincare->id,
                'brand_id' => $brandLaRoche->id,
                'name' => 'Kem Dưỡng Làm Dịu Phục Hồi Da Nhạy Cảm Cicaplast Baume B5+',
                'slug' => 'kem-duong-lam-diu-phuc-hoi-da-nhay-cam-cicaplast-baume-b5',
                'price' => 495000,
                'sale_price' => 415000,
                'stock' => 150,
                'images' => [
                    'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Kem dưỡng quốc dân dành riêng cho làn da nhạy cảm, kích ứng hoặc sau treatment. Chứa 5% Panthenol (Vitamin B5) và Madecassoside chiết xuất rau má giúp làm dịu vết đỏ tức thì và tái tạo hàng rào ẩm tự nhiên.',
                'ingredients' => 'Aqua, Hydrogenated Polyisobutene, Dimethicone, Glycerin, Butyrospermum Parkii Butter, Panthenol (5%), Madecassoside, Tribioma, Zinc Gluconate, Silica.',
                'skin_type' => 'Da nhạy cảm',
                'volume' => '100ml',
                'is_best_seller' => true,
            ],
            [
                'category_id' => $catMakeup->id,
                'brand_id' => $brandSulwhasoo->id,
                'name' => 'Phấn Nước Trang Điểm Căng Bóng Sulwhasoo Perfecting Cushion',
                'slug' => 'phan-nuoc-trang-diem-cang-bong-sulwhasoo-perfecting-cushion',
                'price' => 1350000,
                'sale_price' => 1150000,
                'stock' => 45,
                'images' => [
                    'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Phấn nước cao cấp kết hợp dưỡng chất skincare giúp che phủ hoàn hảo khuyết điểm suốt 24 giờ mà vẫn giữ lớp nền mỏng nhẹ, căng bóng tự nhiên chuẩn Hàn Quốc cùng chỉ số chống nắng SPF50+ PA+++.',
                'ingredients' => 'Water, Titanium Dioxide, Cyclopentasiloxane, Ethylhexyl Methoxycinnamate, Niacinamide, Hyaluronic Acid, Schizophyllum Commune Mycelium Ferment Filtrate, Adenosine.',
                'skin_type' => 'Mọi loại da',
                'volume' => '15g x 2',
                'is_best_seller' => true,
            ],
            [
                'category_id' => $catMakeup->id,
                'brand_id' => $brandLancome->id,
                'name' => 'Son Thỏi Lì Mịn Môi Lancôme L\'Absolu Rouge Drama Matte',
                'slug' => 'son-thoi-li-min-moi-lancome-labsolu-rouge-drama-matte',
                'price' => 980000,
                'sale_price' => 860000,
                'stock' => 70,
                'images' => [
                    'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Son thỏi lì cao cấp sắc hồng đất thời thượng, bổ sung chiết xuất hoa hồng Grand Rose và Hyaluronic Acid giúp đôi môi lên màu chuẩn sắc ngay từ lần chạm đầu tiên, mềm mượt không gây khô môi.',
                'ingredients' => 'Dimethicone, Bis-Diglyceryl Polyacyladipate-2, Hydrogenated Polyisobutene, Rosa Centifolia Flower Extract, Hyaluronic Acid, Simmondsia Chinensis (Jojoba) Seed Oil, Tocopherol.',
                'skin_type' => 'Mọi loại da',
                'volume' => '3.4g',
                'is_best_seller' => false,
            ],
            [
                'category_id' => $catMakeup->id,
                'brand_id' => $brandShiseido->id,
                'name' => 'Kem Nền Mỏng Nhẹ Bắt Sáng Shiseido Synchro Skin Radiant Lifting',
                'slug' => 'kem-nen-mong-nhe-bat-sang-shiseido-synchro-skin',
                'price' => 1290000,
                'sale_price' => 1090000,
                'stock' => 50,
                'images' => [
                    'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Ứng dụng công nghệ điều chỉnh ánh sáng thông minh từ Nhật Bản, giúp che phủ nếp nhăn và vùng da không đều màu, đồng thời khóa ẩm suốt 24 giờ cho làn da khô luôn rạng rỡ.',
                'ingredients' => 'Water, Dimethicone, Titanium Dioxide, Glycerin, Niacinamide, Citrus Unshiu Peel Extract, Fagus Sylvatica Bud Extract, Sodium Acetylated Hyaluronate.',
                'skin_type' => 'Da khô',
                'volume' => '30ml',
                'is_best_seller' => false,
            ],
            [
                'category_id' => $catBodycare->id,
                'brand_id' => $brandLaRoche->id,
                'name' => 'Sữa Dưỡng Thể Trắng Sáng Niacinamide & Bơ Hạt Mỡ Lipikar Lait',
                'slug' => 'sua-duong-the-trang-sang-niacinamide-bo-hat-mo-lipikar',
                'price' => 560000,
                'sale_price' => 465000,
                'stock' => 90,
                'images' => [
                    'https://images.unsplash.com/photo-1608248597359-0e6d526a6c18?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Sữa dưỡng thể giàu Niacinamide tinh khiết và 10% Bơ Hạt Mỡ (Shea Butter) giúp nuôi dưỡng làn da cơ thể mềm mại, dưỡng sáng đều màu và làm dịu cảm giác khô ngứa trên da nhạy cảm suốt 48 giờ.',
                'ingredients' => 'Aqua, Butyrospermum Parkii Butter (10%), Glycerin, Sorbitan Stearate, Paraffinum Liquidum, Niacinamide (4%), Brassica Campestris Oleifera Oil, Panthenol.',
                'skin_type' => 'Da nhạy cảm',
                'volume' => '400ml',
                'is_best_seller' => false,
            ],
            [
                'category_id' => $catBodycare->id,
                'brand_id' => $brandShiseido->id,
                'name' => 'Tẩy Tế Bào Chết Toàn Thân Muối Hồng & BHA Sáng Mịn Cơ Thể',
                'slug' => 'tay-te-bao-chet-toan-than-muoi-hong-bha-sang-min',
                'price' => 520000,
                'sale_price' => 430000,
                'stock' => 65,
                'images' => [
                    'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1608248597359-0e6d526a6c18?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Sự kết hợp hoàn hảo giữa muối hồng Himalaya mịn nhuyễn, 0.5% BHA (Salicylic Acid) và tinh dầu hoa trà Nhật Bản giúp làm sạch sâu lỗ chân lông, hỗ trợ giảm viêm nang lông và mang lại làn da body mướt mịn.',
                'ingredients' => 'Maris Sal (Himalayan Pink Salt), Glycerin, Cocamidopropyl Betaine, Salicylic Acid (BHA), Camellia Japonica Seed Oil, Niacinamide, Tocopheryl Acetate, Rosa Damascena Flower Oil.',
                'skin_type' => 'Da dầu',
                'volume' => '250g',
                'is_best_seller' => false,
            ],
            [
                'category_id' => $catPerfume->id,
                'brand_id' => $brandLancome->id,
                'name' => 'Nước Hoa Nữ Lancôme La Vie Est Belle Rose Extraordinaire EDP',
                'slug' => 'nuoc-hoa-nu-lancome-la-vie-est-belle-rose-edp',
                'price' => 3250000,
                'sale_price' => 2790000,
                'stock' => 30,
                'images' => [
                    'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Biểu tượng hương thơm ngọt ngào, kiêu sa từ bộ ba chiết xuất hoa hồng Damask quý giá quyện cùng hương cam Bergamot thanh mát, hoa Diên Vĩ và xạ hương gỗ ấm áp, lưu hương bền bỉ đến 12 giờ.',
                'ingredients' => 'Alcohol, Parfum / Fragrance, Aqua / Water, Rosa Damascena Flower Water, Rosa Damascena Flower Oil, Iris Pallida Root Extract, Linalool, Limonene, Citronellol.',
                'skin_type' => 'Mọi loại da',
                'volume' => '100ml',
                'is_best_seller' => true,
            ],
        ];

        $createdProducts = [];
        foreach ($products as $index => $item) {
            $item['sku'] = 'LMN-SP' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
            $item['stock'] = 20;
            $createdProducts[] = Product::create($item);
        }

        // 4. Tạo Giỏ hàng đã lưu từ trước (Saved Cart Items) cho tài khoản khách hàng mẫu tuyenthanhthanh3979
        CartItem::create([
            'user_id' => $customerUser->id,
            'product_id' => $createdProducts[0]->id, // Tinh Chất Niacinamide 10%
            'quantity' => 2,
        ]);
        CartItem::create([
            'user_id' => $customerUser->id,
            'product_id' => $createdProducts[5]->id, // Kem Dưỡng Cicaplast Baume B5+
            'quantity' => 1,
        ]);
        CartItem::create([
            'user_id' => $customerUser->id,
            'product_id' => $createdProducts[7]->id, // Son Thỏi Lancôme L'Absolu Rouge
            'quantity' => 1,
        ]);

        // 5. Tạo các Đơn hàng mẫu gắn với tài khoản khách hàng tuyenthanhthanh3979 & khách lẻ
        $orderCustomer1 = Order::create([
            'user_id' => $customerUser->id,
            'customer_name' => $customerUser->name,
            'phone' => $customerUser->phone,
            'address' => $customerUser->address,
            'note' => 'Giao giờ hành chính, gọi trước khi giao 15 phút',
            'payment_method' => 'COD',
            'coupon_code' => 'LUMINA20',
            'discount_amount' => 248000,
            'total_amount' => 992000,
            'status' => 'COMPLETED',
        ]);
        OrderItem::create([
            'order_id' => $orderCustomer1->id,
            'product_id' => $createdProducts[0]->id,
            'quantity' => 2,
            'price' => 345000,
        ]);
        OrderItem::create([
            'order_id' => $orderCustomer1->id,
            'product_id' => $createdProducts[3]->id,
            'quantity' => 1,
            'price' => 550000,
        ]);

        $orderCustomer2 = Order::create([
            'user_id' => $customerUser->id,
            'customer_name' => $customerUser->name,
            'phone' => $customerUser->phone,
            'address' => $customerUser->address,
            'note' => 'Tặng kèm mẫu thử kem chống nắng La Roche-Posay',
            'payment_method' => 'BANK_TRANSFER',
            'coupon_code' => 'SKINCARE15',
            'discount_amount' => 285000,
            'total_amount' => 1615000,
            'status' => 'SHIPPING',
        ]);
        OrderItem::create([
            'order_id' => $orderCustomer2->id,
            'product_id' => $createdProducts[1]->id,
            'quantity' => 1,
            'price' => 750000,
        ]);
        OrderItem::create([
            'order_id' => $orderCustomer2->id,
            'product_id' => $createdProducts[6]->id,
            'quantity' => 1,
            'price' => 1150000,
        ]);

        $order3 = Order::create([
            'user_id' => null,
            'customer_name' => 'Lê Hoàng Bảo Trâm',
            'phone' => '0918888999',
            'address' => '254 Cầu Giấy, Quận Cầu Giấy, Hà Nội',
            'note' => 'Đóng gói hộp quà tặng kèm thiệp sinh nhật',
            'payment_method' => 'COD',
            'coupon_code' => null,
            'discount_amount' => 0,
            'total_amount' => 2790000,
            'status' => 'PENDING',
        ]);
        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $createdProducts[11]->id,
            'quantity' => 1,
            'price' => 2790000,
        ]);
    }
}
