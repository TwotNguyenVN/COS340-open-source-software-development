# Kế hoạch thực hiện dự án Website Bán Hàng (4851_NguyenNgocTinh_WebsiteBanHang)

Dựa trên tài liệu `TAI_LIEU.md`, dự án được chia thành 6 Sprint tương ứng với 6 Bài học. Mỗi Sprint bao gồm các Task cụ thể để hoàn thiện từng tính năng của hệ thống.

## Sprint 1: Khởi tạo dự án & Xây dựng cấu trúc MVC cơ bản (Dựa trên Bài 1)
**Mục tiêu:** Chuẩn bị môi trường, cấu trúc thư mục MVC và tạo ứng dụng hiển thị thông tin sản phẩm đơn giản dùng Session.
- [x] **Task 1.1:** Cài đặt và cấu hình môi trường phát triển (Visual Studio Code, Laragon).
- [x] **Task 1.2:** Khởi tạo ứng dụng Web PHP đơn giản theo cấu trúc MVC (Tạo cấu trúc thư mục `app`, `core`, file `.htaccess`, `index.php`, `DefaultController.php`).
- [x] **Task 1.3:** Xây dựng tính năng quản lý Sản phẩm (Lưu trữ trên Session).
  - [x] Tạo `ProductModel.php` (các thuộc tính ID, Name, Description, Price).
  - [x] Tạo `ProductController.php` (xử lý list, add, edit, delete sử dụng Session).
  - [x] Tạo các View cho sản phẩm: `list.php`, `add.php`, `edit.php`.
- [x] **Task 1.4:** Thực hiện các yêu cầu bổ sung của Bài 1.

## Sprint 2: Tích hợp Cơ sở dữ liệu MySQL (Dựa trên Bài 2)
**Mục tiêu:** Đưa ứng dụng vào thực tế bằng cách lưu trữ và quản lý dữ liệu với MySQL thay vì Session tạm thời.
- [x] **Task 2.1:** Sử dụng Laragon để tạo cơ sở dữ liệu MySQL cho website bán hàng.
- [x] **Task 2.2:** Xây dựng các script SQL và khởi tạo CSDL cùng bảng `products`.
- [x] **Task 2.3:** Cập nhật `ProductModel.php` và tạo class kết nối CSDL (nếu cần) để tương tác trực tiếp với MySQL.
- [x] **Task 2.4:** Khởi tạo và cấu hình lại `ProductController.php` lấy dữ liệu từ DB.
- [x] **Task 2.5:** Cập nhật giao diện (Views) hiển thị, thêm, xóa, sửa tương ứng với model mới.
- [x] **Task 2.6:** Khởi chạy dự án và kiểm thử thao tác CRUD trên CSDL MySQL.
- [x] **Task 2.7:** Hoàn thành các yêu cầu thêm (Yêu cầu bổ sung của Bài 2).

## Sprint 3: Xây dựng chức năng Giỏ hàng, Đặt hàng và Thanh toán (Dựa trên Bài 3)
**Mục tiêu:** Bổ sung nghiệp vụ cốt lõi của website thương mại điện tử là mua hàng và quản lý đơn hàng.
- [x] **Task 3.1:** Tạo các bảng `orders` và `order_details` trong Cơ sở dữ liệu.
- [x] **Task 3.2:** Khởi tạo Session cho giỏ hàng để lưu trữ các sản phẩm khách hàng đang chọn.
- [x] **Task 3.3:** Cập nhật Controller (ví dụ `ProductController` hoặc `CartController`) xử lý chức năng thêm/sửa/xóa sản phẩm trong Giỏ hàng.
- [x] **Task 3.4:** Xây dựng các Views hiển thị Giỏ hàng và trang Checkout (Thanh toán).
- [x] **Task 3.5:** Viết logic xử lý Đặt hàng (Lưu thông tin Giỏ hàng từ Session vào 2 bảng `orders` và `order_details`).
- [x] **Task 3.6:** Khởi chạy và kiểm tra kết quả toàn bộ luồng mua hàng.

## Sprint 4: Chức năng Xác thực người dùng (Dựa trên Bài 4)
**Mục tiêu:** Quản lý tài khoản người dùng, đăng nhập, đăng ký để bảo vệ các chức năng trong hệ thống.
- [x] **Task 4.1:** Cấu hình cơ sở dữ liệu: thêm bảng lưu trữ người dùng (ví dụ: `users`).
- [x] **Task 4.2:** Xây dựng các Models (`UserModel`) để truy xuất dữ liệu tài khoản.
- [x] **Task 4.3:** Tạo các Controllers xử lý nghiệp vụ xác thực (Login, Register, Logout).
- [x] **Task 4.4:** Tạo các trang hiển thị (Views) cho màn hình Đăng nhập và Đăng ký.
- [x] **Task 4.5:** Tạo file `SessionHelper` để quản lý thông tin phiên làm việc của người dùng đăng nhập.
- [x] **Task 4.6:** Cập nhật hệ thống định tuyến (Routing) để chặn/bảo vệ các trang yêu cầu quyền đăng nhập.

## Sprint 5: Xây dựng RESTful API (Dựa trên Bài 5)
**Mục tiêu:** Cung cấp API để hệ thống có thể kết nối với các ứng dụng khác (Frontend SPA, Mobile App).
- [x] **Task 5.1:** Cập nhật `ProductModel` để hỗ trợ các phương thức RESTful.
- [x] **Task 5.2:** Xây dựng các API Controller tương ứng (`ProductApiController`, `CategoryApiController`).
- [x] **Task 5.3:** Cấu hình hệ thống định tuyến (Routing) cho API.
- [x] **Task 5.4:** Cập nhật Views để quản lý sản phẩm thông qua gọi API bằng JavaScript Fetch.
- [x] **Task 5.5:** Khởi chạy dự án và kiểm thử API qua Postman. để kiểm thử các API đã viết.
- [x] **Task 5.6:** Thực hiện yêu cầu bổ sung của Bài 5.

## Sprint 6: Bảo mật RESTful API với JWT (Dựa trên Bài 6)
**Mục tiêu:** Nâng cấp khả năng bảo mật cho hệ thống API bằng cơ chế JSON Web Token (JWT).
- [x] **Task 6.1:** Tải và cài đặt thư viện JWT cho PHP.
- [x] **Task 6.2:** Tạo lớp tiện ích xử lý JWT (tạo token, mã hóa, giải mã và xác thực).
- [x] **Task 6.3:** Cập nhật API Đăng nhập để trả về JWT khi xác thực thành công.
- [x] **Task 6.4:** Cập nhật các API xử lý Dữ liệu ở Sprint 5 để yêu cầu xác thực JWT qua Headers trước khi thực thi.
- [x] **Task 6.5:** Cập nhật các trang hiển thị/Frontend gửi kèm JWT Token khi gọi API lên server.
- [x] **Task 6.6:** Sử dụng Postman và Trình duyệt để kiểm thử luồng API được bảo vệ bằng JWT.
