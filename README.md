# NTECH STORE - Website Bán Hàng Máy Tính & Linh Kiện 💻🚀
> **Sinh viên thực hiện:** Nguyễn Ngọc Tịnh  
> **MSSV:** 4851  
> **Môn học:** Phát triển Phần mềm Mã nguồn mở (PTPM_MNM)

---

## 🌟 Giới thiệu Dự án
**NTECH STORE** là một website bán hàng linh kiện máy tính hoàn chỉnh được thiết kế và xây dựng theo mô hình kiến trúc **MVC (Model-View-Controller)** chuẩn hóa trong PHP. Hệ thống hỗ trợ đầy đủ luồng nghiệp vụ mua bán hàng từ việc duyệt sản phẩm, phân mục danh mục, quản lý giỏ hàng, đặt hàng, thanh toán đến hệ thống đăng ký/đăng nhập người dùng bảo mật cao.

Dự án sở hữu giao diện được thiết kế theo phong cách **Apple Premium Design (Liquid Glass UI)** cao cấp, hỗ trợ chuyển đổi chế độ Sáng/Tối (Dark/Light mode) linh hoạt, tích hợp các thư viện UI hiện đại như SweetAlert2 cho trải nghiệm người dùng cao cấp.

---

## 🛠️ Công nghệ Sử dụng & Thư viện
- **Ngôn ngữ**: PHP (v8.3+), HTML5, CSS3, JavaScript (ES6+).
- **Cơ sở dữ liệu**: MySQL (v8.0+).
- **Môi trường phát triển**: Laragon (Web Server Apache/Nginx + MySQL).
- **Kiến trúc**: MVC thuần, định tuyến qua `.htaccess` và tập trung tại `index.php`.
- **Thư viện Giao diện (CSS/JS)**:
  - **Bootstrap 5**: Xây dựng Layout Responsive hoàn hảo trên mọi thiết bị.
  - **Font Awesome 6**: Hệ thống biểu tượng giao diện hiện đại.
  - **SweetAlert2**: Thông báo pop-up trực quan, sang trọng.
  - **Google Fonts (Inter)**: Typography tinh tế.
- **Thư viện Backend**:
  - **Firebase PHP-JWT (firebase/php-jwt)**: Quản lý, cấp phát và xác thực mã token bảo mật cho hệ thống RESTful API.

---

## ✨ Các Tính năng Nổi bật theo 6 Sprints

### 1. Kiến trúc MVC & Session (Sprint 1)
- Xây dựng luồng xử lý Controller - Model - View riêng biệt.
- Sử dụng Sessions để lưu trữ tạm thời dữ liệu sản phẩm phục vụ thử nghiệm trước khi nối CSDL.

### 2. Tích hợp Cơ sở dữ liệu MySQL (Sprint 2)
- Toàn bộ dữ liệu được quản lý lưu trữ trong DB `my_store`.
- Thực hiện đầy đủ các thao tác thêm, xóa, sửa, xem (CRUD) trực tiếp với bảng `products` của CSDL.
- Xử lý tải ảnh sản phẩm lên thư mục `public/images` an toàn.

### 3. Nghiệp vụ Giỏ hàng & Thanh toán (Sprint 3)
- Cơ chế Giỏ hàng thông minh (thêm sản phẩm, tăng/giảm số lượng trực tiếp bằng AJAX).
- Hệ thống thanh toán hoàn chỉnh lưu thông tin đơn hàng vào bảng `orders` (thông tin khách, trạng thái đơn) và chi tiết vào bảng `order_details`.

### 4. Xác thực & Phân quyền Người dùng (Sprint 4)
- Mã hóa mật khẩu an toàn bằng thuật toán `PASSWORD_BCRYPT` với cost `12`.
- Hệ thống đăng ký tài khoản mới và đăng nhập thông qua CSDL.
- Sử dụng `SessionHelper` để kiểm tra quyền truy cập và chặn các trang quản lý đối với người dùng chưa đăng nhập.

