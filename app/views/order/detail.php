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
$terminalStatuses = ['Đã hủy', 'Hoàn thành', 'Yêu cầu hoàn trả', 'Từ chối hoàn trả', 'Đã thu hồi'];
$isTerminalStatus = in_array($order->status, $terminalStatuses);
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
                <?php if ($order->status === 'Đã duyệt hoàn trả'): ?>
                    <form action="<?php echo BASE_URL; ?>/Order/updateStatus" method="POST" class="d-inline">
                        <input type="hidden" name="id" value="<?php echo $order->id; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo SessionHelper::getCSRFToken(); ?>">
                        <input type="hidden" name="status" value="Đã thu hồi">
                        <button type="submit" class="btn btn-premium px-4 py-2">
                            <i class="fa-solid fa-arrow-right me-2"></i>Chuyển sang: <strong>Đã thu hồi</strong>
                        </button>
                    </form>
                <?php elseif (!$isTerminalStatus && $order->status !== 'Đã giao hàng' && $currentStatusIndex < count($statuses) - 1): ?>
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
                <?php elseif ($order->status === 'Đã thu hồi'): ?>
                    <span class="badge bg-success px-3 py-2" style="font-size: 14px; border-radius: 8px;">
                        <i class="fa-solid fa-check-double me-2"></i>Đã thu hồi hàng
                    </span>
                <?php endif; ?>
            <?php elseif ($order->status === 'Chờ xác nhận'): ?>
                <form action="<?php echo BASE_URL; ?>/Order/cancel" method="POST" class="d-inline" id="cancel-form-<?php echo $order->id; ?>">
                    <input type="hidden" name="id" value="<?php echo $order->id; ?>">
                    <button type="button" onclick="confirmCancel(<?php echo $order->id; ?>)" class="btn px-4 py-2" style="border: 1px solid #ff4d4f; color: #ff4d4f; background: transparent; border-radius: 8px; transition: all 0.2s;">
                        <i class="fa-solid fa-xmark me-2"></i>Hủy đơn hàng
                    </button>
                </form>
            <?php elseif ($order->status === 'Đã giao hàng'): ?>
                <div class="d-flex gap-2 flex-wrap justify-content-md-end mt-2 mt-md-0">
                    <form action="<?php echo BASE_URL; ?>/Order/completeOrder" method="POST" class="d-inline" id="complete-form-<?php echo $order->id; ?>">
                        <input type="hidden" name="id" value="<?php echo $order->id; ?>">
                        <button type="button" onclick="confirmComplete(<?php echo $order->id; ?>)" class="btn px-3 py-2 btn-complete-hover" style="border: 1px solid #30d158; color: #30d158; background: transparent; border-radius: 8px; transition: all 0.2s;">
                            <i class="fa-solid fa-check me-2"></i>Đã nhận hàng
                        </button>
                    </form>
                    <button type="button" class="btn px-3 py-2 btn-return-hover" data-bs-toggle="modal" data-bs-target="#returnModal" style="border: 1px solid #ff9f0a; color: #ff9f0a; background: transparent; border-radius: 8px; transition: all 0.2s;">
                        <i class="fa-solid fa-rotate-left me-2"></i>Hoàn trả
                    </button>
                </div>
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
<?php elseif ($order->status === 'Yêu cầu hoàn trả' || $order->status === 'Đã duyệt hoàn trả' || $order->status === 'Từ chối hoàn trả' || $order->status === 'Đã thu hồi'): ?>
<div class="glass-card mb-4 p-4" style="border-left: 4px solid <?php echo $order->status === 'Từ chối hoàn trả' ? '#ff4d4f' : (($order->status === 'Đã duyệt hoàn trả' || $order->status === 'Đã thu hồi') ? '#30d158' : '#ff9f0a'); ?>;">
    <div class="d-flex align-items-start">
        <div class="me-3 mt-1">
            <?php if ($order->status === 'Đã duyệt hoàn trả'): ?>
                <i class="fa-solid fa-check-circle text-success" style="font-size: 2.5rem;"></i>
            <?php elseif ($order->status === 'Đã thu hồi'): ?>
                <i class="fa-solid fa-box-archive text-success" style="font-size: 2.5rem;"></i>
            <?php elseif ($order->status === 'Từ chối hoàn trả'): ?>
                <i class="fa-solid fa-times-circle text-danger" style="font-size: 2.5rem;"></i>
            <?php else: ?>
                <i class="fa-solid fa-rotate-left text-warning" style="font-size: 2.5rem;"></i>
            <?php endif; ?>
        </div>
        <div class="flex-grow-1">
            <h4 class="fw-bold <?php echo $order->status === 'Từ chối hoàn trả' ? 'text-danger' : ($order->status === 'Đã duyệt hoàn trả' ? 'text-success' : 'text-warning'); ?>">
                <?php echo $order->status; ?>
            </h4>
            
            <div class="mt-3 p-3 bg-light rounded text-start border">
                <strong class="text-dark">Lý do của khách hàng:</strong><br>
                <span class="text-muted"><?php echo nl2br(htmlspecialchars($order->return_reason ?? '', ENT_QUOTES, 'UTF-8')); ?></span>
                
                <?php 
                $returnedProducts = json_decode($order->return_products ?? '[]', true);
                if (!empty($returnedProducts) && is_array($returnedProducts)): 
                ?>
                <div class="mt-2 pt-2 border-top">
                    <strong class="text-dark">Sản phẩm yêu cầu hoàn:</strong>
                    <ul class="mb-0 text-muted ps-3">
                        <?php foreach ($details as $d): 
                            if (in_array($d->product_id, $returnedProducts)): ?>
                            <li><?php echo htmlspecialchars($d->product_name, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endif; endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($order->return_admin_reply)): ?>
                <div class="mt-3 p-3 rounded text-start border <?php echo $order->status === 'Từ chối hoàn trả' ? 'bg-danger text-white border-danger' : 'bg-success text-white border-success'; ?>" style="--bs-bg-opacity: .1;">
                    <strong class="<?php echo $order->status === 'Từ chối hoàn trả' ? 'text-danger' : 'text-success'; ?>">Phản hồi từ Admin:</strong><br>
                    <span class="<?php echo $order->status === 'Từ chối hoàn trả' ? 'text-danger' : 'text-success'; ?>"><?php echo nl2br(htmlspecialchars($order->return_admin_reply, ENT_QUOTES, 'UTF-8')); ?></span>
                </div>
            <?php endif; ?>

            <?php if (SessionHelper::isAdmin() && $order->status === 'Yêu cầu hoàn trả'): ?>
                <form action="<?php echo BASE_URL; ?>/Order/processReturn" method="POST" class="mt-4">
                    <input type="hidden" name="id" value="<?php echo $order->id; ?>">
                    <div class="mb-3">
                        <label class="fw-semibold mb-1">Nhập phản hồi (Lý do từ chối hoặc Hẹn ngày lấy hàng) <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="admin_reply" rows="3" required placeholder="Ghi chú lại phản hồi của bạn..."></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="action" value="approve" class="btn btn-success px-4 py-2 fw-semibold">
                            <i class="fa-solid fa-check me-2"></i>Duyệt hoàn trả
                        </button>
                        <button type="submit" name="action" value="reject" class="btn btn-danger px-4 py-2 fw-semibold">
                            <i class="fa-solid fa-xmark me-2"></i>Từ chối hoàn trả
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php else: ?>
<div class="glass-card mb-4 py-4">
    <h5 class="fw-semibold mb-4 text-center text-md-start"><i class="fa-solid fa-truck-ramp-box me-2 text-primary"></i>Trạng thái giao hàng</h5>
    <div class="timeline-container">
        <div class="timeline-line">
            <div class="timeline-line-fill" style="width: <?php echo ($order->status === 'Hoàn thành' ? 100 : ($currentStatusIndex / 3) * 100); ?>%;"></div>
        </div>
        
        <div class="timeline-step <?php echo ($order->status === 'Hoàn thành' || $currentStatusIndex >= 0) ? 'active' : ''; ?>">
            <div class="timeline-icon">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div class="timeline-label">Chờ xác nhận</div>
        </div>
        
        <div class="timeline-step <?php echo ($order->status === 'Hoàn thành' || $currentStatusIndex >= 1) ? 'active' : ''; ?>">
            <div class="timeline-icon">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <div class="timeline-label">Đang chuẩn bị hàng</div>
        </div>
        
        <div class="timeline-step <?php echo ($order->status === 'Hoàn thành' || $currentStatusIndex >= 2) ? 'active' : ''; ?>">
            <div class="timeline-icon">
                <i class="fa-solid fa-truck"></i>
            </div>
            <div class="timeline-label">Đang giao hàng</div>
        </div>
        
        <div class="timeline-step <?php echo ($order->status === 'Hoàn thành' || $currentStatusIndex >= 3) ? 'active' : ''; ?>">
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
                elseif ($order->status === 'Hoàn thành') $badgeClass = 'bg-success';
                elseif ($order->status === 'Yêu cầu hoàn trả') $badgeClass = 'bg-warning text-dark';
                elseif ($order->status === 'Đã duyệt hoàn trả') $badgeClass = 'bg-success';
                elseif ($order->status === 'Từ chối hoàn trả') $badgeClass = 'bg-danger';
                elseif ($order->status === 'Đã thu hồi') $badgeClass = 'bg-success';
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

