# Quy Trình Làm Việc Git (Git Workflow)

Quy trình này bắt buộc áp dụng cho mọi yêu cầu phát triển tính năng mới trong dự án, đảm bảo mã nguồn được quản lý chuyên nghiệp, minh bạch và an toàn.

## Các bước thực hiện tiêu chuẩn:

1. **Tiếp nhận & Thông báo chuyển nhánh**:
   - Khi nhận được một yêu cầu mới hoàn toàn (không liên quan đến tính năng đang code trên nhánh hiện tại), AI có trách nhiệm thông báo cho người dùng về việc sẽ đóng gói code hiện tại và tạo nhánh mới.

2. **Đóng gói nhánh hiện tại (Lưu trữ code)**:
   - Nếu nhánh hiện tại đang có thay đổi dang dở, AI sẽ tự động thực hiện:
     - Thêm tất cả thay đổi: `git add .`
     - Commit theo chuẩn Conventional Commits: `git commit -m "feat/fix: <mô tả>"`
     - Đẩy lên GitHub: `git push origin <tên-nhánh-hiện-tại>`

3. **Tạo Pull Request (PR) & Merge vào `main`**:
   - Khi tính năng ở nhánh hiện tại đã xong, code được push lên GitHub.
   - Tiến hành tạo Pull Request (PR) và Merge (Gộp code) vào nhánh `main` trực tiếp trên GitHub.
   - Quay lại máy cục bộ, chuyển sang nhánh `main` và đồng bộ code mới nhất:
     - `git checkout main`
     - `git pull origin main`

4. **Tạo nhánh mới để làm việc**:
   - Từ nhánh `main` (đã được cập nhật mới nhất), khởi tạo nhánh mới để thực thi yêu cầu của người dùng.
   - Cú pháp đặt tên nhánh: `feature/<tên-ngắn-gọn>` hoặc `fix/<tên-lỗi>`.
   - Lệnh: `git checkout -b feature/<tên-tính-năng>`
   - Mọi thay đổi code từ lúc này sẽ diễn ra hoàn toàn độc lập trên nhánh mới.

---
**Quy ước chung:**
- Không code trực tiếp trên nhánh `main`.
- Luôn đảm bảo `main` là code sạch và ổn định.
- Mỗi nhánh chỉ nên giải quyết một tính năng hoặc một lỗi cụ thể.
- **Commit liên tục sau mỗi yêu cầu:** Bất cứ khi nào hoàn thành xong một yêu cầu nhỏ hoặc một đoạn thảo luận mang lại thay đổi về code/tài liệu, AI sẽ lập tức tạo commit và push lên nhánh hiện tại để đảm bảo tiến độ luôn được lưu lại an toàn.
