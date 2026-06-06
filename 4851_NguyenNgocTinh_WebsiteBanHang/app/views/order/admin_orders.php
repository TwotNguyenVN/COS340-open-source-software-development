<?php include 'app/views/shares/header.php'; ?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h1 class="text-gradient fw-bold mb-1"><i class="fa-solid fa-receipt me-2 text-primary"></i>Quản lý đơn hàng</h1>
        <p class="text-muted mb-0">Quản lý và cập nhật trạng thái đơn hàng của tất cả khách hàng</p>
    </div>
</div>

<div class="glass-card">
    <?php if (empty($orders)): ?>
        <div class="text-center py-5">
            <div class="mb-3 text-muted">
                <i class="fa-solid fa-inbox fs-1"></i>
            </div>
            <h5>Không có đơn hàng nào</h5>
            <p class="text-muted mb-0">Hệ thống chưa ghi nhận đơn hàng nào.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-premium mb-0">
                <thead>
                    <tr>
                        <th style="width: 8%;">Mã ĐH</th>
                        <th style="width: 20%;">Khách hàng</th>
                        <th style="width: 12%;">Số điện thoại</th>
                        <th style="width: 15%;">Tổng tiền</th>
                        <th style="width: 15%;">Ngày đặt</th>
                        <th style="width: 18%;">Trạng thái</th>
                        <th style="width: 12%;" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>
                                <a href="<?php echo BASE_URL; ?>/Order/show/<?php echo $order->id; ?>" class="fw-semibold text-decoration-none" style="color: var(--primary);">
                                    #OD-<?php echo $order->id; ?>
                                </a>
                            </td>
                            <td>
                                <div>
                                    <strong style="color: var(--text-main);"><?php echo htmlspecialchars($order->name, ENT_QUOTES, 'UTF-8'); ?></strong>
                                </div>
                                <small class="text-muted">
                                    <i class="fa-regular fa-user me-1"></i><?php echo htmlspecialchars($order->username ?? 'Khách vãng lai', ENT_QUOTES, 'UTF-8'); ?>
                                </small>
                            </td>
                            <td><span class="text-muted"><?php echo htmlspecialchars($order->phone, ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td>
                                <strong style="color: var(--primary);"><?php echo number_format($order->total_amount, 0, ',', '.'); ?> VND</strong>
                            </td>
                            <td>
                                <span class="text-muted" style="font-size: 14px;">
                                    <?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $statuses = ['Chờ xác nhận', 'Đang chuẩn bị hàng', 'Đang giao hàng', 'Đã giao hàng'];
                                $statusIcons = ['fa-clock', 'fa-box-open', 'fa-truck', 'fa-circle-check'];
                                $statusColors = ['warning', 'info', 'primary', 'success'];
                                
                                $isCanceled = ($order->status === 'Đã hủy');
                                $currentIdx = array_search($order->status, $statuses);
                                
                                if ($isCanceled) {
                                    $colorClass = 'danger';
                                    $icon = 'fa-circle-xmark';
                                } else {
                                    if ($currentIdx === false) $currentIdx = 0;
                                    $colorClass = $statusColors[$currentIdx] ?? 'secondary';
                                    $icon = $statusIcons[$currentIdx] ?? 'fa-circle';
                                }
                                ?>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="status-pill status-<?php echo $colorClass; ?>">
                                        <i class="fa-solid <?php echo $icon; ?> me-1"></i>
                                        <?php echo htmlspecialchars($order->status, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                    <?php if ($isCanceled): ?>
                                        <span class="text-danger" style="font-size: 12px;"><i class="fa-solid fa-ban me-1"></i>Đã hủy bỏ</span>
                                    <?php elseif ($currentIdx !== false && $currentIdx < count($statuses) - 1): ?>
                                        <form action="<?php echo BASE_URL; ?>/Order/updateStatus" method="POST" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo $order->id; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo SessionHelper::getCSRFToken(); ?>">
                                            <input type="hidden" name="status" value="<?php echo htmlspecialchars($statuses[$currentIdx + 1], ENT_QUOTES, 'UTF-8'); ?>">
                                            <button type="submit" class="btn-next-status" title="Chuyển sang: <?php echo htmlspecialchars($statuses[$currentIdx + 1], ENT_QUOTES, 'UTF-8'); ?>">
                                                <i class="fa-solid fa-arrow-right me-1"></i><?php echo htmlspecialchars($statuses[$currentIdx + 1], ENT_QUOTES, 'UTF-8'); ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-success" style="font-size: 12px;"><i class="fa-solid fa-check-double me-1"></i>Hoàn tất</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="<?php echo BASE_URL; ?>/Order/show/<?php echo $order->id; ?>" class="btn btn-sm btn-glass-secondary py-1 px-3" title="Xem chi tiết">
                                    <i class="fa-solid fa-eye me-1"></i> Chi tiết
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 980px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }
    .status-warning  { background: rgba(255,159,10,0.12); color: #ff9f0a; border: 1px solid rgba(255,159,10,0.3); }
    .status-info     { background: rgba(10,132,255,0.12); color: #0a84ff; border: 1px solid rgba(10,132,255,0.3); }
    .status-primary  { background: rgba(0,113,227,0.12);  color: #0071e3; border: 1px solid rgba(0,113,227,0.3); }
    .status-success  { background: rgba(48,209,88,0.12);  color: #30d158; border: 1px solid rgba(48,209,88,0.3); }
    .status-danger   { background: rgba(255,69,58,0.12);  color: #ff453a; border: 1px solid rgba(255,69,58,0.3); }
    
    [data-theme="light"] .status-warning  { background: rgba(255,149,0,0.08);  color: #b86e00; }
    [data-theme="light"] .status-info     { background: rgba(0,122,255,0.08);  color: #007aff; }
    [data-theme="light"] .status-primary  { background: rgba(0,102,204,0.08);  color: #0066cc; }
    [data-theme="light"] .status-success  { background: rgba(52,199,89,0.08);  color: #1a8a3a; }
    [data-theme="light"] .status-danger   { background: rgba(255,59,48,0.08);  color: #d70015; }

    .btn-next-status {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 980px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid var(--accent-color, #0071e3);
        background: transparent;
        color: var(--accent-color, #0071e3);
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease;
        white-space: nowrap;
    }
    .btn-next-status:hover {
        background: var(--accent-color, #0071e3);
        color: #fff;
    }
</style>

<?php include 'app/views/shares/footer.php'; ?>
