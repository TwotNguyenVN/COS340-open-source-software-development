<?php include 'app/views/shares/header.php'; ?>

<style>
    .cart-table-wrapper {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: var(--glass-shadow);
    }

    .quantity-control {
        display: inline-flex;
        border: 1px solid var(--glass-border);
        border-radius: 980px;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.03);
        padding: 2px;
        align-items: center;
    }

    [data-theme="light"] .quantity-control {
        background: rgba(0, 0, 0, 0.03);
    }

    .quantity-control button {
        border: none;
        background: transparent;
        color: var(--text-main);
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 14px;
        border-radius: 50%;
        transition: background-color 0.2s;
    }

    .quantity-control button:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    [data-theme="light"] .quantity-control button:hover {
        background: rgba(0, 0, 0, 0.06);
    }

    .quantity-control input {
        border: none;
        background: transparent;
        width: 38px;
        text-align: center;
        color: var(--text-main);
        font-weight: 600;
        font-size: 13px;
        outline: none;
    }

    .quantity-control input::-webkit-outer-spin-button,
    .quantity-control input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .cart-total-box {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: var(--glass-shadow);
    }

    .cart-thumb {
        width: 64px;
        height: 64px;
        object-fit: contain;
        background: rgba(255, 255, 255, 0.03);
        padding: 6px;
        border-radius: 10px;
        border: 1px solid var(--glass-border);
    }

    [data-theme="light"] .cart-thumb {
        background: rgba(0, 0, 0, 0.02);
    }
</style>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="text-gradient fw-bold mb-1"><i class="fa-solid fa-cart-shopping me-2 text-primary"></i>Giỏ hàng của bạn</h1>
        <p class="text-muted mb-0">Xem lại các sản phẩm và áp dụng ưu đãi trước khi thanh toán</p>
    </div>
</div>

<?php 
// Calculate initial values
$grand_total = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $grand_total += $item['price'] * $item['quantity'];
    }
}

$discount_amount = 0;
if (isset($_SESSION['coupon'])) {
    $coupon = $_SESSION['coupon'];
    if ($coupon['type'] === 'percentage') {
        $discount_amount = $grand_total * ($coupon['value'] / 100);
    } elseif ($coupon['type'] === 'flat') {
        $discount_amount = min($grand_total, $coupon['value']);
    }
}
$shipping_fee = $grand_total >= 50000000 ? 0 : 100000;
$final_total = max(0, $grand_total - $discount_amount) + $shipping_fee;
?>

<?php if (empty($_SESSION['cart'])): ?>
    <div class="glass-card text-center py-5">
        <div class="mb-4 text-muted">
            <i class="fa-solid fa-basket-shopping fs-1 text-indigo-400" style="opacity: 0.6;"></i>
        </div>
        <h3>Giỏ hàng đang trống!</h3>
        <p class="text-muted">Bạn chưa thêm bất kỳ sản phẩm nào vào giỏ hàng của mình.</p>
        <a href="<?php echo BASE_URL; ?>/Product" class="btn btn-premium mt-3">
            <i class="fa-solid fa-arrow-left me-2"></i>Quay lại mua sắm
        </a>
    </div>
