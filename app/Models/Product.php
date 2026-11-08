<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'category_id',
        'brand_id',
        'name',
        'slug',
        'price',
        'sale_price',
        'stock',
        'images',
        'description',
        'ingredients',
        'skin_type',
        'volume',
        'is_best_seller',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_best_seller' => 'boolean',
            'price' => 'integer',
            'sale_price' => 'integer',
            'stock' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getEffectivePriceAttribute(): int
    {
        return $this->sale_price ?? $this->price;
    }

    public function getFinalPriceAttribute(): int
    {
        return $this->sale_price ?? $this->price;
    }

    public function getProductCodeAttribute(): string
    {
        return $this->sku ?: ('LMN-SP' . str_pad((string) $this->id, 3, '0', STR_PAD_LEFT));
    }

    public const FALLBACK_IMAGE = 'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80';

    public static function sanitizeImageUrl(?string $url): string
    {
        if (!$url || str_contains($url, '1608248597359-0e6d526a6c18')) {
            return self::FALLBACK_IMAGE;
        }

        return $url;
    }

    public function getImagesAttribute($value): array
    {
        $decoded = is_string($value) ? (json_decode($value, true) ?: []) : (is_array($value) ? $value : []);
        if (empty($decoded)) {
            return [self::FALLBACK_IMAGE];
        }

        return array_values(array_map([self::class, 'sanitizeImageUrl'], $decoded));
    }

    public function getFirstImageAttribute(): string
    {
        $imgs = $this->images;
        if (is_array($imgs) && count($imgs) > 0) {
            return self::sanitizeImageUrl($imgs[0]);
        }
        return self::FALLBACK_IMAGE;
    }

    public static function ensureUnder500kProductsSeeded(): void
    {
        if (self::query()->where('sku', 'LMN-SP013')->exists()) {
            return;
        }

        $categories = Category::query()->pluck('id', 'slug');
        $brands = Brand::query()->pluck('id', 'name');
        if ($categories->isEmpty() || $brands->isEmpty()) {
            return;
        }

        $catSkincare = $categories['cham-soc-da'] ?? $categories->first();
        $catMakeup = $categories['trang-diem'] ?? $categories->first();
        $catBodycare = $categories['cham-soc-co-the'] ?? $categories->first();
        $catPerfume = $categories['nuoc-hoa'] ?? $categories->first();

        $brandLaRoche = $brands['La Roche-Posay'] ?? $brands->first();
        $brandSulwhasoo = $brands['Sulwhasoo'] ?? $brands->first();
        $brandShiseido = $brands['Shiseido'] ?? $brands->first();
        $brandLancome = $brands['Lancôme'] ?? $brands->first();

        $newProducts = [
            [
                'sku' => 'LMN-SP013',
                'category_id' => $catSkincare,
                'brand_id' => $brandLaRoche,
                'name' => 'Sữa Rửa Mặt Tạo Bọt Dịu Nhẹ La Roche-Posay Effaclar Purifying Gel',
                'slug' => 'sua-rua-mat-tao-bot-diu-nhe-la-roche-posay-effaclar-gel',
                'price' => 385000,
                'sale_price' => 315000,
                'stock' => 50,
                'images' => [
                    'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Gel rửa mặt tạo bọt không chứa xà phòng với nước khoáng La Roche-Posay và Zinc Pidolate giúp làm sạch sâu bụi bẩn, bã nhờn dư thừa mà vẫn giữ độ pH 5.5 dịu nhẹ cho làn da dầu mụn.',
                'ingredients' => 'Aqua, Sodium Laureth Sulfate, PEG-8, Coco-Betaine, Hexylene Glycol, Sodium Chloride, Zinc PCA, Sodium Hydroxide, Citric Acid.',
                'skin_type' => 'Da dầu',
                'volume' => '200ml',
                'is_best_seller' => true,
            ],
            [
                'sku' => 'LMN-SP014',
                'category_id' => $catSkincare,
                'brand_id' => $brandLaRoche,
                'name' => 'Nước Tẩy Trang Làm Sạch Sâu La Roche-Posay Micellar Water Sensitive',
                'slug' => 'nuoc-tay-trang-lam-sach-sau-la-roche-posay-micellar-water',
                'price' => 430000,
                'sale_price' => 355000,
                'stock' => 60,
                'images' => [
                    'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Công nghệ Glyco-Micellar cuốn sạch 99% lớp trang điểm, kem chống nắng và bụi mịn PM2.5 chỉ sau một lượt bông tẩy trang, bổ sung Glycerin cấp ẩm tức thì cho da nhạy cảm.',
                'ingredients' => 'Aqua / Water, PEG-7 Caprylic/Capric Glycerides, Poloxamer 124, Poloxamer 184, PEG-6 Caprylic/Capric Glycerides, Glycerin, Polysorbate 80, Disodium EDTA.',
                'skin_type' => 'Da nhạy cảm',
                'volume' => '400ml',
                'is_best_seller' => true,
            ],
            [
                'sku' => 'LMN-SP015',
                'category_id' => $catSkincare,
                'brand_id' => $brandSulwhasoo,
                'name' => 'Nước Hoa Hồng Cân Bằng Ẩm Sulwhasoo Essential Comfort Balancing Water',
                'slug' => 'nuoc-hoa-hong-can-bang-am-sulwhasoo-essential-comfort',
                'price' => 480000,
                'sale_price' => 395000,
                'stock' => 45,
                'images' => [
                    'https://images.unsplash.com/photo-1612817288484-6f916006741a?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Nước cân bằng kết cấu dạng gel mỏng nhẹ chiết xuất từ hợp chất thảo mộc JAUM Moisturizing Complex™ giúp làm dịu da khô ráp và cấp nước tức thì sau bước rửa mặt.',
                'ingredients' => 'Water, Alcohol Denat., Betaine, Butylene Glycol, Glyceryl Polymethacrylate, Camellia Sinensis Leaf Extract, Liriope Platyphylla Root Extract, Panthenol.',
                'skin_type' => 'Da khô',
                'volume' => '75ml',
                'is_best_seller' => false,
            ],
            [
                'sku' => 'LMN-SP016',
                'category_id' => $catSkincare,
                'brand_id' => $brandLaRoche,
                'name' => 'Xịt Khoáng Làm Dịu & Cấp Nước Tức Thì La Roche-Posay Thermal Water',
                'slug' => 'xit-khoang-lam-diu-cap-nuoc-la-roche-posay-thermal-water',
                'price' => 295000,
                'sale_price' => 235000,
                'stock' => 80,
                'images' => [
                    'https://images.unsplash.com/photo-1617897903246-719242758050?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Nguồn khoáng thiên nhiên giàu Selenium và nguyên tố vi lượng từ Pháp giúp làm dịu vết ửng đỏ, giảm kích ứng do nắng nóng và khóa ẩm nhẹ nhàng suốt cả ngày.',
                'ingredients' => 'Aqua / Thermal Spring Water (100% Pure La Roche-Posay Thermal Spring Water Rich in Selenium).',
                'skin_type' => 'Mọi loại da',
                'volume' => '150ml',
                'is_best_seller' => false,
            ],
            [
                'sku' => 'LMN-SP017',
                'category_id' => $catSkincare,
                'brand_id' => $brandShiseido,
                'name' => 'Kem Chống Nắng Kiềm Dầu Nâng Tông Shiseido Urban Environment SPF50+',
                'slug' => 'kem-chong-nang-kiem-dau-nang-tong-shiseido-urban-spf50',
                'price' => 490000,
                'sale_price' => 425000,
                'stock' => 55,
                'images' => [
                    'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Sữa chống nắng vật lý lai hóa học ứng dụng công nghệ SuperVeil-UV 360™ bảo vệ da toàn diện trước tia UVA/UVB, kiềm dầu suốt 8 giờ và nâng tông hồng hào tự nhiên.',
                'ingredients' => 'Water, Zinc Oxide, Ethylhexyl Methoxycinnamate, Cyclopentasiloxane, Glycerin, Sodium Hyaluronate, Scutellaria Baicalensis Root Extract.',
                'skin_type' => 'Da dầu',
                'volume' => '30ml',
                'is_best_seller' => true,
            ],
            [
                'sku' => 'LMN-SP018',
                'category_id' => $catSkincare,
                'brand_id' => $brandLaRoche,
                'name' => 'Gel Chấm Mụn Giảm Sưng Nhanh BHA & Niacinamide Effaclar Duo+M Mini',
                'slug' => 'gel-cham-mun-giam-sung-bha-niacinamide-effaclar-duo-mini',
                'price' => 340000,
                'sale_price' => 275000,
                'stock' => 70,
                'images' => [
                    'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Gel chấm mụn chuyên biệt với hoạt chất Phylobioma kết hợp Niacinamide, LHA và Salicylic Acid giúp gom cồi mụn nhanh trong 8 giờ và ngăn ngừa vết thâm sau mụn.',
                'ingredients' => 'Aqua, Glycerin, Dimethicone, Isocetyl Stearate, Niacinamide, Salicylic Acid, Capryloyl Salicylic Acid, Zinc PCA, Piroctone Olamine.',
                'skin_type' => 'Da dầu',
                'volume' => '15ml',
                'is_best_seller' => false,
            ],
            [
                'sku' => 'LMN-SP019',
                'category_id' => $catMakeup,
                'brand_id' => $brandLancome,
                'name' => 'Son Dưỡng Môi Có Màu Căng Mọng Lancôme L\'Absolu Mademoiselle Balm',
                'slug' => 'son-duong-moi-co-mau-cang-mong-lancome-mademoiselle-balm',
                'price' => 460000,
                'sale_price' => 380000,
                'stock' => 40,
                'images' => [
                    'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Son dưỡng có màu giàu tinh dầu hoa hồng Pháp và tinh chất bạc hà mát lạnh giúp nuôi dưỡng đôi môi mềm mọng, ửng hồng tươi tắn suốt cả ngày dài.',
                'ingredients' => 'Diisostearyl Malate, Hydrogenated Polyisobutene, Phenyl Trimethicone, Rosa Canina Fruit Oil, Macadamia Integrifolia Seed Oil, Tocopherol.',
                'skin_type' => 'Mọi loại da',
                'volume' => '3.2g',
                'is_best_seller' => false,
            ],
            [
                'sku' => 'LMN-SP020',
                'category_id' => $catMakeup,
                'brand_id' => $brandSulwhasoo,
                'name' => 'Mặt Nạ Ngủ Môi Nhân Sâm Mềm Mượt Sulwhasoo Essential Lip Mask',
                'slug' => 'mat-na-ngu-moi-nhan-sam-mem-muot-sulwhasoo-lip-mask',
                'price' => 390000,
                'sale_price' => 320000,
                'stock' => 50,
                'images' => [
                    'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Mặt nạ ủ môi ban đêm giàu chiết xuất Nhân Sâm Đỏ và bơ Murumuru giúp làm mềm lớp tế bào chết khô nứt, trả lại bờ môi căng mọng và hồng hào mỗi sáng thức dậy.',
                'ingredients' => 'Hydrogenated Polyisobutene, Phytosteryl/Isostearyl/Cetyl/Stearyl/Behenyl Dimer Dilinoleate, Astrocaryum Murumuru Seed Butter, Panax Ginseng Seed Oil.',
                'skin_type' => 'Da khô',
                'volume' => '10g',
                'is_best_seller' => false,
            ],
            [
                'sku' => 'LMN-SP021',
                'category_id' => $catMakeup,
                'brand_id' => $brandShiseido,
                'name' => 'Chì Kẻ Mày Tự Nhiên Lâu Trôi Shiseido Brow InkTrio Chống Nước',
                'slug' => 'chi-ke-may-tu-nhien-lau-troi-shiseido-brow-inktrio',
                'price' => 350000,
                'sale_price' => 285000,
                'stock' => 65,
                'images' => [
                    'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Chì kẻ chân mày 3 trong 1 tích hợp đầu chì mảnh định hình sợi, đầu phấn tán tự nhiên và cọ chải định hình lông mày sắc nét, chống trôi mồ hôi suốt 24 giờ.',
                'ingredients' => 'Behenic Acid, Rhus Succedanea Fruit Wax, Triethylhexanoin, Sucrose Tetrastearate Triacetate, Tocopherol, Iron Oxides.',
                'skin_type' => 'Mọi loại da',
                'volume' => '0.3g',
                'is_best_seller' => false,
            ],
            [
                'sku' => 'LMN-SP022',
                'category_id' => $catBodycare,
                'brand_id' => $brandLaRoche,
                'name' => 'Kem Dưỡng Da Tay & Móng Bơ Hạt Mỡ La Roche-Posay Cicaplast Mains',
                'slug' => 'kem-duong-da-tay-bo-hat-mo-la-roche-posay-cicaplast-mains',
                'price' => 260000,
                'sale_price' => 195000,
                'stock' => 90,
                'images' => [
                    'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Kem dưỡng phục hồi da tay khô nứt với 4% Niacinamide và 30% Glycerin tạo màng chắn vô hình bảo vệ đôi bàn tay mềm mịn, thấm nhanh không nhờn rít.',
                'ingredients' => 'Aqua / Water, Glycerin (30%), Butylene Glycol, Cetearyl Alcohol, Niacinamide (4%), Glyceryl Stearate, Butyrospermum Parkii Butter / Shea Butter.',
                'skin_type' => 'Da nhạy cảm',
                'volume' => '50ml',
                'is_best_seller' => false,
            ],
            [
                'sku' => 'LMN-SP023',
                'category_id' => $catBodycare,
                'brand_id' => $brandShiseido,
                'name' => 'Sữa Tắm Tinh Dầu Hoa Trà Dưỡng Ẩm Shiseido Kuyura Body Care Wash',
                'slug' => 'sua-tam-tinh-dau-hoa-tra-duong-am-shiseido-kuyura',
                'price' => 320000,
                'sale_price' => 255000,
                'stock' => 75,
                'images' => [
                    'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Sữa tắm tạo bọt tơ tằm giàu tinh dầu Hoa Trà Tsubaki Nhật Bản giúp làm sạch dịu nhẹ, lưu lại hương hoa thanh khiết và làn da cơ thể ẩm mịn suốt ngày dài.',
                'ingredients' => 'Water, Lauric Acid, Myristic Acid, Potassium Hydroxide, Glycol Distearate, Camellia Japonica Seed Oil, Sodium Hyaluronate, Glycerin.',
                'skin_type' => 'Mọi loại da',
                'volume' => '350ml',
                'is_best_seller' => false,
            ],
            [
                'sku' => 'LMN-SP024',
                'category_id' => $catPerfume,
                'brand_id' => $brandLancome,
                'name' => 'Xịt Thơm Toàn Thân Hương Hoa Hồng Lancôme Idôle Hair & Body Mist',
                'slug' => 'xit-thom-toan-than-huong-hoa-hong-lancome-idole-body-mist',
                'price' => 495000,
                'sale_price' => 445000,
                'stock' => 60,
                'images' => [
                    'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?auto=format&fit=crop&w=900&q=80',
                    'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=900&q=80',
                ],
                'description' => 'Xịt thơm dưỡng ẩm cho tóc và cơ thể mang nốt hương hoa hồng Isparta thanh tao kết hợp hoa nhài trắng và xạ hương sạch sẽ, tươi mát suốt 6 giờ.',
                'ingredients' => 'Alcohol, Aqua / Water, Parfum / Fragrance, Panthenol, Rosa Damascena Flower Water, Jasminum Grandiflorum Flower Extract, Linalool, Limonene.',
                'skin_type' => 'Mọi loại da',
                'volume' => '50ml',
                'is_best_seller' => true,
            ],
        ];

        foreach ($newProducts as $prod) {
            self::query()->updateOrCreate(['slug' => $prod['slug']], $prod);
        }
    }
}