### 5. Xây dựng RESTful API & JS Fetch (Sprint 5)
- Viết API endpoints trả về JSON cho Danh mục (`/api/category`) và Sản phẩm (`/api/product`).
- Hỗ trợ các phương thức HTTP: `GET`, `POST`, `PUT`, `DELETE`.
- Thay đổi cách render trang danh sách, thêm và sửa sản phẩm hoàn toàn bằng cách gọi Fetch API của Javascript (Client-Side Rendering) thay cho PHP server rendering.

### 6. Bảo mật RESTful API bằng JSON Web Token (JWT) (Sprint 6)
- Tích hợp lớp bảo mật JWT với Token có hiệu lực 1 giờ và thuật toán mã hóa `HS256` cùng khóa bí mật `"HUTECH"`.
- Endpoint đăng nhập API (`/account/checkLogin`) trả về mã JWT khi tài khoản trùng khớp.
- Gửi kèm mã JWT thông qua `Authorization: Bearer <Token>` khi fetch dữ liệu từ Client lên Server để truy vấn dữ liệu sản phẩm.

---

## 📂 Cấu trúc Thư mục Dự án

```bash
4851_NguyenNgocTinh_WebsiteBanHang/
│
├── app/
│   ├── config/
│   │   └── database.php       # Cấu hình kết nối CSDL MySQL
│   │
│   ├── controllers/
│   │   ├── AccountController.php      # Đăng nhập, đăng ký, đăng xuất
│   │   ├── CategoryApiController.php  # API cho Danh mục
│   │   ├── ProductController.php      # Điều phối Views sản phẩm, giỏ hàng
│   │   └── ProductApiController.php   # API sản phẩm bảo mật bằng JWT
│   │
│   ├── models/
│   │   ├── AccountModel.php   # Tương tác bảng account (đăng nhập/ký)
│   │   ├── CategoryModel.php  # Tương tác bảng categories
│   │   └── ProductModel.php   # Tương tác bảng products và CRUD dữ liệu
│   │
│   ├── utils/
│   │   └── JWTHandler.php     # Class tiện ích sinh/xác thực mã JWT Token
│   │
│   └── views/
│       ├── account/           # Giao diện Đăng nhập, Đăng ký
│       ├── category/          # Giao diện danh mục
│       ├── product/           # Giao diện List, Add, Edit, Detail, Cart, Checkout
│       └── shares/            # Header, Footer dùng chung (tích hợp Dark/Light theme)
│
├── public/
│   ├── css/                   # Stylesheets tuỳ chỉnh phong cách Apple
│   └── images/                # Nơi lưu trữ ảnh upload của sản phẩm
│
├── vendor/                    # Các thư viện PHP tải qua Composer (JWT)
├── .htaccess                  # Cấu hình định tuyến đẹp (Rewriting URL)
├── composer.json              # Khai báo thư viện firebase/php-jwt
├── database.sql               # File SQL cấu trúc và dữ liệu mẫu của CSDL
├── index.php                  # Điểm khởi chạy của dự án (Bootstrap & Router)
└── README.md                  # File hướng dẫn này
```

---

## 🚀 Hướng dẫn Cài đặt & Khởi chạy

