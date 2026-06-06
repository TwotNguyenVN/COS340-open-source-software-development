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
                                if ($order->status === 'Chờ xác nhận') $badgeClass = 'bg-warning text-dark';
                                elseif ($order->status === 'Đang chuẩn bị hàng') $badgeClass = 'bg-info text-dark';
                                elseif ($order->status === 'Đang giao hàng') $badgeClass = 'bg-primary';
                                elseif ($order->status === 'Đã giao hàng') $badgeClass = 'bg-success';
                                elseif ($order->status === 'Đã hủy') $badgeClass = 'bg-danger';
                                elseif ($order->status === 'Hoàn thành') $badgeClass = 'bg-success';
                                elseif ($order->status === 'Yêu cầu hoàn trả') $badgeClass = 'bg-warning text-dark';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>" style="font-size: 13px; font-weight: 500; padding: 6px 12px; border-radius: 6px;">
                                    <?php echo htmlspecialchars($order->status, ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex flex-column align-items-center gap-2">
                                    <a href="<?php echo BASE_URL; ?>/Order/show/<?php echo $order->id; ?>" class="btn btn-sm btn-glass-secondary py-1 px-3 w-100" title="Xem chi tiết" style="border-radius: 20px;">
                                        <i class="fa-solid fa-circle-info me-1"></i> Chi tiết
                                    </a>
                                    <?php if ($order->status === 'Chờ xác nhận'): ?>
                                        <form action="<?php echo BASE_URL; ?>/Order/cancel" method="POST" class="w-100 m-0" id="cancel-form-<?php echo $order->id; ?>">
                                            <input type="hidden" name="id" value="<?php echo $order->id; ?>">
                                            <button type="button" onclick="confirmCancel(<?php echo $order->id; ?>)" class="btn btn-sm py-1 px-3 w-100" title="Hủy đơn hàng" style="border: 1px solid #ff4d4f; color: #ff4d4f; background: transparent; border-radius: 20px; transition: all 0.2s;">
                                                <i class="fa-solid fa-xmark me-1"></i> Hủy
                                            </button>
                                        </form>
                                    <?php elseif ($order->status === 'Đã giao hàng'): ?>
                                        <form action="<?php echo BASE_URL; ?>/Order/completeOrder" method="POST" class="w-100 m-0 mt-1" id="complete-form-<?php echo $order->id; ?>">
                                            <input type="hidden" name="id" value="<?php echo $order->id; ?>">
                                            <button type="button" onclick="confirmComplete(<?php echo $order->id; ?>)" class="btn btn-sm py-1 px-3 w-100 btn-complete-hover" title="Đã nhận được hàng" style="border: 1px solid #30d158; color: #30d158; background: transparent; border-radius: 20px; transition: all 0.2s;">
                                                <i class="fa-solid fa-check me-1"></i> Đã nhận
                                            </button>
                                        </form>
                                        <a href="<?php echo BASE_URL; ?>/Order/show/<?php echo $order->id; ?>#return" class="btn btn-sm py-1 px-3 w-100 btn-return-hover mt-1" title="Yêu cầu hoàn trả" style="border: 1px solid #ff9f0a; color: #ff9f0a; background: transparent; border-radius: 20px; transition: all 0.2s;">
                                            <i class="fa-solid fa-rotate-left me-1"></i> Hoàn trả
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

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
</script>

<style>
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

<?php include 'app/views/shares/footer.php'; ?>
