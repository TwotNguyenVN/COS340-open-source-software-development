<?php include 'app/views/shares/header.php'; ?>

<style>
    .detail-img-wrapper {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 350px;
        padding: 2rem;
    }

    .detail-img {
        width: 100%;
        max-height: 450px;
        object-fit: contain;
        transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .detail-img:hover {
        transform: scale(1.05);
    }

    .detail-placeholder {
        color: var(--gray-48);
        text-align: center;
    }

    .info-pane {
        display: flex;
        flex-direction: column;
        height: 100%;
        justify-content: center;
    }

    .detail-price {
        font-family: var(--font-display);
        font-size: 2.25rem;
        font-weight: 600;
        color: #30d158; /* Apple System Green */
        margin: 1rem 0;
        letter-spacing: -0.5px;
    }

    .detail-desc {
        color: var(--text-muted);
        font-size: 1rem;
        line-height: 1.6;
        margin-bottom: 2rem;
        white-space: pre-line;
    }

    [data-theme="light"] .detail-desc {
        color: rgba(29, 29, 31, 0.65);
    }

    .stock-info-block {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 8px 16px;
        border-radius: 980px;
        font-size: 0.9rem;
        font-weight: 500;
        margin-bottom: 1.5rem;
        align-self: flex-start; /* Prevent stretching in flex layout */
    }
    .stock-info-success {
        background: rgba(48, 209, 88, 0.12);
        color: #30d158;
        border: 1px solid rgba(48, 209, 88, 0.3);
    }
    [data-theme="light"] .stock-info-success {
        background: rgba(52, 199, 89, 0.1);
        color: #1a8a3a;
        border: 1px solid rgba(52, 199, 89, 0.3);
    }
    .stock-info-warning {
        background: rgba(255, 159, 10, 0.12);
        color: #ff9f0a;
        border: 1px solid rgba(255, 159, 10, 0.3);
    }
    [data-theme="light"] .stock-info-warning {
        background: rgba(255, 149, 0, 0.1);
        color: #b86e00;
        border: 1px solid rgba(255, 149, 0, 0.3);
    }
    .stock-info-danger {
        background: rgba(255, 69, 58, 0.12);
        color: #ff453a;
        border: 1px solid rgba(255, 69, 58, 0.3);
    }
    [data-theme="light"] .stock-info-danger {
        background: rgba(255, 59, 48, 0.1);
        color: #c0392b;
        border: 1px solid rgba(255, 59, 48, 0.3);
    }
</style>

<div class="row mb-3">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/Product" class="text-muted text-decoration-none">Sản phẩm</a></li>
                <li class="breadcrumb-item active" style="color: var(--text-main);" aria-current="page"><?php echo htmlspecialchars($product->name); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="glass-card">
    <div class="row g-5">
        <!-- Image Side -->
        <div class="col-md-5">
            <div class="detail-img-wrapper p-3">
                <?php if (!empty($product->image) && file_exists($product->image)): ?>
                    <img src="<?php echo BASE_URL . '/' . $product->image; ?>" alt="<?php echo htmlspecialchars($product->name); ?>" class="detail-img">
                <?php else: ?>
                    <div class="detail-placeholder">
                        <i class="fa-regular fa-image fs-1 mb-3"></i>
                        <h5>Sản phẩm chưa có hình ảnh</h5>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info Side -->
        <div class="col-md-7">
            <div class="info-pane">
                <div>
                    <span class="badge-premium mb-3 d-inline-block"><?php echo htmlspecialchars($product->category_name ?? 'Chưa phân loại', ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <h1 class="text-gradient fw-bold mb-2"><?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?></h1>
                
                <div class="detail-price">
                    <?php echo number_format($product->price, 0, ',', '.'); ?> <span style="font-size: 1.2rem;">VND</span>
                </div>
                
                <hr style="border-color: var(--glass-border);">
                
                <h5 class="fw-semibold mb-2" style="color: var(--text-main);">Mô tả chi tiết:</h5>
                <p class="detail-desc">
                    <?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?>
                </p>

                <!-- Stock display -->
                <?php
                $stock = (int)($product->stock ?? 0);
                if ($stock <= 0) {
                    $sClass = 'stock-info-danger';
                    $sIcon  = 'fa-ban';
                    $sLabel = 'Hết hàng';
                } elseif ($stock <= 5) {
                    $sClass = 'stock-info-warning';
                    $sIcon  = 'fa-triangle-exclamation';
                    $sLabel = 'Sắp hết hàng &mdash; chỉ còn ' . $stock . ' sản phẩm';
                } else {
                    $sClass = 'stock-info-success';
                    $sIcon  = 'fa-circle-check';
                    $sLabel = 'Còn ' . $stock . ' sản phẩm trong kho';
                }
                ?>
                <div class="stock-info-block <?php echo $sClass; ?>">
                    <i class="fa-solid <?php echo $sIcon; ?>"></i>
                    <span><?php echo $sLabel; ?></span>
                </div>

                <div class="d-flex flex-wrap gap-3">
                    <?php if ($stock <= 0): ?>
                        <button class="btn btn-premium px-4 py-2" disabled style="opacity: 0.6; cursor: not-allowed;" title="Sản phẩm đã hết hàng">
                            <i class="fa-solid fa-circle-xmark me-2"></i>Hết hàng
                        </button>
                    <?php else: ?>
                        <button onclick="addToCartAjax(event, '<?php echo $product->id; ?>')" class="btn btn-premium px-4 py-2">
                            <i class="fa-solid fa-cart-shopping me-2"></i>Thêm vào giỏ hàng
                        </button>
                    <?php endif; ?>
                    <?php if (SessionHelper::isAdmin()): ?>
                        <a href="<?php echo BASE_URL; ?>/Product/edit/<?php echo $product->id; ?>" class="btn btn-premium-warning px-4 py-2">
                            <i class="fa-solid fa-pen-to-square me-2"></i>Chỉnh sửa
                        </a>
                        <button onclick="confirmDelete('<?php echo $product->id; ?>', '<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>')" class="btn btn-premium-danger px-4 py-2">
                            <i class="fa-solid fa-trash-can me-2"></i>Xóa
                        </button>
                    <?php endif; ?>

                    <a href="<?php echo BASE_URL; ?>/Product" class="btn btn-glass-secondary px-4 py-2">
                        <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    const currentTheme = localStorage.getItem('theme') || 'dark';
    Swal.fire({
        title: 'Bạn có chắc chắn?',
        text: "Sản phẩm '" + name + "' sẽ bị xóa vĩnh viễn khỏi hệ thống!",
        icon: 'warning',
        showCancelButton: true,
        background: currentTheme === 'light' ? '#ffffff' : '#1d1d1f',
        color: currentTheme === 'light' ? '#1d1d1f' : '#f5f5f7',
        confirmButtonColor: '#ff453a',
        cancelButtonColor: '#0071e3',
        confirmButtonText: '<i class="fa-solid fa-trash me-2"></i>Có, hãy xóa!',
        cancelButtonText: 'Hủy bỏ'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit a hidden form via POST instead of GET
            const form = document.getElementById('deleteProductForm');
            form.action = '<?php echo BASE_URL; ?>/Product/delete/' + id;
            form.submit();
        }
    })
}
</script>

<!-- Hidden DELETE form (POST) -->
<form id="deleteProductForm" method="POST" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?php echo SessionHelper::getCSRFToken(); ?>">
</form>

<?php include 'app/views/shares/footer.php'; ?>