<?php else: ?>
    <div class="row">
        <!-- Cart Items List -->
        <div class="col-lg-8">
            <div class="cart-table-wrapper">
                <table class="table table-premium mb-0">
                    <thead>
                        <tr>
                            <th>Ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Giá bán</th>
                            <th>Số lượng</th>
                            <th>Tổng</th>
                            <th>Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $id => $item): 
                            $subtotal = $item['price'] * $item['quantity'];
                        ?>
                            <tr id="cart-row-<?php echo $id; ?>">
                                <td>
                                    <?php if (!empty($item['image']) && file_exists($item['image'])): ?>
                                        <img src="<?php echo BASE_URL . '/' . $item['image']; ?>" class="cart-thumb" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <?php else: ?>
                                        <div class="d-flex justify-content-center align-items-center rounded bg-dark" style="width:64px; height:64px; border: 1px solid var(--glass-border);">
                                            <i class="fa-regular fa-image text-muted fs-4"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold" style="color: var(--text-main);"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                </td>
                                <td>
                                    <?php echo number_format($item['price'], 0, ',', '.'); ?> VND
                                </td>
                                <td>
                                    <div class="quantity-control">
                                        <button type="button" onclick="updateQtyAjax(<?php echo $id; ?>, -1)">-</button>
                                        <input type="number" id="qty-input-<?php echo $id; ?>" value="<?php echo $item['quantity']; ?>" min="1" onchange="changeQtyAjax(<?php echo $id; ?>, this.value)">
                                        <button type="button" onclick="updateQtyAjax(<?php echo $id; ?>, 1)">+</button>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-success" id="subtotal-<?php echo $id; ?>"><?php echo number_format($subtotal, 0, ',', '.'); ?> VND</div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="deleteItemAjax(<?php echo $id; ?>)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mb-4">
                <a href="<?php echo BASE_URL; ?>/Product" class="btn btn-glass-secondary">
                    <i class="fa-solid fa-arrow-left me-2"></i>Tiếp tục mua sắm
                </a>
                <a href="<?php echo BASE_URL; ?>/Product/clearCart" class="btn btn-premium-danger">
                    <i class="fa-solid fa-trash-can me-2"></i>Xóa toàn bộ
                </a>
            </div>
        </div>

        <!-- Checkout Card -->
        <div class="col-lg-4">
            <div class="cart-total-box">
                <h4 class="fw-bold mb-3" style="color: var(--text-main);"><i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i>Tổng đơn hàng</h4>
                
                <!-- Free Shipping Progress Bar -->
                <div class="mb-4 p-3 rounded-3" style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--glass-border);">
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span id="shipping-progress-text" style="font-size: 11px;">Mua thêm <strong class="text-success" id="shipping-needed-text"></strong> để được Miễn phí giao hàng</span>
                        <span id="shipping-status-badge" class="badge bg-warning text-dark" style="font-size: 9px; line-height: 1.2;">Chưa đủ</span>
                    </div>
                    <div class="progress" style="height: 6px; background: rgba(255, 255, 255, 0.08); border-radius: 980px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" id="shipping-progress-bar" style="width: 0%; border-radius: 980px; transition: width 0.4s ease;"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Tạm tính:</span>
                    <span class="fw-bold" id="grand-total-text" style="color: var(--text-main);"><?php echo number_format($grand_total, 0, ',', '.'); ?> VND</span>
                </div>

                <!-- Discount row -->
                <div class="d-flex justify-content-between mb-3" id="discount-row" style="<?php echo isset($_SESSION['coupon']) ? '' : 'display: none !important;' ?>">
                    <span class="text-muted">Giảm giá:</span>
                    <span class="text-danger fw-bold" id="discount-amount-text">-<?php echo number_format($discount_amount, 0, ',', '.'); ?> VND</span>
                </div>

                <div class="d-flex justify-content-between mb-4">
                    <span class="text-muted">Phí giao hàng:</span>
                    <span class="fw-bold" id="shipping-fee-text" style="color: var(--text-main);"><?php echo $shipping_fee > 0 ? number_format($shipping_fee, 0, ',', '.') . ' VND' : 'Miễn phí'; ?></span>
                </div>
                
                <hr style="border-color: var(--glass-border);">
                
                <div class="d-flex justify-content-between mb-4">
                    <span class="fs-5 fw-bold" style="color: var(--text-main);">Tổng cộng:</span>
                    <span class="fs-4 fw-bold text-success" id="final-total-text"><?php echo number_format($final_total, 0, ',', '.'); ?> VND</span>
                </div>

                <!-- Coupon Section -->
                <div class="coupon-box mt-3 mb-4 p-3 rounded-3" style="background: rgba(255, 255, 255, 0.02); border: 1px dashed var(--glass-border);">
                    <h6 class="mb-2 fw-semibold" style="color: var(--text-main);"><i class="fa-solid fa-ticket me-2 text-warning"></i>Mã giảm giá (Coupon)</h6>
                    
                    <div class="input-group mb-2" id="coupon-input-group" style="<?php echo isset($_SESSION['coupon']) ? 'display: none;' : '' ?>">
                        <input type="text" id="coupon-code-input" class="form-control form-control-glass border-end-0" placeholder="Ví dụ: NTECH10, GIARE" style="height: 38px; font-size: 13px;">
                        <button type="button" class="btn btn-premium" onclick="applyCoupon()" style="border-top-left-radius: 0; border-bottom-left-radius: 0; font-size: 13px; padding: 0 15px;">Áp dụng</button>
                    </div>

                    <div id="coupon-applied-status" class="d-flex justify-content-between align-items-center p-2 rounded-2" style="background: rgba(48, 209, 88, 0.1); border: 1px solid rgba(48, 209, 88, 0.2); <?php echo isset($_SESSION['coupon']) ? '' : 'display: none !important;' ?>">
                        <div>
                            <span class="badge bg-success text-white me-1 fw-bold" id="applied-code-badge"><?php echo isset($_SESSION['coupon']) ? $_SESSION['coupon']['code'] : ''; ?></span>
                            <small class="text-success" style="font-size: 11px;" id="applied-code-desc"><?php echo isset($_SESSION['coupon']) ? $_SESSION['coupon']['description'] : ''; ?></small>
                        </div>
                        <button type="button" class="btn btn-sm text-danger border-0 p-1" onclick="removeCoupon()" style="background: transparent;">
                            <i class="fa-solid fa-xmark fs-5"></i>
                        </button>
                    </div>
                </div>

                <a href="<?php echo BASE_URL; ?>/Product/checkout" class="btn btn-premium w-100 py-3 fs-5 text-decoration-none d-block text-center">
                    <i class="fa-solid fa-credit-card me-2"></i>Tiến hành thanh toán
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
// Free Shipping Progress Bar Helpers
function updateFreeShippingProgress(grandTotalStr) {
    const threshold = 50000000; // 50 million VND
    const grandTotal = parseInt(grandTotalStr.replace(/\./g, '')) || 0;
    const progressText = document.getElementById('shipping-progress-text');
    const statusBadge = document.getElementById('shipping-status-badge');
    const progressBar = document.getElementById('shipping-progress-bar');
    
    if (!progressBar) return;
    
    if (grandTotal >= threshold) {
        progressText.innerHTML = '<span class="text-success fw-bold"><i class="fa-solid fa-circle-check me-1"></i>Đơn hàng của bạn đã được Miễn phí giao hàng!</span>';
        statusBadge.textContent = 'Miễn phí';
        statusBadge.className = 'badge bg-success text-white';
        progressBar.style.width = '100%';
    } else {
        const needed = threshold - grandTotal;
        const pct = (grandTotal / threshold) * 100;
        
        progressText.innerHTML = `Mua thêm <strong class="text-warning">${formatNumber(needed)} VND</strong> để được Miễn phí giao hàng`;
        statusBadge.textContent = 'Chưa đủ';
        statusBadge.className = 'badge bg-warning text-dark';
        progressBar.style.width = `${pct}%`;
    }
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Initialize Free Shipping progress bar on page load
document.addEventListener('DOMContentLoaded', () => {
    const grandTotalText = document.getElementById('grand-total-text');
    if (grandTotalText) {
        updateFreeShippingProgress(grandTotalText.textContent);
    }
});

async function updateQtyAjax(productId, delta) {
    const input = document.getElementById(`qty-input-${productId}`);
    let newQty = parseInt(input.value) + delta;
    if (newQty < 1) newQty = 1;
    await sendQtyUpdate(productId, newQty);
}

async function changeQtyAjax(productId, value) {
    let newQty = parseInt(value);
    if (isNaN(newQty) || newQty < 1) newQty = 1;
    await sendQtyUpdate(productId, newQty);
}

async function sendQtyUpdate(productId, newQty) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', newQty);

    try {
        const response = await fetch('<?php echo BASE_URL; ?>/Product/updateCartAjax', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success) {
            document.getElementById(`qty-input-${productId}`).value = data.quantity;
            document.getElementById(`subtotal-${productId}`).textContent = `${data.item_subtotal} VND`;
            document.getElementById('grand-total-text').textContent = `${data.grand_total} VND`;
            document.getElementById('final-total-text').textContent = `${data.final_total} VND`;
            if (document.getElementById('shipping-fee-text')) {
                document.getElementById('shipping-fee-text').textContent = data.shipping_fee;
            }
            
            if (data.discount_amount !== '0') {
                document.getElementById('discount-amount-text').textContent = `-${data.discount_amount} VND`;
            }

            updateCartBadge(data.cart_count);
            updateFreeShippingProgress(data.grand_total);

            if (data.warning) {
                const currentTheme = localStorage.getItem('theme') || 'dark';
                Swal.fire({
                    icon: 'warning',
                    title: 'Giới hạn số lượng',
                    text: data.message,
                    background: currentTheme === 'light' ? '#ffffff' : '#1d1d1f',
                    color: currentTheme === 'light' ? '#1d1d1f' : '#f5f5f7',
                    confirmButtonColor: '#ff9f0a',
                    timer: 3000
                });
            }
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error('Lỗi kết nối:', error);
    }
}

function updateCartBadge(count) {
    let badges = document.querySelectorAll('.cart-badge');
    badges.forEach(badge => {
        if (count > 0) {
            badge.style.display = 'block';
            badge.textContent = count;
        } else {
            badge.style.display = 'none';
        }
    });
}

async function deleteItemAjax(productId) {
    const currentTheme = localStorage.getItem('theme') || 'dark';
    const result = await Swal.fire({
        title: 'Xóa sản phẩm?',
        text: 'Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Đồng ý',
        cancelButtonText: 'Hủy',
        background: currentTheme === 'light' ? '#ffffff' : '#1d1d1f',
        color: currentTheme === 'light' ? '#1d1d1f' : '#f5f5f7',
        confirmButtonColor: '#ff453a',
        cancelButtonColor: '#0071e3'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`<?php echo BASE_URL; ?>/Product/removeFromCart/${productId}?ajax=1`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();
            
            if (data.success) {
                // Fade out row
                const row = document.getElementById(`cart-row-${productId}`);
                if (row) {
                    row.style.transition = 'all 0.4s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(-20px)';
                    setTimeout(() => {
                        row.remove();
                        
                        // Switch UI to empty state if cart is completely empty
                        if (parseInt(data.cart_count) === 0) {
                            const cartContainer = document.querySelector('.main-container');
                            if (cartContainer) {
                                cartContainer.innerHTML = `
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h1 class="text-gradient fw-bold mb-1"><i class="fa-solid fa-cart-shopping me-2 text-primary"></i>Giỏ hàng của bạn</h1>
                                            <p class="text-muted mb-0">Xem lại các sản phẩm và áp dụng ưu đãi trước khi thanh toán</p>
                                        </div>
                                    </div>
                                    <div class="glass-card text-center py-5">
                                        <div class="mb-4 text-muted">
                                            <i class="fa-solid fa-basket-shopping fs-1 text-indigo-400" style="opacity: 0.6;"></i>
                                        </div>
                                        <h3>Giỏ hàng đang trống!</h3>
                                        <p class="text-muted">Bạn chưa thêm bất kỳ sản phẩm nào vào giỏ hàng của mình.</p>
                                        <a href="<?php echo BASE_URL; ?>/Product" class="btn btn-premium mt-3">
                                            <i class="fa-solid fa-arrow-left me-2"></i>Quay lại mua sắm
                                        </a>
                                    </div>
                                `;
                            }
                        }
                    }, 400);
                }
                
                // Update pricing details
                document.getElementById('grand-total-text').textContent = `${data.grand_total} VND`;
                document.getElementById('final-total-text').textContent = `${data.final_total} VND`;
                if (document.getElementById('shipping-fee-text')) {
                    document.getElementById('shipping-fee-text').textContent = data.shipping_fee;
                }
                
                if (data.discount_amount === '0') {
                    document.getElementById('discount-row').style.setProperty('display', 'none', 'important');
                } else {
                    document.getElementById('discount-amount-text').textContent = `-${data.discount_amount} VND`;
                }

                updateCartBadge(data.cart_count);
                updateFreeShippingProgress(data.grand_total);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Đã xóa!',
                    text: data.message,
                    background: currentTheme === 'light' ? '#ffffff' : '#1d1d1f',
                    color: currentTheme === 'light' ? '#1d1d1f' : '#f5f5f7',
                    confirmButtonColor: '#0071e3',
                    timer: 2000
                });
            }
        } catch (error) {
            console.error('Error removing from cart:', error);
            window.location.reload();
        }
    }
}

