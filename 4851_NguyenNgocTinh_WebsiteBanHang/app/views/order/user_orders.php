<?php include 'app/views/shares/header.php'; ?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h1 class="text-gradient fw-bold mb-1"><i class="fa-solid fa-receipt me-2 text-primary"></i>Đơn hàng của tôi</h1>
        <p class="text-muted mb-0">Theo dõi trạng thái giao hàng và lịch sử đơn hàng của bạn</p>
    </div>
</div>

<div class="glass-card">
    <?php if (empty($orders)): ?>
        <div class="text-center py-5">
            <div class="mb-3 text-muted">
                <i class="fa-solid fa-cart-shopping fs-1"></i>
            </div>
            <h5>Bạn chưa đặt đơn hàng nào</h5>
            <p class="text-muted mb-3">Hãy dạo quanh cửa hàng và chọn cho mình sản phẩm ưng ý nhé.</p>
            <a href="<?php echo BASE_URL; ?>/Product" class="btn btn-premium">
                <i class="fa-solid fa-basket-shopping me-2"></i>Mua sắm ngay
            </a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-premium mb-0">
                <thead>
                    <tr>
                        <th style="width: 10%;">Mã ĐH</th>
                        <th style="width: 20%;">Người nhận</th>
                        <th style="width: 25%;">Địa chỉ giao hàng</th>
                        <th style="width: 15%;">Tổng tiền</th>
                        <th style="width: 15%;">Trạng thái</th>
                        <th style="width: 15%;" class="text-center">Hành động</th>
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
                                <div><strong style="color: var(--text-main);"><?php echo htmlspecialchars($order->name, ENT_QUOTES, 'UTF-8'); ?></strong></div>
                                <small class="text-muted"><?php echo htmlspecialchars($order->phone, ENT_QUOTES, 'UTF-8'); ?></small>
                            </td>
                            <td>
                                <span class="text-muted" style="font-size: 14px; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;" title="<?php echo htmlspecialchars($order->address, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($order->address, ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </td>
                            <td>
                                <strong style="color: var(--primary);"><?php echo number_format($order->total_amount, 0, ',', '.'); ?> VND</strong>
                            </td>
                            <td>
                                <?php
                                $badgeClass = 'bg-secondary';
                                if ($order->status === 'Chờ xác nhận') {
                                    $badgeClass = 'bg-warning text-dark';
                                } elseif ($order->status === 'Đang chuẩn bị hàng') {
                                    $badgeClass = 'bg-info text-dark';
                                } elseif ($order->status === 'Đang giao hàng') {
                                    $badgeClass = 'bg-primary';
                                } elseif ($order->status === 'Đã giao hàng') {
                                    $badgeClass = 'bg-success';
                                } elseif ($order->status === 'Đã hủy') {
                                    $badgeClass = 'bg-danger';
                                }
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>" style="font-size: 13px; font-weight: 500; padding: 6px 12px; border-radius: 6px;">
                                    <?php echo htmlspecialchars($order->status, ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?php echo BASE_URL; ?>/Order/show/<?php echo $order->id; ?>" class="btn btn-sm btn-glass-secondary py-1 px-3" title="Xem chi tiết">
                                    <i class="fa-solid fa-circle-info me-1"></i> Chi tiết
                                </a>
                                <?php if ($order->status === 'Chờ xác nhận'): ?>
                                    <form action="<?php echo BASE_URL; ?>/Order/cancel" method="POST" class="d-inline ms-1" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                                        <input type="hidden" name="id" value="<?php echo $order->id; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-3" title="Hủy đơn hàng">
                                            <i class="fa-solid fa-xmark me-1"></i> Hủy
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'app/views/shares/footer.php'; ?>
