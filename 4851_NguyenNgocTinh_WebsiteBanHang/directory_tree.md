# Cấu trúc thư mục dự án: NTECH STORE (WebsiteBanHang)

Dưới đây là sơ đồ cấu trúc cây thư mục của dự án (đã bỏ qua thư mục bên thứ ba `vendor` và các tệp cấu hình sinh tự động để đảm bảo tính trực quan):

```text
4851_NguyenNgocTinh_WebsiteBanHang/
├── app/                           # Thư mục chính chứa mã nguồn ứng dụng (MVC)
│   ├── config/                    # Cấu hình hệ thống
│   │   ├── database.php           # Cấu hình kết nối Cơ sở dữ liệu MySQL
│   │   ├── migration.php          # File chạy khởi tạo database migration
│   │   └── update_images.php      # File cập nhật đường dẫn ảnh mặc định
│   ├── controllers/               # Lớp Điều khiển (Controllers)
│   │   ├── AccountController.php     # Quản lý tài khoản (Đăng nhập, đăng ký, đăng xuất)
│   │   ├── CategoryApiController.php # API danh sách danh mục sản phẩm (JSON)
│   │   ├── CategoryController.php    # Quản trị danh mục sản phẩm (Thêm, Sửa, Xóa)
│   │   ├── DefaultController.php     # Trang chủ (Homepage) và hiển thị chung
│   │   ├── ProductApiController.php  # API danh sách sản phẩm (JSON)
│   │   └── ProductController.php     # Quản trị sản phẩm & Giỏ hàng (Thêm, Sửa, Xóa, Checkout)
│   ├── helpers/                   # Lớp hỗ trợ (Helpers)
│   │   └── SessionHelper.php      # Hỗ trợ quản lý session, phân quyền Admin và bảo mật CSRF
│   ├── models/                    # Lớp Dữ liệu (Models)
│   │   ├── AccountModel.php       # Xử lý truy vấn tài khoản người dùng
│   │   ├── CategoryModel.php      # Xử lý truy vấn danh mục sản phẩm & Ràng buộc xóa
│   │   └── ProductModel.php       # Xử lý truy vấn sản phẩm & Quản lý tồn kho (Stock)
│   ├── utils/                     # Các công cụ tiện ích bổ trợ
│   │   └── JWTHandler.php         # Tạo và xác thực mã JWT Token
│   └── views/                     # Lớp Giao diện (Views)
│       ├── account/               # Các trang tài khoản
│       │   ├── login.php          # Giao diện Đăng nhập
│       │   └── register.php       # Giao diện Đăng ký tài khoản mới
│       ├── category/              # Các trang quản trị danh mục
│       │   ├── add.php            # Giao diện Thêm danh mục mới
│       │   ├── edit.php           # Giao diện Chỉnh sửa danh mục
│       │   └── list.php           # Giao diện Danh sách danh mục (Bảo vệ xóa SweetAlert2)
│       ├── home/                  # Trang chủ
│       │   └── index.php          # Giao diện Trang chủ (Banner, Hot Deal responsive)
│       ├── product/               # Các trang sản phẩm & mua sắm
│       │   ├── add.php            # Giao diện Thêm sản phẩm mới (Tải ảnh lên)
│       │   ├── cart.php           # Giao diện Giỏ hàng (Cập nhật số lượng qua Ajax, Coupon)
│       │   ├── checkout.php       # Giao diện Điền thông tin thanh toán đơn hàng
│       │   ├── edit.php           # Giao diện Chỉnh sửa sản phẩm & Cập nhật số lượng tồn
│       │   ├── list.php           # Giao diện Quản trị danh sách sản phẩm
│       │   ├── orderConfirmation.php # Giao diện Xác nhận đơn hàng đặt thành công
│       │   └── show.php           # Giao diện Chi tiết thông tin sản phẩm
│       └── shares/                # Các thành phần giao diện dùng chung
│           ├── footer.php         # Phần chân trang (Footer & Script xử lý theme)
│           └── header.php         # Phần đầu trang (Header, Global Nav & Định nghĩa CSS biến màu)
├── public/                        # Thư mục công cộng chứa tài nguyên tĩnh tải lên
│   └── uploads/                   # Nơi lưu trữ ảnh sản phẩm tải lên từ hệ thống
├── .htaccess                      # Tệp cấu hình Apache viết lại đường dẫn (Rewrite URL)
├── composer.json                  # Cấu hình các gói thư viện dependency của Composer
├── composer.lock                  # Lưu trữ phiên bản cài đặt chính xác của các thư viện
├── database.sql                   # Bản kết xuất cơ sở dữ liệu MySQL mẫu (Tiếng Việt)
├── index.php                      # File khởi chạy chính của dự án (Bootstrap & Routing)
└── README.md                      # Hướng dẫn cài đặt và sử dụng dự án
```

### Chi tiết các tầng nghiệp vụ:
- **Routing**: Tất cả các yêu cầu được chuyển hướng qua `index.php` nhờ tệp cấu hình `.htaccess`, sau đó được định tuyến đến Controller tương ứng.
- **Model**: Tương tác trực tiếp với cơ sở dữ liệu bằng PDO để thực hiện CRUD.
- **View**: Sử dụng PHP trộn mã HTML5 cùng hệ thống CSS biến Apple Design System (hỗ trợ Light/Dark mode) và bộ thư viện Icons FontAwesome 6, thông báo SweetAlert2.
- **Controller**: Tiếp nhận đầu vào của người dùng, giao tiếp với Model để xử lý nghiệp vụ dữ liệu và nạp View thích hợp để trả về trình duyệt.