<?php if ($order->status === 'Đã giao hàng' && !SessionHelper::isAdmin()): ?>
<!-- Return Request Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background-color: rgba(255,159,10,0.1); border-bottom: 1px solid rgba(255,159,10,0.2);">
                <h5 class="modal-title text-warning fw-bold" id="returnModalLabel"><i class="fa-solid fa-rotate-left me-2"></i>Yêu cầu hoàn trả</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/Order/returnOrder" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" value="<?php echo $order->id; ?>">
                    
                    <p class="text-muted mb-3">Vui lòng chọn các sản phẩm bạn muốn hoàn trả và cung cấp lý do chi tiết.</p>
                    
                    <div class="mb-4">
                        <label class="fw-semibold mb-2">Sản phẩm cần hoàn trả <span class="text-danger">*</span></label>
                        <div class="table-responsive border rounded">
                            <table class="table align-middle mb-0">
                                <tbody>
                                    <?php foreach ($details as $item): ?>
                                    <tr>
                                        <td class="text-center" style="width: 50px;">
                                            <input class="form-check-input" type="checkbox" name="return_products[]" value="<?php echo $item->product_id; ?>" style="width: 20px; height: 20px; cursor: pointer;">
                                        </td>
                                        <td style="width: 60px;">
                                            <?php if (!empty($item->product_image) && file_exists($item->product_image)): ?>
                                                <img src="<?php echo BASE_URL . '/' . $item->product_image; ?>" alt="img" class="img-fluid rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($item->product_name, ENT_QUOTES, 'UTF-8'); ?></div>
                                            <small class="text-muted">SL: <?php echo $item->quantity; ?> | Giá: <?php echo number_format($item->price, 0, ',', '.'); ?>đ</small>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="return_reason" class="fw-semibold mb-2">Lý do hoàn trả <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="return_reason" id="return_reason" rows="4" placeholder="Ví dụ: Sản phẩm bị lỗi kỹ thuật, giao sai màu..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--glass-border);">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning text-white fw-semibold px-4">Gửi yêu cầu</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

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
    .btn-complete-hover:hover {
        background-color: #30d158 !important;
        color: white !important;
    }
    .btn-return-hover:hover {
        background-color: #ff9f0a !important;
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

function confirmComplete(orderId) {
    Swal.fire({
        title: 'Đã nhận được hàng?',
        text: "Bạn xác nhận đơn hàng đã được giao thành công và không có lỗi gì?",
        icon: 'success',
        showCancelButton: true,
        confirmButtonColor: '#30d158',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa-solid fa-check me-1"></i>Xác nhận',
        cancelButtonText: 'Đóng',
        customClass: {
            confirmButton: 'btn btn-success px-4 py-2 me-2',
            cancelButton: 'btn btn-secondary px-4 py-2'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('complete-form-' + orderId).submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash === '#cancel') {
        if (typeof confirmCancel === 'function') {
            confirmCancel(<?php echo $order->id; ?>);
        }
    } else if (window.location.hash === '#return') {
        var returnModalEl = document.getElementById('returnModal');
        if (returnModalEl) {
            var returnModal = new bootstrap.Modal(returnModalEl);
            returnModal.show();
        }
    }
});
</script>

<?php include 'app/views/shares/footer.php'; ?>
