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
</style>

<div class="row mb-3">
    <div class="col-md-6 mx-auto">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/Category/list" class="text-muted text-decoration-none">Danh mục</a></li>
                <li class="breadcrumb-item active" style="color: var(--text-main);" aria-current="page">Thêm danh mục</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="glass-card">
            <h2 class="text-gradient fw-bold mb-4"><i class="fa-solid fa-folder-plus me-2 text-primary"></i>Thêm danh mục mới</h2>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 p-3 mb-4">
                    <h5 class="fw-bold mb-2"><i class="fa-solid fa-circle-exclamation me-2"></i>Vui lòng khắc phục các lỗi sau:</h5>
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo BASE_URL; ?>/Category/save">
                <input type="hidden" name="csrf_token" value="<?php echo SessionHelper::getCSRFToken(); ?>">
                <div class="mb-4">
                    <label for="name" class="form-label">Tên danh mục:</label>
                    <input type="text" id="name" name="name" class="form-control form-control-glass" placeholder="Nhập tên danh mục (ví dụ: Điện thoại)..." required>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">Mô tả danh mục:</label>
                    <textarea id="description" name="description" class="form-control form-control-glass" rows="4" placeholder="Mô tả chi tiết về danh mục này..."></textarea>
                </div>

                <hr class="my-4" style="border-color: var(--glass-border);">

                <div class="d-flex gap-3 justify-content-end">
                    <a href="<?php echo BASE_URL; ?>/Category/list" class="btn btn-glass-secondary">
                        <i class="fa-solid fa-xmark me-2"></i>Hủy bỏ
                    </a>
                    <button type="submit" class="btn btn-premium px-4">
                        <i class="fa-solid fa-plus me-2"></i>Thêm danh mục
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
