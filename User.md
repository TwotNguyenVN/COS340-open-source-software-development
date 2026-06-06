# 🔑 Thông Tin Tài Khoản Thử Nghiệm (Test Accounts Info)

File `User.md` này được sử dụng để lưu trữ và quản lý thông tin các tài khoản mẫu phục vụ cho việc kiểm thử và chấm điểm các chức năng phân quyền (RBAC) và bảo mật (JWT) của dự án **NTECH STORE**.

Dưới đây là danh sách các tài khoản đã được khởi tạo sẵn trong cơ sở dữ liệu `my_store`:

| Tên Đăng Nhập (Username) | Mật Khẩu (Password) | Vai Trò (Role) | Họ và Tên (Fullname) | Quyền Hạn / Chức Năng |
| :--- | :--- | :--- | :--- | :--- |
| **admin** | `123456` | `admin` | Administrator | **Toàn quyền quản trị**: Xem, thêm mới, chỉnh sửa, xóa sản phẩm và quản lý danh mục. |
| **user** | `123456` | `user` | Nguyễn Văn A | **Khách hàng**: Xem sản phẩm, thêm vào giỏ hàng, đặt hàng và xem lịch sử mua hàng. |

---

### 🛡️ Lưu ý về Bảo mật (Security Notes)
1. **Mã hóa mật khẩu**: Mật khẩu của các tài khoản đã được mã hóa an toàn bằng thuật toán `PASSWORD_BCRYPT` với tham số `cost = 12` trước khi lưu vào cột `password` trong bảng `account`.
2. **Xác thực API**: Khi đăng nhập qua Endpoint `/account/checkLogin`, hệ thống sẽ trả về một mã **JWT (JSON Web Token)** với thời gian hết hạn là 1 giờ. Mã này cần được gửi kèm trong Header `Authorization: Bearer <Token>` để gọi các API bảo mật.
