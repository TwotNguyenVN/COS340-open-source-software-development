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
                        <input type="text" id="name" name="name" class="form-control form-control-glass border-start-0 ps-0" placeholder="Ví dụ: Nguyễn Văn A" minlength="5" maxlength="50" pattern="^[\p{L}\s]{5,50}$" required>
                    </div>
                    <div class="text-danger small mt-1 fw-medium d-none ms-1" id="name-error"></div>
                </div>

                <div class="mb-4">
                    <label for="phone" class="form-label">Số điện thoại liên hệ:</label>
                    <div class="input-group">
                        <span class="input-group-text form-control-glass border-end-0" style="background: rgba(255,255,255,0.02);"><i class="fa-solid fa-phone text-muted"></i></span>
                        <input type="text" id="phone" name="phone" class="form-control form-control-glass border-start-0 ps-0" placeholder="Ví dụ: 0912345678" pattern="^0\d{9}$" required>
                    </div>
                    <div class="text-danger small mt-1 fw-medium d-none ms-1" id="phone-error"></div>
                </div>

                <div class="mb-4">
                    <label for="address" class="form-label">Địa chỉ giao hàng:</label>
                    <div class="input-group">
                        <span class="input-group-text form-control-glass border-end-0 align-items-start pt-3" style="background: rgba(255,255,255,0.02);"><i class="fa-solid fa-location-dot text-muted"></i></span>
                        <textarea id="address" name="address" class="form-control form-control-glass border-start-0 ps-0" rows="3" placeholder="Ví dụ: 123 Nguyễn Văn Khối..." minlength="10" maxlength="255" pattern="^\d+.*" required></textarea>
                    </div>
                    <div class="text-danger small mt-1 fw-medium d-none ms-1" id="address-error"></div>
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
    const nameInput = document.getElementById('name');
    const phoneInput = document.getElementById('phone');
    const addressInput = document.getElementById('address');
    
    const nameRegex = /^[\p{L}\s]{5,50}$/u;
    const phoneRegex = /^0\d{9}$/;
    const addressRegex = /^\d+/; // Bắt đầu bằng số

    function showInlineError(input, errorId, text) {
        const errorDiv = document.getElementById(errorId);
        errorDiv.textContent = text;
        errorDiv.classList.remove('d-none');
        
        input.classList.add('border-danger', 'text-danger');
        input.classList.remove('border-success');
        input.previousElementSibling.classList.add('border-danger', 'text-danger');
        input.previousElementSibling.classList.remove('border-success');
    }

    function clearInlineError(input, errorId) {
        const errorDiv = document.getElementById(errorId);
        errorDiv.textContent = '';
        errorDiv.classList.add('d-none');
        
        input.classList.remove('border-danger', 'text-danger');
        input.classList.add('border-success');
        input.previousElementSibling.classList.remove('border-danger', 'text-danger');
        input.previousElementSibling.classList.add('border-success');
    }

    // Real-time validation on blur
    nameInput.addEventListener('blur', function() {
        const val = this.value.trim();
        if(val === '') {
            showInlineError(this, 'name-error', 'Vui lòng điền họ và tên.');
        } else if(!nameRegex.test(val)) {
            showInlineError(this, 'name-error', 'Tên phải là ký tự chữ và tối thiểu 5 ký tự.');
        } else {
            clearInlineError(this, 'name-error');
        }
    });

    phoneInput.addEventListener('blur', function() {
        const val = this.value.trim();
        if(val === '') {
            showInlineError(this, 'phone-error', 'Vui lòng điền số điện thoại.');
        } else if(!phoneRegex.test(val)) {
            showInlineError(this, 'phone-error', 'Phải bắt đầu bằng số 0, không có chữ và đủ 10 số.');
        } else {
            clearInlineError(this, 'phone-error');
        }
    });

    addressInput.addEventListener('blur', function() {
        const val = this.value.trim();
        if(val === '') {
            showInlineError(this, 'address-error', 'Vui lòng điền địa chỉ giao hàng.');
        } else if(!addressRegex.test(val)) {
            showInlineError(this, 'address-error', 'Địa chỉ phải bắt đầu bằng số (Ví dụ: 123 Nguyễn Văn Khối).');
        } else if (val.length < 10) {
            showInlineError(this, 'address-error', 'Vui lòng nhập chi tiết hơn (tối thiểu 10 ký tự).');
        } else {
            clearInlineError(this, 'address-error');
        }
    });
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const name = nameInput.value.trim();
        const phone = phoneInput.value.trim();
        const address = addressInput.value.trim();
        
        let hasError = false;
        
        if (name === '') {
            showInlineError(nameInput, 'name-error', 'Vui lòng điền họ và tên.');
            hasError = true;
        } else if (!nameRegex.test(name)) {
            showInlineError(nameInput, 'name-error', 'Tên phải là ký tự chữ và tối thiểu 5 ký tự.');
            hasError = true;
        }
        
        if (phone === '') {
            showInlineError(phoneInput, 'phone-error', 'Vui lòng điền số điện thoại.');
            hasError = true;
        } else if (!phoneRegex.test(phone)) {
            showInlineError(phoneInput, 'phone-error', 'Phải bắt đầu bằng số 0, không có chữ và đủ 10 số.');
            hasError = true;
        }
        
        if (address === '') {
            showInlineError(addressInput, 'address-error', 'Vui lòng điền địa chỉ giao hàng.');
            hasError = true;
        } else if (!addressRegex.test(address) || address.length < 10) {
            showInlineError(addressInput, 'address-error', 'Địa chỉ phải bắt đầu bằng số và tối thiểu 10 ký tự.');
            hasError = true;
        }
        
        if (hasError) return;
        
        form.submit();
    });
});
</script>

<?php include 'app/views/shares/footer.php'; ?>
