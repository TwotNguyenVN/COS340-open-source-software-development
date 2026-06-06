<?php include_once 'app/views/shares/header.php'; ?>

<!-- Custom Home Page Styles -->
<style>
    /* Theme-specific variables for homepage banners */
    :root {
        --carousel-bg-1: #161617;
        --carousel-bg-2: #252527;
        --carousel-bg-3: #0d0d0d;
        --carousel-text: #ffffff;
        --carousel-text-muted: #cccccc;
        
        --deal-bg: #272729;
        --deal-border: rgba(255, 255, 255, 0.08);
        --deal-title: #ffffff;
        --deal-desc: #cccccc;
        --deal-countdown-bg: rgba(255, 255, 255, 0.03);
        --deal-countdown-border: rgba(255, 255, 255, 0.08);
        --deal-benefit-title: #ffffff;
        --countdown-separator-color: rgba(255, 255, 255, 0.2);
    }
    [data-theme="light"] {
        --carousel-bg-1: #fafafc;
        --carousel-bg-2: #f5f5f7;
        --carousel-bg-3: #ffffff;
        --carousel-text: #1d1d1f;
        --carousel-text-muted: #7a7a7a;
        
        --deal-bg: #f5f5f7;
        --deal-border: #e0e0e0;
        --deal-title: #1d1d1f;
        --deal-desc: #7a7a7a;
        --deal-countdown-bg: rgba(0, 0, 0, 0.03);
        --deal-countdown-border: rgba(0, 0, 0, 0.06);
        --deal-benefit-title: #1d1d1f;
        --countdown-separator-color: rgba(0, 0, 0, 0.15);
    }

    /* Apple Design System Premium Homepage Styles */
    .hero-carousel {
        border-radius: 24px;
        overflow: hidden;
        margin-bottom: 3rem;
        border: 1px solid var(--glass-border);
        box-shadow: var(--glass-shadow);
        background: var(--glass-bg);
    }
    .carousel-item {
        height: 480px;
        position: relative;
    }
    .carousel-bg-dark-1 {
        background: var(--carousel-bg-1);
    }
    .carousel-bg-light-2 {
        background: var(--carousel-bg-2);
    }
    .carousel-bg-dark-3 {
        background: var(--carousel-bg-3);
    }
    .carousel-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        padding: 4rem;
        color: var(--carousel-text);
    }
    .carousel-product-img {
        position: absolute;
        right: 8%;
        top: 50%;
        transform: translateY(-50%);
        height: 80%;
        max-width: 45%;
        object-fit: contain;
        /* Apple signature product drop-shadow */
        filter: drop-shadow(rgba(0, 0, 0, 0.22) 3px 5px 30px);
        transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .carousel-item.active .carousel-product-img {
        transform: translateY(-50%) scale(1.03);
    }
    .carousel-glass-content {
        max-width: 50%;
        background: transparent;
        border: none;
        padding: 0;
        border-radius: 0;
        box-shadow: none;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
    }
    .carousel-glass-content h2 {
        font-family: var(--font-display);
        font-weight: 600;
        font-size: 2.8rem;
        margin-bottom: 1rem;
        letter-spacing: -0.02em;
        color: var(--carousel-text);
    }
    .carousel-glass-content p {
        font-size: 1.15rem;
        margin-bottom: 1.5rem;
        line-height: 1.47;
        color: var(--carousel-text-muted);
    }
    
    /* Category Shortcut Section */
    .section-title {
        font-family: var(--font-display);
        font-weight: 600;
        font-size: 2rem;
        letter-spacing: -0.01em;
        margin-bottom: 2rem;
        position: relative;
        display: inline-block;
    }
    .category-shortcut-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 18px; /* rounded.lg */
        padding: 1.5rem;
        text-align: center;
        transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1), background-color 0.2s ease;
        text-decoration: none;
        color: var(--text-main);
        display: block;
        box-shadow: none;
    }
    .category-shortcut-card:hover {
        transform: translateY(-4px);
        background: var(--card-hover-bg);
    }
    .category-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: var(--accent-color);
        display: inline-block;
        transition: transform 0.4s ease;
    }
    .category-shortcut-card:hover .category-icon {
        transform: scale(1.08);
    }
    .category-shortcut-card h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
    }

    /* Product Grid Card Styling (Store Utility Card) */
    .product-grid-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 18px; /* rounded.lg */
        padding: 24px; /* spacing.lg */
        overflow: hidden;
        transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1), background-color 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        box-shadow: none; /* No shadow on cards */
    }
    .product-grid-card:hover {
        transform: translateY(-4px);
        background: var(--card-hover-bg);
    }
    .product-img-wrapper {
        position: relative;
        padding-top: 100%;
        background: transparent;
        overflow: hidden;
        border-radius: 8px; /* rounded.sm for inner image */
        margin-bottom: 16px;
    }
    .product-grid-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 8px;
        /* Apple signature product shadow */
        filter: drop-shadow(rgba(0, 0, 0, 0.22) 3px 5px 30px);
        transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1);
    }
    .product-grid-card:hover .product-grid-img {
        transform: scale(1.03);
    }
    .product-badge {
        position: absolute;
        top: 16px;
        left: 16px;
        z-index: 10;
        padding: 6px 14px;
        border-radius: 980px; /* rounded.pill */
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .badge-featured {
        background: rgba(0, 102, 204, 0.1);
        color: #2997ff;
        border: 1px solid rgba(0, 102, 204, 0.3);
    }
    [data-theme="light"] .badge-featured {
        background: rgba(0, 102, 204, 0.05);
        color: #0066cc;
        border: 1px solid rgba(0, 102, 204, 0.2);
    }
    .badge-deal {
        background: rgba(255, 69, 58, 0.1);
        color: #ff453a;
        border: 1px solid rgba(255, 69, 58, 0.3);
    }
    .badge-new {
        background: rgba(48, 209, 88, 0.1);
        color: #30d158;
        border: 1px solid rgba(48, 209, 88, 0.3);
    }
    [data-theme="light"] .badge-new {
        background: rgba(48, 209, 88, 0.05);
        color: #30d158;
        border: 1px solid rgba(48, 209, 88, 0.2);
    }
    .product-card-body {
        padding: 0;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .product-brand {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
        letter-spacing: 0.5px;
    }
    .product-title {
        font-family: var(--font-text);
        font-size: 17px; /* body-strong */
        font-weight: 600;
        line-height: 1.24;
        margin-bottom: 0.75rem;
        color: var(--text-main);
        text-decoration: none;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.5em;
    }
    .product-title:hover {
        color: var(--accent-color);
    }
    .price-wrapper {
        margin-top: auto;
        display: flex;
        align-items: baseline;
        gap: 8px;
        margin-bottom: 1rem;
    }
    .current-price {
        font-size: 17px;
        font-weight: 600;
        color: #30d158; /* Apple green for dark mode */
    }
    [data-theme="light"] .current-price {
        color: var(--ink); /* Ink for light mode */
    }
    .original-price {
        font-size: 14px;
        text-decoration: line-through;
        color: var(--text-muted);
    }
    .stock-status {
        font-size: 14px;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .btn-buy-now {
        border-radius: 980px; /* rounded.pill */
        padding: 11px 22px;
        font-size: 14px;
        font-weight: 400;
        width: 100%;
        background: var(--accent-color);
        color: #ffffff !important;
        border: none;
        transition: opacity 0.15s ease, transform 0.1s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-buy-now:hover {
        opacity: 0.88;
        color: #ffffff !important;
    }
    .btn-buy-now:active {
        transform: scale(0.95);
    }
 
    /* Hot Deal Special Section Banner */
    .deal-banner {
        background: var(--deal-bg);
        border: 1px solid var(--deal-border);
        border-radius: 28px;
        padding: 3.5rem;
        margin-bottom: 4rem;
        box-shadow: none;
        position: relative;
        overflow: hidden;
        transition: background-color 0.3s, border-color 0.3s;
    }
    .deal-content {
        position: relative;
        z-index: 2;
    }
    .deal-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 69, 58, 0.12);
        border: 1px solid rgba(255, 69, 58, 0.35);
        color: #ff453a;
        padding: 8px 16px;
        border-radius: 100px;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 1.5rem;
    }
    .deal-title {
        font-family: var(--font-display), -apple-system, sans-serif;
        font-weight: 600;
        font-size: 2.8rem;
        line-height: 1.2;
        color: var(--deal-title);
        margin-bottom: 1rem;
        letter-spacing: -1px;
        transition: color 0.3s;
    }
    .deal-title span {
        color: #ff453a;
    }
    .deal-description {
        color: var(--deal-desc);
        font-size: 1.1rem;
        line-height: 1.6;
        max-width: 90%;
        margin-bottom: 2rem;
        transition: color 0.3s;
    }
    .countdown-timer {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 1.5rem;
    }
    .countdown-block {
        background: var(--deal-countdown-bg);
        border: 1px solid var(--deal-countdown-border);
        border-radius: 18px;
        padding: 1.25rem 0.75rem;
        min-width: 95px;
        text-align: center;
        box-shadow: none;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .countdown-block:hover {
        transform: translateY(-2px);
        border-color: rgba(255, 69, 58, 0.3);
    }
    .countdown-num {
        font-family: var(--font-display), -apple-system, sans-serif;
        font-size: 2.5rem;
        font-weight: 600;
        color: #ff453a;
        line-height: 1;
        margin-bottom: 6px;
        letter-spacing: -1px;
    }
    .countdown-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #86868b;
        font-weight: 600;
        letter-spacing: 1.5px;
    }
    .countdown-separator {
        font-size: 2rem;
        font-weight: 700;
        color: var(--countdown-separator-color);
        animation: blink 1s infinite;
        margin-bottom: 22px;
        transition: color 0.3s;
    }
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }
    .deal-benefits {
        display: flex;
        gap: 24px;
        margin-top: 2.5rem;
        border-top: 1px solid var(--deal-border);
        padding-top: 1.5rem;
        transition: border-color 0.3s;
    }
    .benefit-item {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }
    .benefit-item i {
        color: #ff453a;
        background: rgba(255, 69, 58, 0.1);
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }
    .benefit-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--deal-benefit-title);
        margin-bottom: 2px;
        transition: color 0.3s;
    }
    .benefit-desc {
        font-size: 0.75rem;
        color: #86868b;
        line-height: 1.3;
    }
