<?php include 'app/views/shares/header.php'; ?>

<style>
    .forgot-wrapper {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .forgot-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        box-shadow: var(--glass-shadow);
        width: 100%;
        max-width: 440px;
        padding: 3rem 2.5rem;
        animation: fadeIn 0.5s cubic-bezier(0.25, 1, 0.5, 1);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .forgot-card .form-control {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        color: var(--text-main);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }

    .forgot-card .form-control:focus {
        background: rgba(255, 255, 255, 0.08);
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.15);
        color: var(--text-main);
    }

    [data-theme="light"] .forgot-card .form-control {
        background: rgba(0, 0, 0, 0.03);
        color: #1d1d1f;
    }

    [data-theme="light"] .forgot-card .form-control:focus {
        background: rgba(0, 0, 0, 0.05);
        color: #1d1d1f;
    }

    .forgot-card .form-label {
        font-weight: 500;
        color: var(--text-muted);
        font-size: 0.85rem;
        margin-bottom: 0.4rem;
    }

    .forgot-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: linear-gradient(135deg, #ff9f0a 0%, #ff375f 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 1.5rem;
        color: white;
    }

    .step-section {
        display: none;
    }
    
    .step-section.active {
        display: block;
        animation: fadeIn 0.3s ease-in-out;
    }
</style>

<div class="forgot-wrapper">
    <div class="forgot-card">
        <div class="forgot-icon">
            <i class="fa-solid fa-key"></i>
        </div>
        
        <h2 class="text-gradient fw-bold text-center mb-1">Khôi phục mật khẩu</h2>
        <p id="step-desc" class="text-muted text-center mb-4" style="font-size: 0.85rem;">Nhập email của bạn để nhận mã xác nhận</p>

        <div id="error-message" class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 p-3 mb-3" style="display: none; font-size: 0.85rem;"></div>
        <div id="success-message" class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-3 p-3 mb-3" style="display: none; font-size: 0.85rem;"></div>

        <!-- Step 1: Nhập Email -->
        <div id="step-email" class="step-section active">
            <div class="mb-4">
                <label class="form-label" for="email"><i class="fa-regular fa-envelope me-1"></i>Địa chỉ Email</label>
                <input type="email" id="email" class="form-control" placeholder="Nhập email đăng ký..." required autofocus />
            </div>
            <button class="btn btn-premium w-100 py-2 mb-3" id="btn-send-otp">
                <i class="fa-solid fa-paper-plane me-2"></i>Gửi mã xác nhận
            </button>
        </div>

        <!-- Step 2: Nhập OTP -->
        <div id="step-otp" class="step-section">
            <div class="mb-4">
                <label class="form-label" for="otp"><i class="fa-solid fa-hashtag me-1"></i>Mã OTP (6 số)</label>
                <input type="text" id="otp" class="form-control text-center fs-4 letter-spacing-2" placeholder="------" maxlength="6" />
            </div>
            <button class="btn btn-premium w-100 py-2 mb-3" id="btn-verify-otp">
                <i class="fa-solid fa-check-circle me-2"></i>Xác thực mã
            </button>
        </div>

        <!-- Step 3: Đổi mật khẩu -->
        <div id="step-password" class="step-section">
            <div class="mb-3">
                <label class="form-label" for="new_password"><i class="fa-solid fa-lock me-1"></i>Mật khẩu mới</label>
                <div class="position-relative">
                    <input type="password" id="new_password" class="form-control pe-5" placeholder="Nhập mật khẩu mới..." />
                    <i class="fa-regular fa-eye position-absolute top-50 end-0 translate-middle-y me-3 text-muted toggle-password" style="cursor: pointer;" onclick="togglePasswordVisibility('new_password', this)"></i>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label" for="confirm_password"><i class="fa-solid fa-shield-halved me-1"></i>Xác nhận mật khẩu</label>
                <div class="position-relative">
                    <input type="password" id="confirm_password" class="form-control pe-5" placeholder="Xác nhận lại mật khẩu..." />
                    <i class="fa-regular fa-eye position-absolute top-50 end-0 translate-middle-y me-3 text-muted toggle-password" style="cursor: pointer;" onclick="togglePasswordVisibility('confirm_password', this)"></i>
                </div>
            </div>
            <button class="btn btn-premium w-100 py-2 mb-3" id="btn-reset-password">
                <i class="fa-solid fa-floppy-disk me-2"></i>Đổi mật khẩu
            </button>
        </div>

        <div class="text-center mt-3">
            <a href="<?= BASE_URL ?>/account/login" class="text-decoration-none text-muted" style="font-size: 0.85rem;">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại đăng nhập
            </a>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>

