<?php include 'app/views/shares/header.php'; ?>

<?php
// Retrieve order details from session
$order_id = $_SESSION['last_order_id'] ?? null;
$details = $_SESSION['last_order_details'] ?? null;

// Optional: clean up details so refreshes show standard confirmation
// We will keep it for now so the user can inspect the output.
?>

<style>
    .invoice-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        box-shadow: var(--glass-shadow);
        overflow: hidden;
        padding: 3rem 2rem;
    }

    .success-ring {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(48, 209, 88, 0.1);
        border: 2px solid #30d158;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }

    .success-ring i {
        font-size: 38px;
        color: #30d158;
    }

    .receipt-details {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    [data-theme="light"] .receipt-details {
        background: rgba(0, 0, 0, 0.01);
    }

    .receipt-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    [data-theme="light"] .receipt-row {
        border-bottom: 1px solid rgba(0, 0, 0, 0.04);
    }

    .receipt-row:last-child {
        border-bottom: none;
    }
</style>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="invoice-card text-center">
            <div class="success-ring">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            
            <h2 class="text-gradient fw-bold mb-2">Đặt hàng thành công!</h2>
            <p class="text-muted mb-4">Cảm ơn bạn đã tin tưởng mua sắm tại NTECH STORE. Đơn hàng của bạn đã được tiếp nhận và đang xử lý.</p>

            <?php if ($order_id && $details): ?>
                <div class="text-start receipt-details">
                    <h5 class="mb-3 fw-bold border-bottom border-secondary border-opacity-20 pb-2">
                        <i class="fa-solid fa-receipt me-2 text-primary"></i>Thông tin hóa đơn #<?php echo $order_id; ?>
                    </h5>
                    
                    <div class="receipt-row">
                        <span class="text-muted">Họ và tên người nhận:</span>
                        <span class="text-white fw-medium" style="color: var(--text-main) !important;"><?php echo htmlspecialchars($details['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="text-muted">Số điện thoại liên hệ:</span>
                        <span class="text-white fw-medium" style="color: var(--text-main) !important;"><?php echo htmlspecialchars($details['phone'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="receipt-row">
                        <span class="text-muted">Địa chỉ nhận hàng:</span>
                        <span class="text-white fw-medium" style="color: var(--text-main) !important;"><?php echo htmlspecialchars($details['address'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>

                    <h5 class="mt-4 mb-3 fw-bold border-bottom border-secondary border-opacity-20 pb-2">
                        <i class="fa-solid fa-box-open me-2 text-success"></i>Chi tiết sản phẩm
                    </h5>
                    
                    <?php 
                    $subtotal_sum = 0;
                    foreach ($details['items'] as $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $subtotal_sum += $subtotal;
                    ?>
                        <div class="receipt-row">
                            <span class="text-muted">
                                <?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>
                                <span class="badge bg-secondary text-dark ms-1">x<?php echo $item['quantity']; ?></span>
                            </span>
                            <span class="text-white" style="color: var(--text-main) !important;"><?php echo number_format($subtotal, 0, ',', '.'); ?> VND</span>
                        </div>
                    <?php endforeach; ?>

                    <div class="receipt-row mt-3 pt-2" style="border-top: 1px dashed var(--glass-border);">
                        <span class="text-muted">Tạm tính:</span>
                        <span class="text-white" style="color: var(--text-main) !important;"><?php echo number_format($subtotal_sum, 0, ',', '.'); ?> VND</span>
                    </div>

                    <?php if ($details['coupon_code']): ?>
                        <div class="receipt-row text-danger">
                            <span>Giảm giá (Mã: <strong><?php echo htmlspecialchars($details['coupon_code']); ?></strong>):</span>
                            <span>-<?php echo number_format($details['discount_amount'], 0, ',', '.'); ?> VND</span>
                        </div>
                    <?php endif; ?>

                    <div class="receipt-row fs-5 fw-bold text-success pt-2" style="border-top: 1px solid var(--glass-border);">
                        <span>Tổng thanh toán:</span>
                        <span style="color: #30d158 !important;"><?php echo number_format($details['total_amount'], 0, ',', '.'); ?> VND</span>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-warning p-3 rounded-3 mb-4">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>Không tìm thấy thông tin chi tiết của đơn hàng vừa đặt.
                </div>
            <?php endif; ?>

            <div class="d-flex gap-3 justify-content-center">
                <a href="<?php echo BASE_URL; ?>/Product" class="btn btn-premium px-4">
                    <i class="fa-solid fa-bag-shopping me-2"></i>Tiếp tục mua sắm
                </a>
                <a href="<?php echo BASE_URL; ?>/" class="btn btn-glass-secondary px-4">
                    <i class="fa-solid fa-house me-2"></i>Quay lại trang chủ
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
