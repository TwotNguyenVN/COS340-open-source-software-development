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
        font-size: 0.9rem;
    }

    .form-card {
        animation: fadeInUp 0.5s cubic-bezier(0.25, 1, 0.5, 1);
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .form-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, #ff9f0a 0%, #ff6723 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        flex-shrink: 0;
    }

    .input-icon-wrapper {
        position: relative;
    }

    .input-icon-wrapper .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 0.85rem;
        pointer-events: none;
        z-index: 2;
    }

    .input-icon-wrapper textarea ~ .input-icon {
        top: 1.15rem;
        transform: none;
    }

    .input-icon-wrapper .form-control-glass {
        padding-left: 2.8rem;
    }

    .char-counter {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-align: right;
        margin-top: 0.25rem;
    }

    .skeleton-field {
        background: rgba(255, 255, 255, 0.04);
        border-radius: 10px;
        height: 48px;
        animation: shimmer 1.5s infinite;
    }

    .skeleton-area {
        height: 110px;
    }

    @keyframes shimmer {
        0% { opacity: 0.4; }
        50% { opacity: 0.7; }
        100% { opacity: 0.4; }
    }
</style>

<!-- Breadcrumb -->
<div class="row mb-3">
    <div class="col-md-8 mx-auto">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/Product" class="text-muted text-decoration-none">Sản phẩm</a></li>
                <li class="breadcrumb-item active" style="color: var(--text-main);" aria-current="page">Chỉnh sửa sản phẩm</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="glass-card form-card">
            <!-- Header -->
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="form-icon">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h2 class="text-gradient fw-bold mb-0">Chỉnh sửa sản phẩm</h2>
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Cập nhật thông tin sản phẩm qua API</p>
                </div>
            </div>

            <!-- Loading skeleton (shown while data loads) -->
            <div id="loading-skeleton">
                <div class="mb-4">
                    <div class="skeleton-field mb-3"></div>
                    <div class="skeleton-field skeleton-area mb-3"></div>
                    <div class="row g-3">
                        <div class="col-6"><div class="skeleton-field"></div></div>
                        <div class="col-6"><div class="skeleton-field"></div></div>
                    </div>
                </div>
                <div class="text-center text-muted py-2">
                    <i class="fa-solid fa-spinner fa-spin me-2"></i>Đang tải dữ liệu sản phẩm...
                </div>
            </div>

            <form id="edit-product-form" enctype="multipart/form-data" style="display: none;">
                <input type="hidden" name="csrf_token" value="<?php echo SessionHelper::getCSRFToken(); ?>">
                <input type="hidden" id="id" name="id">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" id="existing_image" name="existing_image">

                <div class="row g-4">
                    <!-- Tên sản phẩm -->
                    <div class="col-12">
                        <label for="name" class="form-label"><i class="fa-solid fa-tag me-1"></i>Tên sản phẩm</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-box input-icon"></i>
                            <input type="text" id="name" name="name" class="form-control form-control-glass" placeholder="Tên sản phẩm..." required>
                        </div>
                    </div>

                    <!-- Mô tả -->
                    <div class="col-12">
                        <label for="description" class="form-label"><i class="fa-solid fa-align-left me-1"></i>Mô tả sản phẩm</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-pen-fancy input-icon"></i>
                            <textarea id="description" name="description" class="form-control form-control-glass" rows="4" placeholder="Mô tả chi tiết..." required></textarea>
                        </div>
                        <div class="char-counter"><span id="desc-count">0</span> ký tự</div>
                    </div>

                    <!-- Giá + Số lượng -->
                    <div class="col-sm-6">
                        <label for="price" class="form-label"><i class="fa-solid fa-coins me-1"></i>Giá bán (VND)</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-dong-sign input-icon"></i>
                            <input type="number" id="price" name="price" class="form-control form-control-glass" step="1000" placeholder="0" required>
                        </div>
                        <div class="error-msg text-danger mt-1 fw-medium" id="price-error" style="font-size: 0.8rem; display: none;">
                            <i class="fa-solid fa-circle-exclamation me-1"></i>Không được nhập số âm!
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <label for="stock" class="form-label"><i class="fa-solid fa-cubes me-1"></i>Số lượng tồn kho</label>
                        <div class="input-icon-wrapper">
                            <i class="fa-solid fa-hashtag input-icon"></i>
                            <input type="number" id="stock" name="stock" class="form-control form-control-glass" step="1" placeholder="0" required>
                        </div>
                        <div class="error-msg text-danger mt-1 fw-medium" id="stock-error" style="font-size: 0.8rem; display: none;">
                            <i class="fa-solid fa-circle-exclamation me-1"></i>Không được nhập số âm!
                        </div>
                    </div>

                    <!-- Ảnh sản phẩm -->
                    <div class="col-sm-6">
                        <label for="image" class="form-label"><i class="fa-solid fa-image me-1"></i>Ảnh sản phẩm</label>
                        <input type="file" id="image" name="image" accept="image/*" class="form-control form-control-glass">
                        <div class="mt-3">
                            <div id="image-preview-wrapper" class="d-flex align-items-center justify-content-center" style="width: 150px; height: 150px; border-radius: 12px; border: 2px dashed var(--glass-border); background: rgba(255, 255, 255, 0.01); overflow: hidden; position: relative; transition: all 0.3s ease;">
                                <div id="preview-placeholder" class="text-center text-muted" style="font-size: 0.8rem; padding: 10px;">
                                    <i class="fa-regular fa-image d-block fs-3 mb-2 opacity-50"></i>
                                    <span>Chưa chọn ảnh</span>
                                </div>
                                <img id="image-preview" src="#" alt="Preview" style="display: none; width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                                <button type="button" id="remove-image-preview" class="btn btn-sm btn-danger d-flex align-items-center justify-content-center" style="display: none; position: absolute; top: 8px; right: 8px; border-radius: 50%; width: 24px; height: 24px; padding: 0; opacity: 0.8; font-size: 0.75rem; border: none; background: rgba(255, 69, 58, 0.9);">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <label for="category_id" class="form-label"><i class="fa-solid fa-folder me-1"></i>Danh mục</label>
                        <select id="category_id" name="category_id" class="form-control form-control-glass" required>
                            <option value="" disabled selected>-- Chọn danh mục --</option>
                        </select>
                    </div>
                </div>

                <hr class="my-4" style="border-color: var(--glass-border);">

                <!-- Actions -->
                <div class="d-flex gap-3 justify-content-between align-items-center">
                    <a href="<?= BASE_URL ?>/Product" class="btn btn-glass-secondary">
                        <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
                    </a>
                    <button type="submit" class="btn btn-premium-warning px-4" id="submit-btn">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>

