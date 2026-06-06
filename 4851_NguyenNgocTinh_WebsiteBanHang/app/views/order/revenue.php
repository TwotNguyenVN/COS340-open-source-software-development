<?php include 'app/views/shares/header.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-gradient fw-bold mb-0"><i class="fa-solid fa-chart-line me-2 text-primary"></i>Thống kê Doanh thu</h1>
        <a href="<?php echo BASE_URL; ?>/Order" class="btn btn-glass-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i>Quay lại
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="glass-card text-center h-100 d-flex flex-column justify-content-center p-4" style="border-top: 3px solid #34c759;">
                <div class="text-muted mb-2 text-uppercase fw-semibold" style="letter-spacing: 1px; font-size: 0.85rem;">Tổng doanh thu</div>
                <h2 class="display-5 fw-bold mb-0 text-success"><?php echo number_format($totalRevenue, 0, ',', '.'); ?> đ</h2>
            </div>
        </div>
        <div class="col-md-6">
            <div class="glass-card text-center h-100 d-flex flex-column justify-content-center p-4" style="border-top: 3px solid var(--primary);">
                <div class="text-muted mb-2 text-uppercase fw-semibold" style="letter-spacing: 1px; font-size: 0.85rem;">Số đơn hàng hoàn thành</div>
                <h2 class="display-5 fw-bold mb-0 text-primary"><?php echo $totalCompletedOrders; ?></h2>
            </div>
        </div>
    </div>

    <div class="glass-card">
        <h3 class="fw-bold mb-4">Chi tiết theo ngày</h3>
        <?php if (empty($revenues)): ?>
            <div class="text-center py-5">
                <i class="fa-solid fa-chart-bar text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                <h5 class="text-muted">Chưa có dữ liệu doanh thu</h5>
                <p class="text-muted mb-0">Doanh thu sẽ xuất hiện khi có đơn hàng được giao thành công.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-premium mb-0">
                    <thead>
                        <tr>
                            <th>Ngày</th>
                            <th class="text-center">Số đơn hàng</th>
                            <th class="text-end">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($revenues as $revenue): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded p-2 me-3" style="background: rgba(0, 102, 204, 0.1); color: var(--primary);">
                                            <i class="fa-regular fa-calendar"></i>
                                        </div>
                                        <span class="fw-medium"><?php echo date('d/m/Y', strtotime($revenue->date)); ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-premium">
                                        <?php echo $revenue->total_orders; ?> đơn
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    + <?php echo number_format($revenue->daily_revenue, 0, ',', '.'); ?> đ
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
