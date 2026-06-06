<?php include 'app/views/shares/header.php'; ?>

<?php
// Calculate item subtotal total
$subtotal = 0;
foreach ($details as $item) {
    $subtotal += $item->price * $item->quantity;
}
// Shipping fee logic: 100,000 VND if subtotal is under 50,000,000 VND
$shipping_fee = $subtotal >= 50000000 ? 0 : 100000;

// Status index mapping for timeline
$statuses = ['Chờ xác nhận', 'Đang chuẩn bị hàng', 'Đang giao hàng', 'Đã giao hàng'];
$currentStatusIndex = array_search($order->status, $statuses);
if ($currentStatusIndex === false) {
    $currentStatusIndex = 0;
}
?>

<div class="mb-4">
    <a href="<?php echo BASE_URL; ?>/Order" class="btn btn-glass-secondary btn-sm mb-3">
        <i class="fa-solid fa-arrow-left me-2"></i>Quay lại danh sách
    </a>
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="text-gradient fw-bold mb-1">Chi tiết đơn hàng #OD-<?php echo $order->id; ?></h1>
            <p class="text-muted mb-0">Đặt ngày: <?php echo date('d/m/Y H:i:s', strtotime($order->created_at)); ?></p>
        </div>
        
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <?php if (SessionHelper::isAdmin()): ?>
                <?php if ($currentStatusIndex < count($statuses) - 1 && $order->status !== 'Đã hủy'): ?>
                    <form action="<?php echo BASE_URL; ?>/Order/updateStatus" method="POST" class="d-inline">
                        <input type="hidden" name="id" value="<?php echo $order->id; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo SessionHelper::getCSRFToken(); ?>">
                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($statuses[$currentStatusIndex + 1], ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-premium px-4 py-2">
                            <i class="fa-solid fa-arrow-right me-2"></i>Chuyển sang: <strong><?php echo htmlspecialchars($statuses[$currentStatusIndex + 1], ENT_QUOTES, 'UTF-8'); ?></strong>
                        </button>
                    </form>
                <?php elseif ($order->status === 'Đã giao hàng'): ?>
                    <span class="badge bg-success px-3 py-2" style="font-size: 14px; border-radius: 8px;">
                        <i class="fa-solid fa-check-double me-2"></i>Hoàn tất giao hàng
                    </span>
                <?php endif; ?>
            <?php elseif ($order->status === 'Chờ xác nhận'): ?>
                <form action="<?php echo BASE_URL; ?>/Order/cancel" method="POST" class="d-inline" id="cancel-form-<?php echo $order->id; ?>">
                    <input type="hidden" name="id" value="<?php echo $order->id; ?>">
                    <button type="button" onclick="confirmCancel(<?php echo $order->id; ?>)" class="btn px-4 py-2" style="border: 1px solid #ff4d4f; color: #ff4d4f; background: transparent; border-radius: 8px; transition: all 0.2s;">
                        <i class="fa-solid fa-xmark me-2"></i>Hủy đơn hàng
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Order Timeline Progress -->
<?php if ($order->status === 'Đã hủy'): ?>
<div class="glass-card mb-4 py-4 text-center">
    <div class="text-danger mb-3">
        <i class="fa-solid fa-circle-xmark" style="font-size: 4rem;"></i>
    </div>
    <h4 class="text-danger fw-bold">Đơn hàng đã bị hủy</h4>
    <p class="text-muted">Đơn hàng này đã được hủy và sẽ không được giao.</p>
</div>
<?php else: ?>
<div class="glass-card mb-4 py-4">
    <h5 class="fw-semibold mb-4 text-center text-md-start"><i class="fa-solid fa-truck-ramp-box me-2 text-primary"></i>Trạng thái giao hàng</h5>
    <div class="timeline-container">
        <div class="timeline-line">
            <div class="timeline-line-fill" style="width: <?php echo ($currentStatusIndex / 3) * 100; ?>%;"></div>
        </div>
        
        <div class="timeline-step <?php echo $currentStatusIndex >= 0 ? 'active' : ''; ?>">
            <div class="timeline-icon">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div class="timeline-label">Chờ xác nhận</div>
        </div>
        
        <div class="timeline-step <?php echo $currentStatusIndex >= 1 ? 'active' : ''; ?>">
            <div class="timeline-icon">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <div class="timeline-label">Đang chuẩn bị hàng</div>
        </div>
        
        <div class="timeline-step <?php echo $currentStatusIndex >= 2 ? 'active' : ''; ?>">
            <div class="timeline-icon">
                <i class="fa-solid fa-truck"></i>
            </div>
            <div class="timeline-label">Đang giao hàng</div>
        </div>
        
        <div class="timeline-step <?php echo $currentStatusIndex >= 3 ? 'active' : ''; ?>">
            <div class="timeline-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="timeline-label">Đã giao hàng</div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row">
    <!-- Customer and Shipping info -->
    <div class="col-lg-4 mb-4">
        <div class="glass-card h-100 p-4">
            <h5 class="fw-semibold mb-3 border-bottom pb-2" style="border-color: var(--glass-border) !important;">
                <i class="fa-solid fa-circle-info me-2 text-primary"></i>Thông tin đơn hàng
            </h5>
            
            <div class="mb-3">
                <label class="text-muted d-block mb-1" style="font-size: 13px;">Người nhận</label>
                <span class="fw-semibold" style="color: var(--text-main);"><?php echo htmlspecialchars($order->name, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            
            <div class="mb-3">
                <label class="text-muted d-block mb-1" style="font-size: 13px;">Số điện thoại</label>
                <span class="fw-semibold" style="color: var(--text-main);"><?php echo htmlspecialchars($order->phone, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            
            <div class="mb-3">
                <label class="text-muted d-block mb-1" style="font-size: 13px;">Địa chỉ giao hàng</label>
                <span style="color: var(--text-main); font-size: 15px;"><?php echo htmlspecialchars($order->address, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>

            <div class="mb-3">
                <label class="text-muted d-block mb-1" style="font-size: 13px;">Trạng thái hiện tại</label>
                <?php
                $badgeClass = 'bg-secondary';
                if ($order->status === 'Chờ xác nhận') $badgeClass = 'bg-warning text-dark';
                elseif ($order->status === 'Đang chuẩn bị hàng') $badgeClass = 'bg-info text-dark';
                elseif ($order->status === 'Đang giao hàng') $badgeClass = 'bg-primary';
                elseif ($order->status === 'Đã giao hàng') $badgeClass = 'bg-success';
                elseif ($order->status === 'Đã hủy') $badgeClass = 'bg-danger';
                ?>
                <span class="badge <?php echo $badgeClass; ?> px-3 py-2 font-size-14" style="border-radius: 6px; font-weight: 500;">
                    <?php echo htmlspecialchars($order->status, ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Items and Calculation -->
    <div class="col-lg-8 mb-4">
        <div class="glass-card h-100 p-4">
            <h5 class="fw-semibold mb-3 border-bottom pb-2" style="border-color: var(--glass-border) !important;">
                <i class="fa-solid fa-cubes me-2 text-primary"></i>Danh sách sản phẩm
            </h5>
            
            <div class="table-responsive">
                <table class="table table-premium align-middle mb-4">
                    <thead>
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Sản phẩm</th>
                            <th class="text-end">Đơn giá</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $item): ?>
                            <tr>
                                <td style="width: 80px;">
                                    <?php if (!empty($item->product_image) && file_exists($item->product_image)): ?>
                                        <img src="<?php echo BASE_URL . '/' . $item->product_image; ?>" alt="Product image" class="img-fluid rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fa-regular fa-image text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong style="color: var(--text-main);"><?php echo htmlspecialchars($item->product_name, ENT_QUOTES, 'UTF-8'); ?></strong>
                                </td>
                                <td class="text-end"><?php echo number_format($item->price, 0, ',', '.'); ?>đ</td>
                                <td class="text-center"><?php echo $item->quantity; ?></td>
                                <td class="text-end fw-semibold" style="color: var(--text-main);"><?php echo number_format($item->price * $item->quantity, 0, ',', '.'); ?>đ</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Calculation summary -->
            <div class="row justify-content-end">
                <div class="col-md-6 col-lg-5">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tạm tính:</span>
                        <span class="fw-semibold text-main"><?php echo number_format($subtotal, 0, ',', '.'); ?> VND</span>
                    </div>
                    <?php if ($order->discount_amount > 0): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Giảm giá <?php echo !empty($order->coupon_code) ? '(' . htmlspecialchars($order->coupon_code, ENT_QUOTES, 'UTF-8') . ')' : ''; ?>:</span>
                            <span class="text-danger fw-semibold">-<?php echo number_format($order->discount_amount, 0, ',', '.'); ?> VND</span>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-2" style="border-color: var(--glass-border) !important;">
                        <span class="text-muted">Phí giao hàng:</span>
                        <span class="fw-semibold text-main"><?php echo $shipping_fee > 0 ? number_format($shipping_fee, 0, ',', '.') . ' VND' : 'Miễn phí'; ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <strong class="text-main" style="font-size: 18px;">Tổng tiền:</strong>
                        <strong style="color: var(--primary); font-size: 20px;"><?php echo number_format($order->total_amount, 0, ',', '.'); ?> VND</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Status pill badges */
    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 980px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }
    
    /* Horizontal Progress Timeline CSS */
    .timeline-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        position: relative;
        width: 100%;
        margin: 20px 0;
        padding: 0 40px;
    }
    
    .timeline-line {
        position: absolute;
        top: 25px;
        left: 80px;
        right: 80px;
        height: 4px;
        background-color: var(--glass-border);
        z-index: 1;
        border-radius: 2px;
    }
    
    .timeline-line-fill {
        height: 100%;
        background-color: var(--primary);
        transition: width 0.5s ease-in-out;
        border-radius: 2px;
    }
    
    .timeline-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 120px;
        text-align: center;
        z-index: 2;
        position: relative;
    }
    
    .timeline-icon {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background-color: var(--canvas-parchment);
        border: 2px solid var(--glass-border);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: var(--text-muted);
        transition: all 0.4s ease;
    }
    
    .timeline-label {
        margin-top: 12px;
        font-size: 13px;
        font-weight: 500;
        color: var(--text-muted);
        transition: color 0.4s ease;
    }
    
    /* Active State for Timeline Steps */
    .timeline-step.active .timeline-icon {
        background-color: var(--primary);
        border-color: var(--primary);
        color: #ffffff;
        box-shadow: 0 0 0 6px rgba(0, 102, 204, 0.15);
    }
    
    .timeline-step.active .timeline-label {
        color: var(--text-main);
        font-weight: 600;
    }
    
    /* Responsive timeline */
    @media (max-width: 768px) {
        .timeline-container {
            flex-direction: column;
            align-items: flex-start;
            padding: 0 10px 0 30px;
        }
        .timeline-line {
            top: 20px;
            bottom: 20px;
            left: 55px;
            right: auto;
            width: 4px;
            height: calc(100% - 40px);
        }
        .timeline-line-fill {
            width: 100% !important;
            height: <?php echo ($currentStatusIndex / 3) * 100; ?>%;
        }
        .timeline-step {
            flex-direction: row;
            width: auto;
            text-align: left;
            margin-bottom: 25px;
        }
        .timeline-step:last-child {
            margin-bottom: 0;
        }
        .timeline-icon {
            width: 44px;
            height: 44px;
            font-size: 16px;
        }
        .timeline-label {
            margin-top: 0;
            margin-left: 15px;
        }
    }

    /* Add hover effect for the cancel button */
    button[onclick^="confirmCancel"]:hover {
        background-color: #ff4d4f !important;
        color: white !important;
    }
</style>

<script>
function confirmCancel(orderId) {
    Swal.fire({
        title: 'Hủy đơn hàng?',
        text: "Bạn có chắc chắn muốn hủy đơn hàng này không? Thao tác này không thể hoàn tác.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff4d4f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa-solid fa-check me-1"></i>Đồng ý hủy',
        cancelButtonText: 'Đóng',
        customClass: {
            confirmButton: 'btn btn-danger px-4 py-2 me-2',
            cancelButton: 'btn btn-secondary px-4 py-2'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cancel-form-' + orderId).submit();
        }
    });
}
</script>

<?php include 'app/views/shares/footer.php'; ?>