<script>
    let currentEmail = '';

    function showError(msg) {
        const err = document.getElementById('error-message');
        err.style.display = 'block';
        err.innerHTML = '<i class="fa-solid fa-circle-exclamation me-2"></i>' + msg;
        document.getElementById('success-message').style.display = 'none';
    }

    function showSuccess(msg) {
        const suc = document.getElementById('success-message');
        suc.style.display = 'block';
        suc.innerHTML = '<i class="fa-solid fa-circle-check me-2"></i>' + msg;
        document.getElementById('error-message').style.display = 'none';
    }

    function switchStep(stepId, desc) {
        document.querySelectorAll('.step-section').forEach(el => el.classList.remove('active'));
        document.getElementById(stepId).classList.add('active');
        document.getElementById('step-desc').innerText = desc;
    }

    document.getElementById('btn-send-otp').addEventListener('click', function() {
        const email = document.getElementById('email').value.trim();
        if(!email) return showError('Vui lòng nhập email!');

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang gửi...';

        fetch('<?= BASE_URL ?>/account/apiSendOtp', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({email: email})
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i>Gửi mã xác nhận';
            if(data.success) {
                currentEmail = email;
                showSuccess('Mã xác nhận đã được gửi đến email của bạn!');
                switchStep('step-otp', 'Nhập mã gồm 6 chữ số vừa được gửi đến email');
            } else {
                showError(data.message || 'Lỗi gửi mã OTP');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i>Gửi mã xác nhận';
            showError('Lỗi kết nối máy chủ');
        });
    });

    document.getElementById('btn-verify-otp').addEventListener('click', function() {
        const otp = document.getElementById('otp').value.trim();
        if(!otp || otp.length !== 6) return showError('Vui lòng nhập đúng 6 số OTP!');

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang kiểm tra...';

        fetch('<?= BASE_URL ?>/account/apiVerifyOtp', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({email: currentEmail, otp: otp})
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check-circle me-2"></i>Xác thực mã';
            if(data.success) {
                showSuccess('Xác thực thành công. Mời bạn tạo mật khẩu mới.');
                switchStep('step-password', 'Mật khẩu mới của bạn');
            } else {
                showError(data.message || 'OTP không hợp lệ');
            }
        });
    });

    document.getElementById('btn-reset-password').addEventListener('click', function() {
        const newPass = document.getElementById('new_password').value;
        const confirmPass = document.getElementById('confirm_password').value;
        const otp = document.getElementById('otp').value.trim();

        if(!newPass) return showError('Vui lòng nhập mật khẩu mới!');
        if(newPass !== confirmPass) return showError('Mật khẩu xác nhận không khớp!');

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang xử lý...';

        fetch('<?= BASE_URL ?>/account/apiResetPassword', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({email: currentEmail, otp: otp, password: newPass})
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                showSuccess('Đổi mật khẩu thành công! Đang chuyển hướng...');
                setTimeout(() => location.href = '<?= BASE_URL ?>/account/login', 2000);
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk me-2"></i>Đổi mật khẩu';
                showError(data.message || 'Có lỗi xảy ra');
            }
        });
    });

    function togglePasswordVisibility(inputId, iconElement) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            iconElement.classList.remove('fa-eye');
            iconElement.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            iconElement.classList.remove('fa-eye-slash');
            iconElement.classList.add('fa-eye');
        }
    }
</script>