async function applyCoupon() {
    const codeInput = document.getElementById('coupon-code-input');
    const code = codeInput.value.trim();
    if (!code) return;

    const formData = new FormData();
    formData.append('coupon_code', code);

    try {
        const response = await fetch('<?php echo BASE_URL; ?>/Product/applyCouponAjax', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        const currentTheme = localStorage.getItem('theme') || 'dark';

        if (data.success) {
            document.getElementById('coupon-input-group').style.display = 'none';
            const statusDiv = document.getElementById('coupon-applied-status');
            statusDiv.style.setProperty('display', 'flex', 'important');
            
            document.getElementById('applied-code-badge').textContent = data.coupon_code;
            document.getElementById('applied-code-desc').textContent = data.description;
            
            const discountRow = document.getElementById('discount-row');
            discountRow.style.setProperty('display', 'flex', 'important');
            document.getElementById('discount-amount-text').textContent = `-${data.discount_amount} VND`;
            document.getElementById('final-total-text').textContent = `${data.final_total} VND`;
            if (document.getElementById('shipping-fee-text')) {
                document.getElementById('shipping-fee-text').textContent = data.shipping_fee;
            }
            
            codeInput.value = '';

            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: data.message,
                background: currentTheme === 'light' ? '#ffffff' : '#1d1d1f',
                color: currentTheme === 'light' ? '#1d1d1f' : '#f5f5f7',
                confirmButtonColor: '#0071e3',
                timer: 2000
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Thất bại!',
                text: data.message,
                background: currentTheme === 'light' ? '#ffffff' : '#1d1d1f',
                color: currentTheme === 'light' ? '#1d1d1f' : '#f5f5f7',
                confirmButtonColor: '#ff453a',
                timer: 3000
            });
        }
    } catch (error) {
        console.error('Lỗi khi áp dụng mã:', error);
    }
}

async function removeCoupon() {
    try {
        const response = await fetch('<?php echo BASE_URL; ?>/Product/removeCouponAjax', {
            method: 'POST'
        });
        const data = await response.json();
        const currentTheme = localStorage.getItem('theme') || 'dark';

        if (data.success) {
            document.getElementById('coupon-applied-status').style.setProperty('display', 'none', 'important');
            document.getElementById('coupon-input-group').style.display = 'flex';
            
            document.getElementById('discount-row').style.setProperty('display', 'none', 'important');
            document.getElementById('final-total-text').textContent = `${data.final_total} VND`;
            if (document.getElementById('shipping-fee-text')) {
                document.getElementById('shipping-fee-text').textContent = data.shipping_fee;
            }

            Swal.fire({
                icon: 'info',
                title: 'Đã gỡ mã!',
                text: data.message,
                background: currentTheme === 'light' ? '#ffffff' : '#1d1d1f',
                color: currentTheme === 'light' ? '#1d1d1f' : '#f5f5f7',
                confirmButtonColor: '#0071e3',
                timer: 2000
            });
        }
    } catch (error) {
        console.error('Lỗi khi gỡ mã:', error);
    }
}
</script>

<?php include 'app/views/shares/footer.php'; ?>