<script>
// Character counter
document.getElementById('description').addEventListener('input', function() {
    document.getElementById('desc-count').textContent = this.value.length;
});

document.addEventListener("DOMContentLoaded", function() {
    // Live validation for negative numbers
    const priceInput = document.getElementById('price');
    const stockInput = document.getElementById('stock');
    const submitBtn = document.getElementById('submit-btn');

    function validateInputs() {
        let isPriceInvalid = false;
        let isStockInvalid = false;

        const priceVal = parseFloat(priceInput.value);
        if (!isNaN(priceVal) && priceVal < 0) {
            document.getElementById('price-error').style.display = 'block';
            priceInput.style.borderColor = '#ff453a';
            isPriceInvalid = true;
        } else {
            document.getElementById('price-error').style.display = 'none';
            priceInput.style.borderColor = '';
        }

        const stockVal = parseInt(stockInput.value);
        if (!isNaN(stockVal) && stockVal < 0) {
            document.getElementById('stock-error').style.display = 'block';
            stockInput.style.borderColor = '#ff453a';
            isStockInvalid = true;
        } else {
            document.getElementById('stock-error').style.display = 'none';
            stockInput.style.borderColor = '';
        }

        if (isPriceInvalid || isStockInvalid) {
            submitBtn.disabled = true;
        } else {
            submitBtn.disabled = false;
        }
    }

    if (priceInput && stockInput) {
        priceInput.addEventListener('input', validateInputs);
        stockInput.addEventListener('input', validateInputs);
    }

    const currentTheme = localStorage.getItem('theme') || 'dark';
    const productId = <?= isset($product) ? $product->id : (isset($editId) ? $editId : 0) ?>;

    if (!productId) {
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Không tìm thấy ID sản phẩm.',
            background: currentTheme === 'light' ? '#ffffff' : '#1d1d1f',
            color: currentTheme === 'light' ? '#1d1d1f' : '#f5f5f7',
            confirmButtonColor: '#ff453a'
        }).then(() => {
            location.href = '<?= BASE_URL ?>/Product';
        });
        return;
    }

    // Fetch product data
    fetch(`<?= BASE_URL ?>/api/product/${productId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('id').value = data.id;
            document.getElementById('name').value = data.name;
            document.getElementById('description').value = data.description;
            document.getElementById('price').value = data.price;
            document.getElementById('stock').value = data.stock ?? 0;
            document.getElementById('desc-count').textContent = (data.description || '').length;

            if (document.getElementById('existing_image')) {
                document.getElementById('existing_image').value = data.image || '';
            }

            // Preview logic for edit
            const previewWrapper = document.getElementById('image-preview-wrapper');
            const previewPlaceholder = document.getElementById('preview-placeholder');
            const previewImg = document.getElementById('image-preview');
            const removeBtn = document.getElementById('remove-image-preview');

            if (data.image) {
                previewImg.src = `<?= BASE_URL ?>/${data.image}`;
                previewImg.style.display = 'block';
                previewPlaceholder.style.display = 'none';
                removeBtn.style.display = 'flex';
                previewWrapper.style.borderStyle = 'solid';
            } else {
                previewImg.src = '#';
                previewImg.style.display = 'none';
                previewPlaceholder.style.display = 'block';
                removeBtn.style.display = 'none';
                previewWrapper.style.borderStyle = 'dashed';
            }

            const imageInput = document.getElementById('image');
            if (imageInput) {
                imageInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            previewImg.style.display = 'block';
                            previewPlaceholder.style.display = 'none';
                            removeBtn.style.display = 'flex';
                            previewWrapper.style.borderStyle = 'solid';
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    imageInput.value = '';
                    if (document.getElementById('existing_image')) {
                        document.getElementById('existing_image').value = '';
                    }
                    previewImg.src = '#';
                    previewImg.style.display = 'none';
                    previewPlaceholder.style.display = 'block';
                    removeBtn.style.display = 'none';
                    previewWrapper.style.borderStyle = 'dashed';
                });
            }

            // Then load categories
            return fetch('<?= BASE_URL ?>/api/category')
                .then(response => response.json())
                .then(categories => {
                    const categorySelect = document.getElementById('category_id');
                    categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        if (category.id == data.category_id) {
                            option.selected = true;
                        }
                        categorySelect.appendChild(option);
                    });

                    // Show form, hide skeleton
                    document.getElementById('loading-skeleton').style.display = 'none';
                    document.getElementById('edit-product-form').style.display = 'block';
                });
        })
        .catch(error => {
            document.getElementById('loading-skeleton').innerHTML = `
                <div class="text-center py-4">
                    <i class="fa-solid fa-circle-exclamation fs-2 text-danger mb-3 d-block"></i>
                    <h5 class="text-danger">Không thể tải dữ liệu sản phẩm</h5>
                    <p class="text-muted">Vui lòng kiểm tra kết nối và thử lại.</p>
                    <a href="<?= BASE_URL ?>/Product" class="btn btn-glass-secondary mt-2">
                        <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
                    </a>
                </div>
            `;
        });

    // Handle form submission via AJAX
    const editForm = document.getElementById('edit-product-form');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (submitBtn.disabled) return;
            
            // Show loading state
            const originalBtnHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Đang lưu...';

            const formData = new FormData(this);

            $.ajax({
                url: '<?php echo BASE_URL; ?>/api/product/' + productId,
                method: 'POST', // POST with _method=PUT inside formData
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: 'Cập nhật sản phẩm thành công!',
                        background: currentTheme === 'light' ? '#ffffff' : '#1d1d1f',
                        color: currentTheme === 'light' ? '#1d1d1f' : '#f5f5f7',
                        confirmButtonColor: '#ff9f0a'
                    }).then(() => {
                        window.location.href = '<?php echo BASE_URL; ?>/Product';
                    });
                },
                error: function(xhr) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                    
                    let errorMsg = 'Đã xảy ra lỗi khi cập nhật sản phẩm.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            // Collect all error messages
                            let errors = Object.values(xhr.responseJSON.errors).join('<br>');
                            errorMsg = errors;
                        } else if (xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        html: errorMsg,
                        background: currentTheme === 'light' ? '#ffffff' : '#1d1d1f',
                        color: currentTheme === 'light' ? '#1d1d1f' : '#f5f5f7',
                        confirmButtonColor: '#ff453a'
                    });
                }
            });
        });
    }
});
</script>
