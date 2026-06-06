<?php include 'app/views/shares/header.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="text-gradient fw-bold mb-0"><i class="fa-solid fa-chart-line me-2 text-primary"></i>Thống kê Doanh thu</h1>
        <div class="d-flex gap-2 align-items-center">
            <div class="btn-group shadow-sm" role="group">
                <a href="?filter=7days" class="btn <?php echo (!isset($_GET['filter']) || $_GET['filter'] === '7days') ? 'btn-primary' : 'btn-light border'; ?> px-3">7 ngày</a>
                <a href="?filter=30days" class="btn <?php echo (isset($_GET['filter']) && $_GET['filter'] === '30days') ? 'btn-primary' : 'btn-light border'; ?> px-3">30 ngày</a>
                <a href="?filter=month" class="btn <?php echo (isset($_GET['filter']) && $_GET['filter'] === 'month') ? 'btn-primary' : 'btn-light border'; ?> px-3">Tháng này</a>
            </div>
            <a href="<?php echo BASE_URL; ?>/Order" class="btn btn-glass-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i>Quay lại
            </a>
        </div>
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

    <div class="glass-card mb-4 p-4">
        <h3 class="fw-bold mb-4">Biểu đồ doanh thu</h3>
        <div style="height: 350px; width: 100%;">
            <canvas id="revenueChart"></canvas>
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
                        <?php 
                            $idx = 0;
                            foreach ($revenues as $revenue): 
                                $dateKey = $revenue->date;
                                $dayOrders = isset($ordersByDate[$dateKey]) ? $ordersByDate[$dateKey] : [];
                                $idx++;
                        ?>
                            <tr data-bs-toggle="collapse" data-bs-target="#collapseDay-<?php echo $idx; ?>" style="cursor: pointer;" class="hover-bg-light">
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
                                <td class="text-end fw-bold text-success align-middle">
                                    + <?php echo number_format($revenue->daily_revenue, 0, ',', '.'); ?> đ
                                    <i class="fa-solid fa-chevron-down ms-3 text-muted" style="font-size: 0.8rem;"></i>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="p-0 border-0">
                                    <div class="collapse" id="collapseDay-<?php echo $idx; ?>">
                                        <div class="p-3 bg-light" style="border-radius: 0 0 12px 12px; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                            <?php if (!empty($dayOrders)): ?>
                                                <h6 class="mb-3 text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Các đơn hàng trong ngày</h6>
                                                <div class="row g-3">
                                                    <?php foreach ($dayOrders as $order): ?>
                                                        <div class="col-md-6 col-lg-4">
                                                            <a href="<?php echo BASE_URL; ?>/Order/show/<?php echo $order->id; ?>" class="text-decoration-none">
                                                                <div class="card h-100 border-0 shadow-sm hover-lift" style="border-radius: 8px;">
                                                                    <div class="card-body p-3">
                                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                                            <span class="badge bg-secondary">#<?php echo $order->id; ?></span>
                                                                            <span class="text-muted" style="font-size: 0.8rem;"><?php echo date('H:i', strtotime($order->created_at)); ?></span>
                                                                        </div>
                                                                        <div class="d-flex justify-content-between align-items-end mt-3">
                                                                            <div class="text-dark fw-medium"><?php echo htmlspecialchars($order->name); ?></div>
                                                                            <div class="text-success fw-bold"><?php echo number_format($order->total_amount, 0, ',', '.'); ?> đ</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="text-muted text-center py-2">Không có chi tiết đơn hàng</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Import Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Parse PHP data
    const labels = <?php echo $chartLabels ?? '[]'; ?>;
    const data = <?php echo $chartRevenues ?? '[]'; ?>;
    
    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(0, 113, 227, 0.4)');   // primary color with opacity
    gradient.addColorStop(1, 'rgba(0, 113, 227, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: data,
                borderColor: '#0071e3', // primary
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#0071e3',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4 // curve
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: { size: 13, family: 'Inter' },
                    bodyFont: { size: 14, family: 'Inter', weight: 'bold' },
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            let value = context.raw;
                            return ' ' + new Intl.NumberFormat('vi-VN').format(value) + ' VNĐ';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        font: { family: 'Inter', size: 12 },
                        callback: function(value) {
                            if (value >= 1000000) {
                                return (value / 1000000) + 'Tr';
                            } else if (value >= 1000) {
                                return (value / 1000) + 'k';
                            }
                            return value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: { family: 'Inter', size: 12 },
                        maxTicksLimit: 10
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
        }
    });
});
</script>

<?php include 'app/views/shares/footer.php'; ?>