</style>

<div class="container main-container">
    
    <!-- Hero Slideshow Banner -->
    <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="6000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active carousel-bg-dark-1">
                <img src="<?= BASE_URL ?>/public/uploads/iphone_15_pro_max.png" class="carousel-product-img" alt="iPhone 15 Pro Max">
                <div class="carousel-overlay overlay-dark">
                    <div class="carousel-glass-content">
                        <h2>iPhone 15 Pro Max</h2>
                        <p>Thiết kế Titanium bền bỉ, chip A17 Pro mạnh mẽ vượt trội và hệ thống camera đỉnh cao mới nhất.</p>
                        <button onclick="addToCartAjax(event, 1)" class="btn btn-premium">Thêm vào giỏ hàng</button>
                    </div>
                </div>
            </div>
            <div class="carousel-item carousel-bg-light-2">
                <img src="<?= BASE_URL ?>/public/uploads/macbook_air_m2.png" class="carousel-product-img" alt="Macbook Air M2">
                <div class="carousel-overlay overlay-light">
                    <div class="carousel-glass-content">
                        <h2>MacBook Air M2</h2>
                        <p>Siêu mỏng nhẹ, hiệu năng cực đỉnh từ chip Apple M2 cùng thời lượng pin lên đến 18 giờ liên tục.</p>
                        <a href="<?= BASE_URL ?>/Product/show/2" class="btn btn-premium">Khám Phá</a>
                    </div>
                </div>
            </div>
            <div class="carousel-item carousel-bg-dark-3">
                <img src="<?= BASE_URL ?>/public/uploads/ipad_air_5.png" class="carousel-product-img" alt="iPad Air 5">
                <div class="carousel-overlay overlay-dark">
                    <div class="carousel-glass-content">
                        <h2>iPad Air M1</h2>
                        <p>Hiệu năng mạnh mẽ ấn tượng từ vi xử lý M1, màn hình Liquid Retina sắc nét hỗ trợ Apple Pencil.</p>
                        <button onclick="addToCartAjax(event, 4)" class="btn btn-premium">Thêm vào giỏ hàng</button>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Category Grid Shortcuts -->
    <div class="mb-5">
        <h3 class="section-title">Danh Mục Sản Phẩm</h3>
        <div class="row g-4">
            <?php 
            $icons = [
                'Điện thoại' => 'fa-mobile-screen-button',
                'Laptop' => 'fa-laptop',
                'Phụ kiện' => 'fa-headphones',
                'Máy tính bảng' => 'fa-tablet-screen-button'
            ];
            foreach ($categories as $cat): 
                $icon = isset($icons[$cat->name]) ? $icons[$cat->name] : 'fa-box';
            ?>
                <div class="col-6 col-md-3">
                    <a href="<?= BASE_URL ?>/Product?category_id=<?= $cat->id ?>" class="category-shortcut-card">
                        <div class="category-icon">
                            <i class="fa-solid <?= $icon ?>"></i>
                        </div>
                        <h4><?= htmlspecialchars($cat->name) ?></h4>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Hot Deals Section -->
    <?php if (!empty($discountedProducts)): ?>
        <div class="deal-banner">
            <div class="row align-items-center">
                <div class="col-lg-7 deal-content mb-4 mb-lg-0">
                    <span class="deal-badge">
                        <i class="fa-solid fa-fire-flame-curved"></i> Khuyến mãi giới hạn
                    </span>
                    <h2 class="deal-title">Deal <span>Chớp Nhoáng</span></h2>
                    <p class="deal-description">Sở hữu những siêu phẩm công nghệ hàng đầu với mức giá ưu đãi cực khủng tốt nhất thị trường.</p>
                    <div class="countdown-timer">
                        <div class="countdown-block">
                            <div class="countdown-num" id="hours">02</div>
                            <div class="countdown-label">Giờ</div>
                        </div>
                        <div class="countdown-separator">:</div>
                        <div class="countdown-block">
                            <div class="countdown-num" id="minutes">45</div>
                            <div class="countdown-label">Phút</div>
                        </div>
                        <div class="countdown-separator">:</div>
                        <div class="countdown-block">
                            <div class="countdown-num" id="seconds">18</div>
                            <div class="countdown-label">Giây</div>
                        </div>
                    </div>
                    
                    <!-- Deal Benefits to fill space -->
                    <div class="deal-benefits">
                        <div class="benefit-item">
                            <i class="fa-solid fa-truck-fast"></i>
                            <div class="benefit-title">Giao Hỏa Tốc</div>
                            <div class="benefit-desc">Nhận hàng trong 2 giờ nội thành</div>
                        </div>
                        <div class="benefit-item">
                            <i class="fa-solid fa-shield-halved"></i>
                            <div class="benefit-title">Chính Hãng</div>
                            <div class="benefit-desc">Bảo hành chính hãng 12 tháng</div>
                        </div>
                        <div class="benefit-item">
                            <i class="fa-solid fa-arrows-rotate"></i>
                            <div class="benefit-title">1 Đổi 1</div>
                            <div class="benefit-desc">Đổi mới trong 30 ngày nếu có lỗi</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 d-flex justify-content-center align-items-center">
                    <div class="w-100" style="max-width: 380px;">
                        <?php foreach ($discountedProducts as $product): ?>
                            <div class="product-grid-card">
                                <?php 
                                $discountPct = 0;
                                if ($product->price > 0 && $product->sale_price < $product->price) {
                                    $discountPct = round((($product->price - $product->sale_price) / $product->price) * 100);
                                }
                                ?>
                                <?php if ($discountPct > 0): ?>
                                    <div class="product-badge badge-deal">-<?= $discountPct ?>%</div>
                                <?php endif; ?>
                                <div class="product-img-wrapper">
                                    <img src="<?= BASE_URL ?>/<?= !empty($product->image) ? $product->image : 'public/uploads/1779023431_logo.jpg' ?>" class="product-grid-img" alt="<?= htmlspecialchars($product->name) ?>">
                                </div>
                                <div class="product-card-body">
                                    <div class="product-brand"><?= htmlspecialchars($product->brand ?? 'APPLE') ?></div>
                                    <a href="<?= BASE_URL ?>/Product/show/<?= $product->id ?>" class="product-title"><?= htmlspecialchars($product->name) ?></a>
                                    <div class="price-wrapper">
                                        <span class="current-price"><?= number_format($product->sale_price, 0, ',', '.') ?> ₫</span>
                                        <span class="original-price"><?= number_format($product->price, 0, ',', '.') ?> ₫</span>
                                    </div>
                                    <div class="stock-status">
                                        <?php if ($product->stock > 0): ?>
                                            <span class="text-success"><i class="fa-solid fa-circle-check me-1"></i> Còn <?= $product->stock ?> sản phẩm</span>
                                        <?php else: ?>
                                            <span class="text-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Tạm hết hàng</span>
                                        <?php endif; ?>
                                    </div>
                                    <button onclick="addToCartAjax(event, '<?= $product->id ?>')" class="btn-buy-now">
                                        <i class="fa-solid fa-cart-shopping"></i> Thêm vào giỏ hàng
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Featured Products -->
    <?php if (!empty($featuredProducts)): ?>
        <div class="mb-5">
            <h3 class="section-title">Sản Phẩm Nổi Bật</h3>
            <div class="row g-4">
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="product-grid-card">
                            <div class="product-badge badge-featured">Nổi Bật</div>
                            <div class="product-img-wrapper">
                                <img src="<?= BASE_URL ?>/<?= !empty($product->image) ? $product->image : 'public/uploads/1779023431_logo.jpg' ?>" class="product-grid-img" alt="<?= htmlspecialchars($product->name) ?>">
                            </div>
                            <div class="product-card-body">
                                <div class="product-brand"><?= htmlspecialchars($product->brand ?? 'APPLE') ?></div>
                                <a href="<?= BASE_URL ?>/Product/show/<?= $product->id ?>" class="product-title"><?= htmlspecialchars($product->name) ?></a>
                                <div class="price-wrapper">
                                    <?php if ($product->sale_price && $product->sale_price < $product->price): ?>
                                        <span class="current-price"><?= number_format($product->sale_price, 0, ',', '.') ?> ₫</span>
                                        <span class="original-price"><?= number_format($product->price, 0, ',', '.') ?> ₫</span>
                                    <?php else: ?>
                                        <span class="current-price"><?= number_format($product->price, 0, ',', '.') ?> ₫</span>
                                    <?php endif; ?>
                                </div>
                                <div class="stock-status">
                                    <?php if ($product->stock > 0): ?>
                                        <span class="text-success"><i class="fa-solid fa-circle-check me-1"></i> Còn <?= $product->stock ?> sản phẩm</span>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Tạm hết hàng</span>
                                    <?php endif; ?>
                                </div>
                                <button onclick="addToCartAjax(event, '<?= $product->id ?>')" class="btn-buy-now">
                                    <i class="fa-solid fa-cart-shopping"></i> Thêm vào giỏ hàng
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- New Arrivals Section -->
    <?php if (!empty($newArrivals)): ?>
        <div class="mb-5">
            <h3 class="section-title">Hàng Mới Về</h3>
            <div class="row g-4">
                <?php foreach ($newArrivals as $product): ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="product-grid-card">
                            <div class="product-badge badge-new">Mới</div>
                            <div class="product-img-wrapper">
                                <img src="<?= BASE_URL ?>/<?= !empty($product->image) ? $product->image : 'public/uploads/1779023431_logo.jpg' ?>" class="product-grid-img" alt="<?= htmlspecialchars($product->name) ?>">
                            </div>
                            <div class="product-card-body">
                                <div class="product-brand"><?= htmlspecialchars($product->brand ?? 'APPLE') ?></div>
                                <a href="<?= BASE_URL ?>/Product/show/<?= $product->id ?>" class="product-title"><?= htmlspecialchars($product->name) ?></a>
                                <div class="price-wrapper">
                                    <?php if ($product->sale_price && $product->sale_price < $product->price): ?>
                                        <span class="current-price"><?= number_format($product->sale_price, 0, ',', '.') ?> ₫</span>
                                        <span class="original-price"><?= number_format($product->price, 0, ',', '.') ?> ₫</span>
                                    <?php else: ?>
                                        <span class="current-price"><?= number_format($product->price, 0, ',', '.') ?> ₫</span>
                                    <?php endif; ?>
                                </div>
                                <div class="stock-status">
                                    <?php if ($product->stock > 0): ?>
                                        <span class="text-success"><i class="fa-solid fa-circle-check me-1"></i> Còn <?= $product->stock ?> sản phẩm</span>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Tạm hết hàng</span>
                                    <?php endif; ?>
                                </div>
                                <button onclick="addToCartAjax(event, '<?= $product->id ?>')" class="btn-buy-now">
                                    <i class="fa-solid fa-cart-shopping"></i> Thêm vào giỏ hàng
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- Countdown Logic -->
<script>
    (function() {
        // Set countdown to 3 hours from now
        let countdownTime = new Date().getTime() + (3 * 60 * 60 * 1000);
        
        function updateTimer() {
            const now = new Date().getTime();
            const distance = countdownTime - now;
            
            if (distance < 0) {
                // reset to next 3 hours to loop the demo
                countdownTime = new Date().getTime() + (3 * 60 * 60 * 1000);
                return;
            }
            
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            const hElem = document.getElementById('hours');
            const mElem = document.getElementById('minutes');
            const sElem = document.getElementById('seconds');
            
            if (hElem && mElem && sElem) {
                hElem.innerText = String(hours).padStart(2, '0');
                mElem.innerText = String(minutes).padStart(2, '0');
                sElem.innerText = String(seconds).padStart(2, '0');
            }
        }
        
        if (document.getElementById('hours')) {
            updateTimer();
            setInterval(updateTimer, 1000);
        }
    })();
</script>

<?php include_once 'app/views/shares/footer.php'; ?>
