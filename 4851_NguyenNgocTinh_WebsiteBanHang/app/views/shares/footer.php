    </div> <!-- End container main-container -->

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <div class="row align-items-center">
                <div class="col-md-6 text-md-start mb-3 mb-md-0">
                    <span>© 2026 <strong>NTECH STORE</strong>. Tất cả quyền lợi được bảo lưu.</span>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="me-3">Phát triển bởi: <strong>Nguyễn Ngọc Tính - 4851</strong></span>
                    <a href="https://github.com" target="_blank" class="text-muted hover-light"><i class="fa-brands fa-github fs-5"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 Bundle JS (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Light/Dark Mode & Global JS Setup -->
    <script>
        // Global AJAX setup to always send JWT token if available
        $.ajaxSetup({
            beforeSend: function(xhr) {
                const token = localStorage.getItem('jwt_token');
                if (token) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            
            function updateToggleIcon(theme) {
                if (theme === 'light') {
                    themeIcon.className = 'fa-solid fa-sun fs-5 text-warning';
                    if (toggleButton) {
                        toggleButton.title = 'Chuyển chế độ tối';
                    }
                } else {
                    themeIcon.className = 'fa-solid fa-moon fs-5';
                    if (toggleButton) {
                        toggleButton.title = 'Chuyển chế độ sáng';
                    }
                }
            }
            
            // Get current theme and load icon state
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            updateToggleIcon(currentTheme);
            
            // Register theme click toggle event
            if (toggleButton) {
                toggleButton.addEventListener('click', function() {
                    const activeTheme = document.documentElement.getAttribute('data-theme') || 'dark';
                    const newTheme = activeTheme === 'dark' ? 'light' : 'dark';
                    
                    document.documentElement.setAttribute('data-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    updateToggleIcon(newTheme);
                });
            }
        });
    </script>
</body>
</html>
