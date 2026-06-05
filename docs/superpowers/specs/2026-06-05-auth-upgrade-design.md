# Kế Hoạch Thiết Kế Nâng Cấp Hệ Thống Xác Thực (Auth Upgrade)

**Ngày tạo:** 05/06/2026
**Dự án:** 4851_NguyenNgocTinh_WebsiteBanHang

## 1. Mục Tiêu
Nâng cấp hệ thống xác thực hiện tại (Bài 4) để hỗ trợ đăng nhập linh hoạt hơn và cung cấp tính năng lấy lại mật khẩu an toàn, thân thiện với người dùng (UX cao).

## 2. Tính Năng Chốt Yêu Cầu
1. **Đăng nhập linh hoạt:** Người dùng có thể điền `username` hoặc `email` vào ô đăng nhập đều hợp lệ.
2. **Khôi phục mật khẩu (Forgot Password) qua OTP:** Thay vì dùng link reset truyền thống, hệ thống gửi một mã OTP gồm 6 chữ số qua Email. Toàn bộ quá trình xác thực OTP và đổi mật khẩu mới được thực hiện mượt mà trên cùng một màn hình (sử dụng AJAX Fetch API) mà không tải lại trang.

## 3. Thiết Kế Database (Cơ sở dữ liệu)
- **Bảng `account`**: 
  - Cần thêm trường `email VARCHAR(255) UNIQUE` để lưu trữ email của người dùng.
  - Sửa đổi Form đăng ký (Register) để yêu cầu nhập thêm Email.

- **Bảng `password_resets` (Mới)**:
  - Để lưu trữ mã OTP tạm thời phục vụ việc quên mật khẩu.
  - Các trường: `id`, `email`, `otp_code` (INT 6 số), `expires_at` (Thời gian hết hạn, thường là 10-15 phút), `created_at`.

## 4. Thiết Kế Luồng Xử Lý (Data Flow)

### 4.1. Cập nhật Đăng nhập & Đăng ký
- **Đăng ký (Register):** Thêm trường `email`. Kiểm tra cả `username` và `email` xem có bị trùng hay không trước khi Insert.
- **Đăng nhập (Login):** Câu lệnh SQL truy vấn user sẽ sửa thành: 
  `SELECT * FROM account WHERE username = :input OR email = :input`

### 4.2. Luồng Quên Mật Khẩu (Forgot Password)
1. **Bước 1 (Gửi OTP):** Khách hàng nhập Email. AJAX POST gửi lên `/account/forgotPassword`. Backend kiểm tra Email có tồn tại không. Nếu có, sinh random OTP 6 số, lưu vào bảng `password_resets` kèm `expires_at`. Dùng PHPMailer gửi OTP qua email. Backend trả về JSON báo thành công.
2. **Bước 2 (Xác thực OTP):** Giao diện web ẩn ô Email, hiện ra ô "Nhập mã OTP 6 số". Khách hàng nhập, AJAX POST lên `/account/verifyOtp`. Backend kiểm tra OTP khớp và chưa hết hạn. Nếu đúng, trả về JSON cho phép đổi mật khẩu.
3. **Bước 3 (Đổi Mật Khẩu):** Giao diện hiện ô "Mật khẩu mới". Khách hàng nhập, AJAX POST lên `/account/resetPassword` (kèm theo email và OTP để xác thực bảo mật một lần nữa). Backend update hash password mới vào bảng `account`. Xóa OTP trong bảng `password_resets`. Trả về thành công và chuyển hướng ra trang Đăng nhập.

## 5. Các Component & Files Cần Sửa/Tạo
- `database.sql` (Cập nhật schema)
- `app/models/AccountModel.php` (Thêm hàm check email, đổi query checkLogin, xử lý lưu/check OTP)
- `app/controllers/AccountController.php` (Thêm các endpoints API cho Forgot Password)
- `app/views/account/register.php` (Thêm ô input Email)
- `app/views/account/login.php` (Cập nhật placeholder "Username hoặc Email")
- `app/views/account/forgot_password.php` (Tạo mới màn hình nhập OTP & Reset mật khẩu dùng AJAX)
- Bổ sung thư viện **PHPMailer** để gửi thư.

---
*Tài liệu này được tạo ra sau phiên Brainstorming/Grill Session với người dùng.*
