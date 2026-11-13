@if(app()->getLocale() === 'en')
<script>
(function () {
    const exactMap = {
        // Navigation & Header
        'Trang chủ': 'Home',
        'Trang Chủ': 'Home',
        'Sản phẩm': 'Products',
        'Sản Phẩm': 'Products',
        'Chăm sóc da': 'Skincare',
        'Chăm Sóc Da': 'Skincare',
        'Tra cứu SĐT': 'Track Order',
        'Tra Cứu Đơn Hàng': 'Track Order',
        '👤 Đăng nhập Khách hàng': '👤 Login / Register',
        '👤 Đăng Nhập / Đăng Ký': '👤 Login / Register',
        'Đăng Nhập / Đăng Ký': 'Login / Register',
        '🛍️ Giỏ hàng': '🛍️ Cart',
        '🛍️ Giỏ Hàng': '🛍️ Cart',
        'Giỏ Hàng Mỹ Phẩm': 'Cosmetics Cart',
        'Xóa': 'Remove',
        'Giỏ hàng của bạn đang trống': 'Your shopping cart is empty',
        'Hãy khám phá các dòng serum, kem dưỡng và son môi chính hãng.': 'Discover our authentic serums, moisturizers, and lipsticks.',
        'Tạm tính:': 'Subtotal:',
        'Tiến Hành Thanh Toán →': 'Proceed to Checkout →',
        'Xem giỏ hàng & Thanh toán →': 'View Cart & Checkout →',

        // Home Page
        '✦ BỘ SƯU TẬP DƯỠNG DA 2026 — GLOW & RADIANCE': '✦ 2026 SKINCARE COLLECTION — GLOW & RADIANCE',
        'Thuần Khiết & Rạng Ngời': 'Pure & Radiant Glow',
        'Khám phá quy trình chăm sóc da chuẩn khoa học với Niacinamide 10%, BHA 2%, Retinol 0.5% tinh khiết và Hyaluronic Acid đa tầng từ những thương hiệu danh tiếng hàng đầu Pháp, Hàn Quốc và Nhật Bản.': 'Discover a science-backed skincare routine featuring pure 10% Niacinamide, 2% BHA, 0.5% Retinol, and multi-molecular Hyaluronic Acid from world-leading French, Korean, and Japanese brands.',
        'Khám Phá Bộ Sưu Tập →': 'Explore Collection →',
        'Chọn Mỹ Phẩm Theo Loại Da': 'Shop by Skin Type',
        '✓ 100% Chính hãng': '✓ 100% Authentic',
        '✓ Kiểm nghiệm da liễu': '✓ Dermatologist Tested',
        '✓ Đổi trả 14 ngày': '✓ 14-Day Returns',
        'Best Seller Tuần Này': 'Weekly Best Seller',
        'Serum Hyaluronic Acid 2% + B5 Cấp Ẩm Đa Tầng': 'Multi-Depth Hydrating Hyaluronic Acid 2% + B5 Serum',
        'Phục hồi màng ẩm tự nhiên chỉ sau 7 ngày': 'Restores natural moisture barrier in just 7 days',
        'Danh Mục Làm Đẹp': 'Beauty Categories',
        'Khám Phá Theo Danh Mục': 'Explore by Category',
        'Xem tất cả danh mục →': 'View all categories →',
        'Được Yêu Thích Nhất': 'Most Loved',
        'Sản Phẩm Bán Chạy (Best Sellers)': 'Best Selling Products',
        'Cá Nhân Hóa Chu Trình Dưỡng Da': 'Personalized Skincare Routine',
        'Chọn Mỹ Phẩm Theo Loại Da Của Bạn': 'Choose Cosmetics for Your Skin Type',
        'Mỗi làn da có một nhu cầu riêng biệt. Hãy chọn tình trạng da để SHINE Beauty gợi ý công thức hoạt chất phù hợp nhất.': 'Every skin has unique needs. Select your skin concern so SHINE Beauty can recommend the ideal active formula.',
        'Tất cả loại da': 'All Skin Types',
        'Bộ sưu tập chăm sóc toàn diện': 'Complete care collection for every skin',
        'Da dầu & Hỗn hợp': 'Oily & Combination Skin',
        'Kiểm soát bã nhờn, thu nhỏ lỗ chân lông (Niacinamide, BHA)': 'Sebum control & pore minimizing (Niacinamide, BHA)',
        'Da khô & Thiếu ẩm': 'Dry & Dehydrated Skin',
        'Cấp ẩm đa tầng, khóa ẩm chuyên sâu (Hyaluronic Acid, Nhân sâm)': 'Multi-layer hydration & deep moisture lock (Hyaluronic Acid, Ginseng)',
        'Da nhạy cảm': 'Sensitive Skin',
        'Làm dịu kích ứng, phục hồi màng ẩm (Vitamin B5, Rau má)': 'Soothing & barrier recovery (Vitamin B5, Centella)',
        'Đối Tác Chính Hãng': 'Official Brand Partners',
        'Thương Hiệu Mỹ Phẩm Hàng Đầu Thế Giới': 'World-Leading Cosmetics Brands',

        // Categories & Skin Types & Origins
        'Trang điểm': 'Makeup',
        'Chăm sóc cơ thể': 'Body Care',
        'Nước hoa': 'Fragrance & Perfume',
        'Tất cả': 'All',
        'Da dầu': 'Oily Skin',
        'Da khô': 'Dry Skin',
        'Mọi loại da': 'All Skin Types',
        'Chăm sóc da chuyên sâu': 'Advanced Skincare',
        'Trang điểm cao cấp': 'Luxury Makeup',
        'Nước hoa chính hãng': 'Authentic Perfume',
        'Theo Loại Da': 'By Skin Type',
        'Da dầu & Lỗ chân lông to': 'Oily Skin & Enlarged Pores',
        'Da nhạy cảm & Phục hồi B5': 'Sensitive Skin & B5 Recovery',
        'Liên Hệ & Hỗ Trợ': 'Contact & Support',
        'Hệ thống phân phối mỹ phẩm cao cấp & chăm sóc da chuẩn khoa học chính hãng từ Pháp, Hàn Quốc và Nhật Bản.': 'Official distributor of luxury cosmetics & science-backed skincare from France, South Korea, and Japan.',
        'Hotline tư vấn da liễu: 1900 6868': 'Dermatology Hotline: 1900 6868',
        'Cửa Hàng: 28 Phạm Văn Chiêu, Phường Thông Tây Hội, Tp. Hồ Chí Minh': 'Store: 28 Pham Van Chieu, Thong Tay Hoi Ward, Ho Chi Minh City',
        'Cam kết 100% sản phẩm chính hãng có tem phụ tiếng Việt.': '100% authentic products guaranteed with official certification labels.',
        'Chúng tôi cam kết cung cấp sản phẩm chính hãng 100%, đi kèm tem phụ tiếng Việt theo đúng quy định.': 'We guarantee 100% authentic products, complete with official Vietnamese sub-labels in full compliance with regulations.',

        // Product Cards & Product Pages
        '+ Thêm Vào Giỏ': '+ Add to Cart',
        '+ Thêm Vào Giỏ Hàng': '+ Add to Cart',
        'Mua Ngay →': 'Buy Now →',
        'Bộ Sưu Tập Chính Hãng': 'Authentic Collection',
        'Tất Cả Sản Phẩm Mỹ Phẩm & Chăm Sóc Da': 'All Cosmetics & Skincare Products',
        'Sử dụng bộ lọc thông minh theo danh mục, thương hiệu, loại da (Da dầu, Da khô, Da nhạy cảm) và khoảng giá để tìm sản phẩm phù hợp nhất.': 'Use our smart filters by category, brand, skin type (Oily, Dry, Sensitive), and price range to find your perfect match.',
        'Bộ Lọc Tìm Kiếm': 'Search Filters',
        'Đặt lại': 'Reset',
        'Tìm theo tên / Hoạt chất': 'Search by Name / Active Ingredient',
        'Danh mục mỹ phẩm': 'Cosmetics Category',
        'Tất cả danh mục': 'All Categories',
        'Thương hiệu': 'Brand',
        'Tất cả thương hiệu': 'All Brands',
        'Loại da phù hợp': 'Suitable Skin Type',
        'Khoảng giá': 'Price Range',
        'Tất cả mức giá': 'All Price Ranges',
        'Dưới 500.000₫': 'Under 500,000₫',
        'Từ 500.000₫ - 1.000.000₫': '500,000₫ - 1,000,000₫',
        'Trên 1.000.000₫': 'Above 1,000,000₫',
        'Áp Dụng Bộ Lọc': 'Apply Filters',
        'Khoảng giá (₫)': 'Price Range (₫)',
        'Sắp xếp': 'Sort By',
        'Mới nhất': 'Newest',
        'Giá tăng dần': 'Price: Low to High',
        'Giá giảm dần': 'Price: High to Low',
        'Tên A → Z': 'Name A → Z',
        'Danh mục không tồn tại': 'Category not found',
        'Danh mục bạn tìm không có trong hệ thống. Hãy chọn danh mục khác hoặc xem toàn bộ mỹ phẩm.': 'The category you are looking for does not exist. Please choose another category or browse all products.',
        'Không thể tải danh sách sản phẩm': 'Unable to load products',
        'Vui lòng kiểm tra kết nối và thử lại.': 'Please check your connection and try again.',
        'Thử lại': 'Retry',
        'Không tìm thấy sản phẩm phù hợp với bộ lọc': 'No products match your selected filters',
        'Hãy thử thay đổi từ khóa tìm kiếm hoặc đặt lại bộ lọc để xem toàn bộ mỹ phẩm.': 'Try changing your search keyword or reset the filters to view all products.',
        'Xem tất cả sản phẩm': 'View All Products',
        'Công dụng nổi bật': 'Key Benefits',
        '🧪 Bảng Thành Phần Chi Tiết (INCI Ingredients)': '🧪 Full INCI Ingredients List',
        'Toàn bộ thành phần:': 'Full Ingredients:',
        'Số lượng:': 'Quantity:',
        'Sản Phẩm Cùng Danh Mục': 'Related Products in Category',

        // Product Names (12 Seeded Products)
        'Tinh Chất Sáng Da Mờ Thâm Niacinamide 10% + Zinc 1%': 'Brightening & Blemish Control Serum Niacinamide 10% + Zinc 1%',
        'Dung Dịch Loại Bỏ Tế Bào Chết BHA 2% Salicylic Acid': 'Pore Clearing Liquid Exfoliant BHA 2% Salicylic Acid',
        'Kem Dưỡng Trẻ Hóa Da Ban Đêm Retinol 0.5% Tinh Khiết': 'Pure Retinol 0.5% Anti-Aging Night Cream',
        'Serum Cấp Ẩm Đa Tầng Hyaluronic Acid 2% + Vitamin B5': 'Multi-Molecular Hyaluronic Acid 2% + Vitamin B5 Hydrating Serum',
        'Kem Dưỡng Phục Hồi Nhân Sâm Cô Đặc Sulwhasoo Concentrated Ginseng': 'Sulwhasoo Concentrated Ginseng Renewing Cream',
        'Kem Dưỡng Làm Dịu Phục Hồi Da Nhạy Cảm Cicaplast Baume B5+': 'Cicaplast Baume B5+ Soothing & Barrier Repair Balm',
        'Phấn Nước Trang Điểm Căng Bóng Sulwhasoo Perfecting Cushion': 'Sulwhasoo Perfecting Glow Cushion SPF50+ PA+++',
        "Son Thỏi Lì Mịn Môi Lancôme L'Absolu Rouge Drama Matte": "Lancôme L'Absolu Rouge Drama Matte Lipstick",
        'Kem Nền Mỏng Nhẹ Bắt Sáng Shiseido Synchro Skin Radiant Lifting': 'Shiseido Synchro Skin Radiant Lifting Foundation',
        'Sữa Dưỡng Thể Trắng Sáng Niacinamide & Bơ Hạt Mỡ Lipikar Lait': 'Lipikar Lait Niacinamide & Shea Butter Brightening Body Lotion',
        'Tẩy Tế Bào Chết Toàn Thân Muối Hồng & BHA Sáng Mịn Cơ Thể': 'Himalayan Pink Salt & BHA Smoothing Body Scrub',
        'Nước Hoa Nữ Lancôme La Vie Est Belle Rose Extraordinaire EDP': 'Lancôme La Vie Est Belle Rose Extraordinaire Eau de Parfum',

        // Product Descriptions
        'Tinh chất cô đặc chứa 10% Niacinamide tinh khiết kết hợp 1% Zinc PCA giúp kiểm soát dầu thừa tối ưu, thu nhỏ lỗ chân lông, làm dịu nốt mụn sưng đỏ và cải thiện sắc tố da sáng mịn đều màu sau 4 tuần sử dụng.': 'Concentrated serum with 10% pure Niacinamide and 1% Zinc PCA to control excess sebum, minimize pores, calm redness, and visibly brighten uneven skin tone in 4 weeks.',
        'Dung dịch tẩy tế bào chết hóa học chứa 2% BHA (Salicylic Acid) thẩm thấu sâu vào lỗ chân lông giúp đẩy mụn ẩn, làm sạch sợi bã nhờn, ngăn ngừa mụn đầu đen và tái tạo bề mặt da láng mịn.': 'Leave-on 2% BHA (Salicylic Acid) chemical exfoliant that penetrates deep into pores to clear Sebaceous filaments, prevent blackheads, and smooth skin texture.',
        'Kem dưỡng chống lão hóa chuyên sâu với công thức Retinol 0.5% bọc vi nang kết hợp Peptide và Ceramide giúp kích thích tăng sinh Collagen, làm mờ nếp nhăn li ti và nâng cơ săn chắc mà không gây bong tróc.': 'Advanced anti-aging night cream with micro-encapsulated 0.5% Retinol, Peptides, and Ceramides to boost collagen production, smooth fine lines, and firm skin without irritation.',
        'Serum cấp nước chuyên sâu với phân tử Hyaluronic Acid đa kích thước kết hợp Pro-Vitamin B5 giúp phục hồi màng bảo vệ da, làm dịu tình trạng khô căng bong tróc và mang lại làn da căng bóng ngậm nước.': 'Deeply hydrating serum with multi-molecular Hyaluronic Acid and Pro-Vitamin B5 to repair the skin barrier, soothe dryness, and deliver plump, dewy skin.',
        'Tuyệt tác dưỡng da hoàng cung Hàn Quốc chiết xuất từ tinh chất Nhân Sâm cô đặc gấp 6000 lần kết hợp Squalane và Hyaluronic Acid nuôi dưỡng làn da khô lão hóa trở nên hồng hào, săn chắc từ sâu bên trong.': 'Luxury Korean royal skincare formulated with 6000x concentrated Ginseng extract, Squalane, and Hyaluronic Acid to deeply nourish, firm, and revitalize dry, aging skin.',
        'Kem dưỡng quốc dân dành riêng cho làn da nhạy cảm, kích ứng hoặc sau treatment. Chứa 5% Panthenol (Vitamin B5) và Madecassoside chiết xuất rau má giúp làm dịu vết đỏ tức thì và tái tạo hàng rào ẩm tự nhiên.': 'Cult-favorite soothing balm for sensitive, irritated, or post-treatment skin. Formulated with 5% Panthenol (Vitamin B5) and Centella Madecassoside to calm redness and rebuild the moisture barrier.',
        'Phấn nước cao cấp kết hợp dưỡng chất skincare giúp che phủ hoàn hảo khuyết điểm suốt 24 giờ mà vẫn giữ lớp nền mỏng nhẹ, căng bóng tự nhiên chuẩn Hàn Quốc cùng chỉ số chống nắng SPF50+ PA+++.': 'Skincare-infused luxury cushion providing 24-hour flawless coverage with a weightless, radiant Korean glass-skin finish and SPF50+ PA+++ UV protection.',
        'Son thỏi lì cao cấp sắc hồng đất thời thượng, bổ sung chiết xuất hoa hồng Grand Rose và Hyaluronic Acid giúp đôi môi lên màu chuẩn sắc ngay từ lần chạm đầu tiên, mềm mượt không gây khô môi.': 'Ultra-matte luxury lipstick in timeless dusty rose, enriched with Grand Rose extracts and Hyaluronic Acid for rich one-swipe pigment and plush, non-drying comfort.',
        'Ứng dụng công nghệ điều chỉnh ánh sáng thông minh từ Nhật Bản, giúp che phủ nếp nhăn và vùng da không đều màu, đồng thời khóa ẩm suốt 24 giờ cho làn da khô luôn rạng rỡ.': 'Powered by Japanese light-adjusting technology to smooth fine lines and uneven tone while locking in 24-hour hydration for a luminous finish.',
        'Sữa dưỡng thể giàu Niacinamide tinh khiết và 10% Bơ Hạt Mỡ (Shea Butter) giúp nuôi dưỡng làn da cơ thể mềm mại, dưỡng sáng đều màu và làm dịu cảm giác khô ngứa trên da nhạy cảm suốt 48 giờ.': 'Nourishing body lotion enriched with pure Niacinamide and 10% Shea Butter to soften, brighten, and soothe dry, sensitive body skin for 48 hours.',
        'Sự kết hợp hoàn hảo giữa muối hồng Himalaya mịn nhuyễn, 0.5% BHA (Salicylic Acid) và tinh dầu hoa trà Nhật Bản giúp làm sạch sâu lỗ chân lông, hỗ trợ giảm viêm nang lông và mang lại làn da body mướt mịn.': 'Fine Himalayan pink salt blended with 0.5% BHA (Salicylic Acid) and Japanese Camellia oil to deeply unclog pores, smooth rough bumps, and leave body skin silky soft.',
        'Biểu tượng hương thơm ngọt ngào, kiêu sa từ bộ ba chiết xuất hoa hồng Damask quý giá quyện cùng hương cam Bergamot thanh mát, hoa Diên Vĩ và xạ hương gỗ ấm áp, lưu hương bền bỉ đến 12 giờ.': 'An iconic, sophisticated floral fragrance blending a trio of precious Damask roses with fresh Bergamot, Iris, and warm woody musk for up to 12 hours of lasting elegance.',

        // Checkout & Order Tracking
        'Đặt Hàng Thành Công': 'Order Placed Successfully',
        'Mã đơn hàng:': 'Order ID:',
        'Số điện thoại:': 'Phone Number:',
        'Địa chỉ nhận hàng:': 'Shipping Address:',
        'Phương thức thanh toán:': 'Payment Method:',
        'Thanh toán khi nhận hàng (COD)': 'Cash on Delivery (COD)',
        'Chuyển khoản ngân hàng': 'Bank Transfer',
        'Chuyển khoản Ngân hàng': 'Bank Transfer',
        'Tổng thanh toán:': 'Total Paid:',
        'Tổng thanh toán': 'Total Amount',
        'Tiếp Tục Mua Sắm': 'Continue Shopping',
        '🔍 Tra Cứu Đơn Hàng Đã Mua': '🔍 Track Your Order',
        '← Tiếp tục chọn mỹ phẩm': '← Continue Shopping',
        'Thanh Toán & Giao Hàng': 'Checkout & Shipping',
        'Giỏ hàng của bạn hiện chưa có sản phẩm nào': 'Your shopping cart is currently empty',
        'Khám phá sản phẩm ngay': 'Explore Products Now',
        '1. Thông Tin Người Nhận': '1. Recipient Information',
        'Họ và tên người nhận *': 'Recipient Full Name *',
        'Số điện thoại liên hệ *': 'Contact Phone Number *',
        'Địa chỉ giao hàng chi tiết *': 'Detailed Shipping Address *',
        'Ghi chú đơn hàng (Tùy chọn)': 'Order Notes (Optional)',
        '2. Phương Thức Thanh Toán': '2. Payment Method',
        'Kiểm tra tem niêm phong mỹ phẩm trước khi thanh toán': 'Inspect product seal upon delivery before paying',
        'Tặng kèm bộ sample mặt nạ dưỡng ẩm cao cấp': 'Includes a complimentary luxury hydrating mask sample set',
        '🎟️ Mã Giảm Giá / Voucher': '🎟️ Promo Code / Voucher',
        'Gỡ': 'Remove',
        'Áp dụng': 'Apply',
        'Tạm tính': 'Subtotal',
        'Phí vận chuyển': 'Shipping Fee',
        'Miễn phí': 'Free',
        'Dành Cho Khách Mua Hàng': 'Customer Order Tracking',
        'Tra Cứu Trạng Thái Đơn Hàng': 'Track Your Order Status',
        'Nhập số điện thoại đặt hàng để kiểm tra tiến độ xác nhận và giao hàng': 'Enter your order phone number to check confirmation and delivery progress',
        '🔍 Tra Cứu Đơn Hàng': '🔍 Track Order',

        // Customer Login & Account
        '👤 Cổng Đăng Nhập Khách Hàng': '👤 Customer Login Portal',
        'Theo dõi đơn hàng, giỏ hàng đã lưu trước đây & kho voucher ưu đãi': 'Track orders, saved shopping cart & exclusive shop vouchers',
        'Thành Viên SHINE Beauty': 'SHINE Beauty Member',
        'Đăng Nhập Khách Hàng': 'Customer Login',
        'Đăng Ký Tài Khoản Mới': 'Create New Account',
        '🔑 Đăng Nhập': '🔑 Login',
        '✨ Đăng Ký Mới': '✨ Sign Up',
        '🔑 Mở Popup Đăng Nhập': '🔑 Open Login Popup',
        '✨ Mở Popup Đăng Ký Mới': '✨ Open Sign Up Popup',
        'Chưa có tài khoản thành viên?': 'New to SHINE Beauty?',
        'Đăng ký tài khoản mới →': 'Create a new account →',
        'Đã có tài khoản thành viên?': 'Already have an account?',
        '← Quay lại Đăng nhập': '← Back to Login',
        'Tạo Tài Khoản Khách Hàng Mới →': 'Create Customer Account →',
        'Họ và tên *': 'Full Name *',
        'Tên đăng nhập *': 'Username *',
        'Số điện thoại *': 'Phone Number *',
        'Địa chỉ nhận hàng': 'Shipping Address',
        'Tên đăng nhập hoặc Email *': 'Username or Email *',
        'Mật khẩu *': 'Password *',
        'Tự động lưu giỏ hàng & phiên đăng nhập': 'Automatically save cart & keep me signed in',
        'Đồng bộ PostgreSQL': 'PostgreSQL Synced',
        'Quên Mật Khẩu?': 'Forgot Password?',
        '← Quay lại trang Đăng Nhập / Đăng Ký': '← Back to Login / Register',
        'Bảo Mật Tài Khoản Khách Hàng': 'Customer Account Security',
        'Bước 1 / 2': 'Step 1 / 2',
        'Nhận Mã Khôi Phục Mật Khẩu': 'Request Password Reset Code',
        'Email hoặc Số điện thoại đăng ký *': 'Registered Email or Phone Number *',
        'Phương thức nhận mã xác thực (OTP)': 'Verification Code (OTP) Delivery Channel',
        '📧📱 Gửi đồng thời qua cả Email & SMS Số điện thoại': '📧📱 Send via both Email & SMS Phone Number',
        '📱 Nhận mã OTP qua tin nhắn Số điện thoại (SMS)': '📱 Receive OTP via SMS Phone Number',
        '📧 Nhận mã OTP qua hộp thư Email': '📧 Receive OTP via Email Inbox',
        'Gửi Mã Xác Thực Về SĐT & Email →': 'Send OTP Code to Phone & Email →',
        'Gửi Lại Mã Xác Thực Mới →': 'Resend New OTP Code →',
        'Bước 2 / 2': 'Step 2 / 2',
        'Xác Thực Mã & Tạo Mật Khẩu Mới': 'Verify Code & Create New Password',
        'Mã xác thực 6 số (OTP) *': '6-Digit Verification Code (OTP) *',
        'Mật khẩu mới (tối thiểu 6 ký tự) *': 'New Password (min 6 chars) *',
        'Xác nhận mật khẩu mới *': 'Confirm New Password *',
        'Xác Nhận & Tạo Mật Khẩu Mới →': 'Verify & Set New Password →',
        'Điền nhanh': 'Autofill',
        'Đăng Nhập Vào Tài Khoản Khách Hàng →': 'Sign In to Customer Account →',
        '🎁 Tài khoản Khách hàng Mẫu (Đã nạp sẵn Đơn hàng & Giỏ hàng cũ)': '🎁 Demo Customer Account (Pre-loaded with Orders & Saved Cart)',
        'Click để điền': 'Click to autofill',
        'Cách 1: Đăng nhập bằng Username': 'Option 1: Sign in with Username',
        'Cách 2: Đăng nhập bằng Email': 'Option 2: Sign in with Email',
        'Đặc Quyền Tài Khoản Khách Hàng': 'Customer Account Benefits',
        'Tiện Ích Khi Đăng Nhập': 'Member Perks',
        'Chưa có tài khoản? Đăng ký mới': 'New to Lumina? Create an Account',
        'Tạo Tài Khoản Khách Hàng Mới': 'Create Customer Account',
        'Khách Hàng Thân Thiết': 'VIP Member',
        '🛍️ Tiếp tục mua sắm': '🛍️ Continue Shopping',
        'Đăng xuất tài khoản': 'Sign Out',
        'Giỏ Hàng Đã Lưu Trước Đây': 'Previously Saved Cart',
        'Đơn Hàng Đã Đặt': 'Placed Orders',
        'Kho Voucher Của Shop': 'Available Shop Vouchers',
        'Sản Phẩm Đã Từng Mua': 'Previously Purchased',
        'Đặt mua lại nhanh với 1 click': '1-click quick reorder',
        'Đồng Bộ Tự Động Với Tài Khoản': 'Auto-Synced With Your Account',
        'Thanh Toán Ngay →': 'Checkout Now →',
        'Tiến Hành Đặt Hàng Ngay →': 'Proceed to Checkout →',
        'Tổng giá trị giỏ hàng đã lưu:': 'Total saved cart value:',
        'Ưu Đãi Dành Cho Tài Khoản Khách Hàng': 'Exclusive Member Vouchers',
        'Bấm "Áp dụng ngay" để gắn mã giảm giá trực tiếp vào đơn thanh toán của bạn': 'Click "Apply Now" to attach the promo code directly to your checkout',
        'Áp dụng ngay': 'Apply Now',
        '✓ Đang dùng': '✓ Applied',
        'Lịch Sử Mua Sắm & Vận Chuyển': 'Order & Delivery History',
        '⏳ Chờ Shop Xác Nhận': '⏳ Pending Confirmation',
        '✓ Shop Đã Xác Nhận': '✓ Confirmed by Shop',
        '🚚 Đang Giao Hàng Đến Bạn': '🚚 Out for Delivery',
        '✨ Giao Thành Công': '✨ Completed',
        '✕ Đã Hủy': '✕ Cancelled',
        '+ Mua lại': '+ Buy Again',
        'Tủ Đồ Làm Đẹp Của Bạn': 'Your Beauty Wardrobe',
        '💄 Sản Phẩm Bạn Đã Mua & Yêu Thích': '💄 Purchased & Favorite Products',
        'Xem toàn bộ cửa hàng →': 'View Full Store →'
    };

    const patternReplacements = [
        [/^Khám phá \((\d+) sản phẩm\) →$/, 'Explore ($1 products) →'],
        [/^Xem toàn bộ (\d+) sản phẩm →$/, 'View all $1 products →'],
        [/^Xuất xứ:\s*(.+)$/, function (_, origin) {
            const oMap = { 'Pháp': 'France', 'Hàn Quốc': 'South Korea', 'Nhật Bản': 'Japan' };
            return 'Origin: ' + (oMap[origin.trim()] || origin);
        }],
        [/^Kho:\s*(\d+)\s*SP$/, 'Stock: $1 pcs'],
        [/^• Kho:\s*(\d+)\s*SP$/, '• Stock: $1 pcs'],
        [/^Tồn kho tiêu chuẩn:\s*(\d+)\s*sản phẩm$/, 'Standard Stock: $1 units'],
        [/^Mã SP:\s*(.+)$/, 'SKU: $1'],
        [/^Loại da:\s*(.+)$/, function (_, skin) {
            const sMap = { 'Da dầu': 'Oily Skin', 'Da khô': 'Dry Skin', 'Da nhạy cảm': 'Sensitive Skin', 'Mọi loại da': 'All Skin Types' };
            return 'Skin Type: ' + (sMap[skin.trim()] || skin);
        }],
        [/^Dung tích:\s*(.+)$/, 'Volume: $1'],
        [/^Tiết kiệm\s*(\d+)%$/, 'Save $1%'],
        [/^(\d+)\s*sản phẩm đã chọn$/, '$1 items selected'],
        [/^Hiển thị\s*(\d+)\s*sản phẩm phù hợp$/, 'Showing $1 matching products'],
        [/^Hoàn Tất Đặt Hàng\s*\((.+)\)$/, 'Complete Order ($1)'],
        [/^Chi Tiết Giỏ Hàng\s*\((\d+)\)$/, 'Cart Summary ($1)'],
        [/^Giảm giá Voucher\s*\((.+)\)$/, 'Voucher Discount ($1)'],
        [/^Cảm Ơn\s+(.+)\s+Đã Mua Sắm Tại SHINE Beauty!$/, 'Thank You $1 for Shopping at SHINE Beauty!'],
        [/^Tìm thấy\s+(\d+)\s+đơn hàng của SĐT\s+(.+)$/, 'Found $1 orders for phone number $2'],
        [/^Trạng thái:\s*(.+)$/, 'Status: $1'],
        [/^(\d+)\s*sản phẩm$/, '$1 items'],
        [/^(\d+)\s*đơn hàng$/, '$1 orders'],
        [/^(\d+)\s*mã ưu đãi$/, '$1 vouchers'],
        [/^(\d+)\s*dòng mỹ phẩm$/, '$1 products'],
        [/^Tạm tính:\s*(.+)$/, 'Subtotal: $1'],
        [/^Giảm tối đa đến\s*(\d+)%$/, 'Save up to $1%'],
        [/^🛒 Giỏ Hàng Đã Lưu Từ Trước\s*\((\d+)\s*loại SP\)$/, '🛒 Saved Shopping Cart ($1 items)'],
        [/^🎟️ Kho Voucher Hiện Có\s*\((\d+)\)$/, '🎟️ Available Vouchers ($1)'],
        [/^📦 Đơn Hàng Của Tôi\s*\((\d+)\)$/, '📦 My Orders ($1)'],
        [/^Giảm\s*(\d+)%$/, '$1% OFF'],
        [/^Tất cả\s*\((\d+)\)$/, 'All ($1)'],
        [/^Chờ xác nhận\s*\((\d+)\)$/, 'Pending ($1)'],
        [/^Đã xác nhận\s*\((\d+)\)$/, 'Confirmed ($1)'],
        [/^Đang giao\s*\((\d+)\)$/, 'Shipping ($1)'],
        [/^Hoàn thành\s*\((\d+)\)$/, 'Completed ($1)'],
        [/^Đã hủy\s*\((\d+)\)$/, 'Cancelled ($1)'],
        [/^Đặt lúc:\s*(.+)$/, 'Ordered at: $1'],
        [/\bPháp\b/g, 'France'],
        [/\bHàn Quốc\b/g, 'South Korea'],
        [/\bNhật Bản\b/g, 'Japan'],
        [/\bDa dầu\b/g, 'Oily Skin'],
        [/\bDa khô\b/g, 'Dry Skin'],
        [/\bDa nhạy cảm\b/g, 'Sensitive Skin'],
        [/\bMọi loại da\b/g, 'All Skin Types']
    ];

    const placeholderMap = {
        'VD: Niacinamide, BHA, Son...': 'e.g., Niacinamide, BHA, Lipstick...',
        'VD: Nguyễn Thị Minh Châu': 'e.g., Jane Nguyen',
        'VD: 0909 123 456': 'e.g., 0909 123 456',
        'Số nhà, tên đường, Phường/Xã, Quận/Huyện, Tỉnh/Thành phố': 'Street address, Ward, District, City/Province',
        'Ghi chú giao giờ hành chính hoặc gói quà tặng...': 'Delivery notes (office hours, gift wrapping...)',
        'Nhập mã (VD: LUMINA20)': 'Enter code (e.g., LUMINA20)',
        'Nhập số điện thoại đặt hàng (VD: 0909123456)...': 'Enter order phone number (e.g., 0909123456)...',
        'Họ và tên *': 'Full Name *',
        'Tên đăng nhập *': 'Username *',
        'Số điện thoại *': 'Phone Number *',
        'Địa chỉ nhận hàng mặc định': 'Default Shipping Address',
        'Mật khẩu (tối thiểu 6 ký tự) *': 'Password (min 6 chars) *'
    };

    function translateText(raw) {
        const trimmed = raw.trim();
        if (!trimmed) return raw;
        if (exactMap[trimmed]) {
            return raw.replace(trimmed, exactMap[trimmed]);
        }
        let updated = trimmed;
        for (const [regex, replacement] of patternReplacements) {
            if (regex.test(updated)) {
                updated = updated.replace(regex, replacement);
            }
        }
        if (updated !== trimmed) {
            return raw.replace(trimmed, updated);
        }
        return raw;
    }

    function applyEnglishTranslations() {
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null);
        const nodes = [];
        while (walker.nextNode()) {
            const node = walker.currentNode;
            const parentTag = node.parentElement ? node.parentElement.tagName : '';
            if (parentTag !== 'SCRIPT' && parentTag !== 'STYLE') {
                nodes.push(node);
            }
        }
        nodes.forEach(node => {
            const nextVal = translateText(node.nodeValue);
            if (nextVal !== node.nodeValue) {
                node.nodeValue = nextVal;
            }
        });

        document.querySelectorAll('input[placeholder], textarea[placeholder]').forEach(el => {
            const ph = el.getAttribute('placeholder');
            if (ph && placeholderMap[ph.trim()]) {
                el.setAttribute('placeholder', placeholderMap[ph.trim()]);
            }
        });
    }

    window.__applyI18n = applyEnglishTranslations;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', applyEnglishTranslations);
    } else {
        applyEnglishTranslations();
    }
})();
</script>
@endif
