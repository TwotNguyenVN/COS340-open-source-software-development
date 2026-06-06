<?php include 'app/views/shares/header.php'; ?>

<style>
    .form-control-glass {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        color: var(--text-main);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control-glass:focus {
        background: rgba(255, 255, 255, 0.08);
        border-color: var(--accent-color);
        box-shadow: 0 0 0 4px rgba(0, 113, 227, 0.15);
        color: var(--text-main);
    }

    .form-label {
        font-weight: 500;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .order-summary-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
</style>

<div class="row mb-3">
    <div class="col-md-8 mx-auto">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/Product/cart" class="text-muted text-decoration-none">Giỏ hàng</a></li>
                <li class="breadcrumb-item active" style="color: var(--text-main);" aria-current="page">Thanh toán</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="glass-card">
            <h2 class="text-gradient fw-bold mb-4"><i class="fa-solid fa-credit-card me-2 text-primary"></i>Thông tin thanh toán</h2>

            <?php if (isset($_SESSION['error_msg'])): ?>
                <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 p-3 mb-4">
                    <i class="fa-solid fa-circle-exclamation me-2"></i><?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
                </div>
            <?php endif; ?>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="order-summary-card">
                        <h5 class="mb-3 fw-bold" style="color: var(--text-main);"><i class="fa-solid fa-basket-shopping me-2 text-success"></i>Tóm tắt đơn hàng</h5>
                        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                            <?php 
                            $total = 0;
                            foreach ($_SESSION['cart'] as $item): 
                                $subtotal = $item['price'] * $item['quantity'];
                                $total += $subtotal;
                            ?>
                                <div class="d-flex justify-content-between align-items-center mb-2 py-1 border-bottom border-secondary border-opacity-10">
                                    <div class="text-muted"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?> <span class="badge-premium ms-2">x<?php echo $item['quantity']; ?></span></div>
                                    <div class="fw-medium" style="color: var(--text-main);"><?php echo number_format($subtotal, 0, ',', '.'); ?> VND</div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">Tạm tính:</div>
                                <div class="fw-medium" style="color: var(--text-main);"><?php echo number_format($total, 0, ',', '.'); ?> VND</div>
                            </div>

                            <?php 
                            $discount = 0;
                            if (isset($_SESSION['coupon'])): 
                                $coupon = $_SESSION['coupon'];
                                if ($coupon['type'] === 'percentage') {
                                    $discount = $total * ($coupon['value'] / 100);
                                } else {
                                    $discount = min($total, $coupon['value']);
                                }
                            ?>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div class="text-muted">Giảm giá <span class="badge bg-success text-white ms-1 fw-bold"><?php echo htmlspecialchars($coupon['code']); ?></span>:</div>
                                    <div class="text-danger fw-bold">-<?php echo number_format($discount, 0, ',', '.'); ?> VND</div>
                                </div>
                            <?php endif; ?>

                            <?php 
                            $shipping_fee = $total >= 50000000 ? 0 : 100000;
                            ?>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div class="text-muted">Phí giao hàng:</div>
                                <div class="fw-medium" style="color: var(--text-main);"><?php echo $shipping_fee > 0 ? number_format($shipping_fee, 0, ',', '.') . ' VND' : 'Miễn phí'; ?></div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top border-secondary border-opacity-20">
                                <h6 class="fw-bold mb-0" style="color: var(--text-main);">Tổng thanh toán:</h6>
                                <h5 class="text-success fw-bold mb-0" style="color: #30d158 !important;"><?php echo number_format(max(0, $total - $discount) + $shipping_fee, 0, ',', '.'); ?> VND</h5>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">Không có sản phẩm nào trong giỏ hàng.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <form id="checkoutForm" method="POST" action="<?php echo BASE_URL; ?>/Product/processCheckout" novalidate>
                <div class="mb-4">
                    <label for="name" class="form-label">Họ và tên người nhận:</label>
                    <div class="input-group">
                        <span class="input-group-text form-control-glass border-end-0" style="background: rgba(255,255,255,0.02);"><i class="fa-solid fa-user text-muted"></i></span>
                        <input type="text" id="name" name="name" class="form-control form-control-glass border-start-0 ps-0" placeholder="Ví dụ: Nguyễn Văn A" minlength="2" maxlength="50" pattern="^[\p{L}\s]{2,50}$" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="phone" class="form-label">Số điện thoại liên hệ:</label>
                    <div class="input-group">
                        <span class="input-group-text form-control-glass border-end-0" style="background: rgba(255,255,255,0.02);"><i class="fa-solid fa-phone text-muted"></i></span>
                        <input type="text" id="phone" name="phone" class="form-control form-control-glass border-start-0 ps-0" placeholder="Ví dụ: 0912345678" pattern="^(03|05|07|08|09)\d{8}$" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="address" class="form-label">Địa chỉ giao hàng:</label>
                    <div class="input-group">
                        <span class="input-group-text form-control-glass border-end-0 align-items-start pt-3" style="background: rgba(255,255,255,0.02);"><i class="fa-solid fa-location-dot text-muted"></i></span>
                        <textarea id="address" name="address" class="form-control form-control-glass border-start-0 ps-0" rows="3" placeholder="Nhập số nhà, tên đường, phường/xã, quận/huyện, thành phố..." minlength="10" maxlength="255" required></textarea>
                    </div>
                </div>

                <hr class="my-4" style="border-color: var(--glass-border);">

                <div class="d-flex gap-3 justify-content-between align-items-center">
                    <a href="<?php echo BASE_URL; ?>/Product/cart" class="btn btn-glass-secondary">
                        <i class="fa-solid fa-arrow-left me-2"></i>Quay lại giỏ hàng
                    </a>
                    <button type="submit" class="btn btn-premium px-5" <?php echo (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) ? 'disabled' : ''; ?>>
                        <i class="fa-solid fa-check me-2"></i>Xác nhận đặt hàng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkoutForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const name = document.getElementById('name').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const address = document.getElementById('address').value.trim();
        
        // Validation patterns
        const nameRegex = /^[\p{L}\s]{2,50}$/u;
        const phoneRegex = /^(03|05|07|08|09)\d{8}$/;
        
        if (name === '' || phone === '' || address === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Thiếu thông tin',
                text: 'Vui lòng điền đầy đủ tất cả các trường!',
                confirmButtonColor: '#0071e3'
            });
            return;
        }
        
        if (!nameRegex.test(name)) {
            Swal.fire({
                icon: 'error',
                title: 'Tên không hợp lệ',
                text: 'Họ và tên chỉ được chứa chữ cái và khoảng trắng, độ dài từ 2-50 ký tự.',
                confirmButtonColor: '#0071e3'
            });
            return;
        }
        
        if (!phoneRegex.test(phone)) {
            Swal.fire({
                icon: 'error',
                title: 'Số điện thoại không hợp lệ',
                text: 'Số điện thoại phải gồm 10 số và bắt đầu bằng các đầu số hợp lệ của Việt Nam (03, 05, 07, 08, 09).',
                confirmButtonColor: '#0071e3'
            });
            return;
        }
        
        if (address.length < 10 || address.length > 255) {
            Swal.fire({
                icon: 'error',
                title: 'Địa chỉ chưa rõ ràng',
                text: 'Địa chỉ giao hàng phải dài từ 10 đến 255 ký tự.',
                confirmButtonColor: '#0071e3'
            });
            return;
        }
        
        // Nếu qua hết thì submit
        form.submit();
    });
});
</script>

<?php include 'app/views/shares/footer.php'; ?>
