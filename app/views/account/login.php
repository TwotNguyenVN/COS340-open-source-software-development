<?php include 'app/views/shares/header.php'; ?>

<style>
    .login-wrapper {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-card {
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

    .login-card .form-control {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        color: var(--text-main);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }

    .login-card .form-control:focus {
        background: rgba(255, 255, 255, 0.08);
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.15);
        color: var(--text-main);
    }

    [data-theme="light"] .login-card .form-control {
        background: rgba(0, 0, 0, 0.03);
        color: #1d1d1f;
    }

    [data-theme="light"] .login-card .form-control:focus {
        background: rgba(0, 0, 0, 0.05);
        color: #1d1d1f;
    }

    .login-card .form-label {
        font-weight: 500;
        color: var(--text-muted);
        font-size: 0.85rem;
        margin-bottom: 0.4rem;
    }

    .login-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--accent-color) 0%, #147ce5 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 1.5rem;
        color: white;
    }

    .login-divider {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: var(--text-muted);
        font-size: 0.8rem;
        margin: 1.5rem 0;
    }

    .login-divider::before,
    .login-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--glass-border);
    }
</style>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-icon">
            <i class="fa-solid fa-user-lock"></i>
        </div>
        
        <h2 class="text-gradient fw-bold text-center mb-1">Đăng nhập</h2>
        <p class="text-muted text-center mb-4" style="font-size: 0.85rem;">Vui lòng nhập tên đăng nhập và mật khẩu</p>

        <div id="error-message" class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 p-3 mb-3" style="display: none; font-size: 0.85rem;"></div>

        <form id="login-form">
            <div class="mb-3">
                <label class="form-label" for="username"><i class="fa-regular fa-user me-1"></i>Tên đăng nhập hoặc Email</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Nhập username hoặc email..." required autofocus />
            </div>

            <div class="mb-4">
                <label class="form-label" for="password"><i class="fa-solid fa-lock me-1"></i>Mật khẩu</label>
                <div class="position-relative">
                    <input type="password" id="password" name="password" class="form-control pe-5" placeholder="Nhập mật khẩu..." required />
                    <i class="fa-regular fa-eye position-absolute top-50 end-0 translate-middle-y me-3 text-muted toggle-password" style="cursor: pointer;" onclick="togglePasswordVisibility('password', this)"></i>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-3">
                <a href="<?= BASE_URL ?>/account/forgotPassword" class="text-decoration-none" style="font-size: 0.85rem; color: var(--link-blue-dark);">Quên mật khẩu?</a>
            </div>
            <button class="btn btn-premium w-100 py-2 mb-3" type="submit" id="login-btn">
                <i class="fa-solid fa-right-to-bracket me-2"></i>Đăng nhập
            </button>
        </form>

        <div class="login-divider">hoặc</div>

        <div class="text-center">
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Chưa có tài khoản? 
                <a href="<?= BASE_URL ?>/account/register" class="text-decoration-none" style="color: var(--link-blue-dark);">Đăng ký ngay</a>
            </p>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>

<script>
document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const btn = document.getElementById('login-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang xử lý...';

    const formData = new FormData(this);
    const jsonData = {};
    formData.forEach((value, key) => {
        jsonData[key] = value;
    });

    fetch('<?= BASE_URL ?>/account/checkLogin', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(jsonData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.token) {
            // Store JWT token for API calls
            localStorage.setItem('jwtToken', data.token);
            // Connect socket session immediately
            if (typeof SocketManager !== 'undefined') {
                SocketManager.connect(data.token);
            }
            // Session is already set by server, redirect
            location.href = '<?= BASE_URL ?>/';
        } else {
            const errDiv = document.getElementById('error-message');
            errDiv.innerHTML = '<i class="fa-solid fa-circle-exclamation me-2"></i>' + (data.message || 'Đăng nhập thất bại');
            errDiv.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-right-to-bracket me-2"></i>Đăng nhập';
        }
    })
    .catch(error => {
        console.error('Error logging in:', error);
        const errDiv = document.getElementById('error-message');
        errDiv.innerHTML = '<i class="fa-solid fa-circle-exclamation me-2"></i>Có lỗi kết nối xảy ra!';
        errDiv.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-right-to-bracket me-2"></i>Đăng nhập';
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