### Bước 1: Khởi chạy CSDL MySQL
1. Khởi động Web Server (Laragon) của bạn.
2. Mở công cụ quản lý MySQL của bạn (HeidiSQL, phpMyAdmin, DBeaver,...).
3. Tạo một Database mới có tên là `my_store`.
4. Mở file [database.sql](file:///d:/laragon/www/4851_NguyenNgocTinh_PTPM_MNM/4851_NguyenNgocTinh_WebsiteBanHang/database.sql) nằm ở thư mục gốc của dự án, sao chép toàn bộ nội dung và thực thi (Run) trên Database `my_store` vừa tạo để khởi tạo cấu trúc bảng (`products`, `categories`, `orders`, `order_details`, `account`) cùng các dữ liệu mẫu.

### Bước 2: Cài đặt Dependencies qua Composer
Mở Terminal tại thư mục dự án và chạy lệnh sau để tự động tải thư viện xử lý token JWT:
```bash
composer install
```
*(Nếu hệ thống chưa nhận diện lệnh `composer`, vui lòng sử dụng file thực thi của Laragon hoặc gọi đường dẫn trực tiếp: `d:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe d:\laragon\bin\composer\composer.phar install`)*.

### Bước 3: Cấu hình Kết nối CSDL
Nếu thông số MySQL của bạn khác với mặc định của Laragon (User: `root`, Password: trống, Host: `localhost`), hãy mở file [database.php](file:///d:/laragon/www/4851_NguyenNgocTinh_PTPM_MNM/4851_NguyenNgocTinh_WebsiteBanHang/app/config/database.php) để sửa lại cấu hình kết nối.

### Bước 4: Khởi chạy và Trải nghiệm
1. Truy cập trang web theo URL (Mặc định khi sử dụng Laragon Virtual Hosts):  
   `http://4851-nguyenngoctinh-websitebanhang.test` hoặc `http://localhost/4851_NguyenNgocTinh_PTPM_MNM/4851_NguyenNgocTinh_WebsiteBanHang/Product`.
2. Hệ thống sẽ tự động chặn và đưa bạn đến màn hình Đăng nhập do cơ chế JWT bảo vệ REST API lấy danh sách sản phẩm.
3. Đăng ký một tài khoản mới tại nút "Đăng ký ngay" hoặc đăng nhập trực tiếp bằng tài khoản mẫu:
   - **Tên đăng nhập**: `admin`
   - **Mật khẩu**: `123456`
4. Trải nghiệm đầy đủ tính năng Thêm/Sửa/Xóa sản phẩm bằng AJAX Fetch API, Quản lý danh mục, Thêm vào giỏ hàng và Đặt hàng lưu trữ dữ liệu vào CSDL thời gian thực!

### Bước 5: Sao lưu / Cập nhật dữ liệu từ CSDL vào file `database.sql`
Khi bạn thêm sản phẩm hoặc thay đổi cấu trúc bảng trên giao diện web và muốn lưu lại trạng thái dữ liệu mới nhất vào file `database.sql` trong dự án để đẩy lên GitHub, hãy mở Terminal/PowerShell và chạy lệnh:
- **Nếu đang ở thư mục gốc của repository (`D:\laragon\www\4851_NguyenNgocTinh_PTPM_MNM`):**
  ```powershell
  & "D:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqldump.exe" -u root my_store > 4851_NguyenNgocTinh_WebsiteBanHang\database.sql
  ```
- **Nếu đang ở trong thư mục dự án (`D:\laragon\www\4851_NguyenNgocTinh_PTPM_MNM\4851_NguyenNgocTinh_WebsiteBanHang`):**
  ```powershell
  & "D:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqldump.exe" -u root my_store > database.sql
  ```
*(Thay thế `"D:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqldump.exe"` bằng đường dẫn thực tế đến file `mysqldump.exe` trong thư mục cài đặt Laragon của bạn nếu có sự thay đổi).*

---

## 📈 Danh sách các API Endpoints (RESTful APIs)

| HTTP Method | API Route | Chức năng | Phân quyền (JWT) |
| :--- | :--- | :--- | :--- |
| **POST** | `/account/checkLogin` | Đăng nhập tài khoản & nhận mã Token JWT | Công khai (Public) |
| **GET** | `/api/product` | Lấy danh sách sản phẩm dưới dạng JSON | 🔑 **Bảo mật (JWT Required)** |
| **GET** | `/api/product/{id}` | Lấy chi tiết thông tin 1 sản phẩm theo ID | Công khai (Public) |
| **POST** | `/api/product` | Thêm mới 1 sản phẩm mới vào CSDL | Công khai (Public) |
| **PUT** | `/api/product/{id}` | Cập nhật thông tin sản phẩm theo ID | Công khai (Public) |
| **DELETE**| `/api/product/{id}` | Xóa bỏ sản phẩm khỏi CSDL theo ID | Công khai (Public) |
| **GET** | `/api/category` | Lấy danh sách danh mục (categories) | Công khai (Public) |

---
*Chúc bạn có những trải nghiệm tuyệt vời khi học tập và phát triển cùng **NTECH STORE**!* 🚀💻✨
