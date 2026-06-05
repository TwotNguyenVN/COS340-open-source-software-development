<?php include 'app/views/shares/header.php'; ?>

<style>
    .register-wrapper {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .register-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        box-shadow: var(--glass-shadow);
        width: 100%;
        max-width: 520px;
        padding: 3rem 2.5rem;
        animation: fadeIn 0.5s cubic-bezier(0.25, 1, 0.5, 1);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .register-card .form-control {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        color: var(--text-main);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }

    .register-card .form-control:focus {
        background: rgba(255, 255, 255, 0.08);
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.15);
        color: var(--text-main);
    }

    [data-theme="light"] .register-card .form-control {
        background: rgba(0, 0, 0, 0.03);
        color: #1d1d1f;
    }

    [data-theme="light"] .register-card .form-control:focus {
        background: rgba(0, 0, 0, 0.05);
        color: #1d1d1f;
    }

    .register-card .form-label {
        font-weight: 500;
        color: var(--text-muted);
        font-size: 0.85rem;
        margin-bottom: 0.4rem;
    }

    .register-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: linear-gradient(135deg, #30d158 0%, #34c759 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 1.5rem;
        color: white;
    }
</style>

<div class="register-wrapper">
    <div class="register-card">
        <div class="register-icon">
            <i class="fa-solid fa-user-plus"></i>
        </div>
        
        <h2 class="text-gradient fw-bold text-center mb-1">Đăng ký tài khoản</h2>
        <p class="text-muted text-center mb-4" style="font-size: 0.85rem;">Tạo tài khoản mới để trải nghiệm đầy đủ tính năng</p>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 p-3 mb-3" style="font-size: 0.85rem;">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/account/save" method="post">
            <div class="mb-3">
                <label for="email" class="form-label"><i class="fa-regular fa-envelope me-1"></i>Địa chỉ Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email của bạn" 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label for="username" class="form-label"><i class="fa-regular fa-user me-1"></i>Tên đăng nhập</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <div class="col-sm-6">
                    <label for="fullname" class="form-label"><i class="fa-solid fa-id-card me-1"></i>Họ và tên</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Họ và tên"
                           value="<?php echo htmlspecialchars($_POST['fullname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <label for="password" class="form-label"><i class="fa-solid fa-lock me-1"></i>Mật khẩu</label>
                    <div class="position-relative">
                        <input type="password" class="form-control pe-5" id="password" name="password" placeholder="Mật khẩu" required>
                        <i class="fa-regular fa-eye position-absolute top-50 end-0 translate-middle-y me-3 text-muted toggle-password" style="cursor: pointer;" onclick="togglePasswordVisibility('password', this)"></i>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label for="confirmpassword" class="form-label"><i class="fa-solid fa-shield-halved me-1"></i>Xác nhận</label>
                    <div class="position-relative">
                        <input type="password" class="form-control pe-5" id="confirmpassword" name="confirmpassword" placeholder="Xác nhận mật khẩu" required>
                        <i class="fa-regular fa-eye position-absolute top-50 end-0 translate-middle-y me-3 text-muted toggle-password" style="cursor: pointer;" onclick="togglePasswordVisibility('confirmpassword', this)"></i>
                    </div>
                </div>
            </div>
            <button class="btn btn-premium w-100 py-2 mb-3" type="submit">
                <i class="fa-solid fa-user-plus me-2"></i>Đăng ký
            </button>
            <div class="text-center">
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Đã có tài khoản? 
                    <a href="<?= BASE_URL ?>/account/login" class="text-decoration-none" style="color: var(--link-blue-dark);">Đăng nhập ngay</a>
                </p>
            </div>
        </form>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>

<script>
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
