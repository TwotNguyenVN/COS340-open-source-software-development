# Hướng Dẫn Cấu Hình & Chạy Dự Án Khi Clone Trên Máy Mới 🚀

Tài liệu này hướng dẫn chi tiết các bước cần thiết để khởi chạy dự án **NTECH STORE** sau khi clone mã nguồn về một máy tính mới sử dụng môi trường phát triển **Laragon**.

---

## 1. Khởi động Laragon 🖥️
- Mở ứng dụng **Laragon** trên máy tính của bạn.
- Nhấp vào nút **Start All** để khởi động Web Server (Apache/Nginx) và Hệ quản trị Cơ sở dữ liệu (MySQL).

---

## 2. Khởi tạo Cơ sở dữ liệu (Database) 🗄️
Dự án sử dụng cơ sở dữ liệu có tên là `my_store`. Bạn cần tạo mới cơ sở dữ liệu này và nạp dữ liệu mẫu vào:

> [!IMPORTANT]  
> Tên cơ sở dữ liệu bắt buộc phải trùng khớp với cấu hình, mặc định là `my_store`.

### Các bước thực hiện:
1. Mở công cụ quản lý cơ sở dữ liệu đi kèm với Laragon (như **HeidiSQL**) hoặc bất kỳ công cụ quản lý MySQL nào bạn đang sử dụng.
2. Tạo mới một cơ sở dữ liệu có tên là `my_store` với bảng mã (Collation): `utf8mb4_unicode_ci` (hoặc `utf8mb4_general_ci`).
3. Nạp (Import) tệp tin SQL mẫu nằm ở thư mục gốc của dự án:  
   [database.sql](file:///c:/laragon/www/4851_NguyenNgocTinh_PTPM_MNM/4851_NguyenNgocTinh_WebsiteBanHang/database.sql) vào database `my_store` vừa tạo.

> [!TIP]  
> Bạn cũng có thể thực thi nhanh bằng dòng lệnh trong PowerShell (chạy dưới quyền Admin nếu cần) để tự động tạo và nhập cơ sở dữ liệu:
> ```powershell
> # 1. Tạo Database
> C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS my_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
> 
> # 2. Nạp dữ liệu cấu trúc bảng và dữ liệu mẫu
> cmd.exe /c "C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe -u root --default-character-set=utf8mb4 my_store < C:\laragon\www\4851_NguyenNgocTinh_PTPM_MNM\4851_NguyenNgocTinh_WebsiteBanHang\database.sql"
> ```
> *(Lưu ý: Thay thế `mysql-8.0.30-winx64` bằng phiên bản MySQL thực tế trên Laragon của bạn).*

---

## 3. Cài đặt các Thư viện phụ thuộc (Composer Dependencies) 📦
Dự án sử dụng thư viện **Firebase PHP-JWT** để tạo và giải mã token JWT. Thư mục `vendor/` mặc định không được đẩy lên GitHub, do đó bạn cần khôi phục lại:

### Các bước thực hiện:
1. Mở Terminal và di chuyển vào thư mục dự án `4851_NguyenNgocTinh_WebsiteBanHang`.
2. Nếu máy tính của bạn đã được cài đặt **Composer** toàn cục, hãy chạy lệnh:
   ```bash
   composer install
   ```
3. Nếu máy chưa cấu hình Composer toàn cục, bạn có thể chạy trực tiếp bằng PHP và Composer của Laragon bằng lệnh PowerShell sau:
   ```powershell
   C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe C:\laragon\bin\composer\composer.phar install
   ```
   *(Lưu ý: Thay thế `php-8.1.10-Win32-vs16-x64` bằng thư mục PHP thực tế có trong thư mục `C:\laragon\bin\php\` trên máy tính của bạn).*

---

## 4. Cấu hình Kết nối Cơ sở dữ liệu (Database Config) ⚙️
Nếu môi trường MySQL trên Laragon của bạn sử dụng tài khoản khác hoặc có mật khẩu (mặc định của Laragon là User: `root`, Password: trống), vui lòng chỉnh sửa thông tin kết nối tương ứng tại file cấu hình:
- [database.php](file:///c:/laragon/www/4851_NguyenNgocTinh_PTPM_MNM/4851_NguyenNgocTinh_WebsiteBanHang/app/config/database.php)
```php
private $host = "localhost";
private $db_name = "my_store";
private $username = "root";
private $password = ""; // Điền mật khẩu MySQL của bạn nếu có
```

---

## 5. Truy cập & Trải nghiệm dự án 🚀
Sau khi hoàn tất toàn bộ các bước thiết lập ở trên, bạn truy cập website thông qua trình duyệt web:

- **Đường dẫn mặc định:** `http://localhost:88/4851_NguyenNgocTinh_PTPM_MNM/4851_NguyenNgocTinh_WebsiteBanHang/Product`
- **Thông tin tài khoản thử nghiệm:**
  
  | Tên đăng nhập (Username) | Mật khẩu (Password) | Vai trò (Role) | Chức năng kiểm thử |
  | :--- | :--- | :--- | :--- |
  | **admin** | `123456` | `admin` | **Quản trị viên**: Có toàn quyền Quản lý sản phẩm (CRUD) & Danh mục. |
  | **user** | `123456` | `user` | **Khách hàng**: Trải nghiệm xem sản phẩm, giỏ hàng, đặt hàng & xem đơn hàng. |

---
*Chúc bạn thiết lập thành công và có những trải nghiệm tuyệt vời cùng **NTECH STORE**!* 💻✨
