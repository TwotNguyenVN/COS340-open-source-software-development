<?php include 'app/views/shares/header.php'; ?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h1 class="text-gradient fw-bold mb-1"><i class="fa-solid fa-tags me-2 text-primary"></i>Quản lý danh mục</h1>
        <p class="text-muted mb-0">Thiết lập và phân loại danh mục sản phẩm cho cửa hàng công nghệ</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="<?php echo BASE_URL; ?>/Category/add" class="btn btn-premium">
            <i class="fa-solid fa-folder-plus me-2"></i>Thêm danh mục mới
        </a>
    </div>
</div>

<div class="glass-card">
    <?php if (empty($categories)): ?>
        <div class="text-center py-4">
            <div class="mb-3 text-muted">
                <i class="fa-regular fa-folder-open fs-2"></i>
            </div>
            <h5>Không có danh mục nào</h5>
            <p class="text-muted mb-0">Vui lòng tạo thêm danh mục sản phẩm mới.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-premium mb-0">
                <thead>
                    <tr>
                        <th style="width: 10%;">ID</th>
                        <th style="width: 30%;">Tên danh mục</th>
                        <th style="width: 45%;">Mô tả chi tiết</th>
                        <th style="width: 15%;" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><span class="badge-premium">#<?php echo $category->id; ?></span></td>
                            <td><strong style="color: var(--text-main);"><?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?></strong></td>
                            <td class="text-muted"><?php echo htmlspecialchars($category->description ?? 'Không có mô tả', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="<?php echo BASE_URL; ?>/Category/edit/<?php echo $category->id; ?>" class="btn btn-sm btn-premium-warning" title="Sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <?php if ((int)($category->product_count ?? 0) > 0): ?>
                                        <button class="btn btn-sm btn-premium-danger" style="opacity: 0.4; cursor: not-allowed;" title="Không thể xóa danh mục này vì đang có sản phẩm" disabled>
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    <?php else: ?>
                                        <button onclick="confirmDelete('<?php echo $category->id; ?>', '<?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>', 0)" class="btn btn-sm btn-premium-danger" title="Xóa">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
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
function confirmDelete(id, name, productCount) {
    const currentTheme = localStorage.getItem('theme') || 'dark';
    
    if (productCount > 0) {
        Swal.fire({
            title: 'Không thể xóa danh mục!',
            text: "Danh mục '" + name + "' hiện đang có " + productCount + " sản phẩm. Vui lòng chuyển hoặc xóa hết sản phẩm thuộc danh mục này trước khi thực hiện xóa!",
            icon: 'error',
            background: currentTheme === 'light' ? '#ffffff' : '#1d1d1f',
            color: currentTheme === 'light' ? '#1d1d1f' : '#f5f5f7',
            confirmButtonColor: '#0071e3',
            confirmButtonText: 'Đã hiểu'
        });
        return;
    }

    Swal.fire({
        title: 'Xác nhận xóa?',
        text: "Bạn có chắc chắn muốn xóa danh mục '" + name + "'? Hành động này không thể hoàn tác!",
        icon: 'warning',
        showCancelButton: true,
        background: currentTheme === 'light' ? '#ffffff' : '#1d1d1f',
        color: currentTheme === 'light' ? '#1d1d1f' : '#f5f5f7',
        confirmButtonColor: '#ff453a',
        cancelButtonColor: '#0071e3',
        confirmButtonText: '<i class="fa-solid fa-trash me-2"></i>Có, hãy xóa!',
        cancelButtonText: 'Hủy bỏ'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?php echo BASE_URL; ?>/Category/delete/' + id + '?csrf_token=<?php echo SessionHelper::getCSRFToken(); ?>';
        }
    })
}
</script>

<?php include 'app/views/shares/footer.php'; ?>
