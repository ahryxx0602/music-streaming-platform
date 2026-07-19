# [SCR-ADM-03] Quản lý Người dùng Đa vai trò (User Management Hub)

> [!IMPORTANT]
> **I18N REQUIREMENT:** Tất cả các đoạn Text, Label, Placeholder, Message hiển thị trong tài liệu Screen Specs này khi triển khai vào code thực tế đều **KHÔNG ĐƯỢC HARDCODE**. Bắt buộc phải sử dụng key đa ngôn ngữ qua hàm `$t()` của `vue-i18n`.


> **Mô tả ngắn:** Phân hệ quản lý toàn bộ nhân sự và người dùng của nền tảng, được chia thành 3 Tab tách biệt tương ứng với 3 tệp người dùng: Listener, Artist và Admin Staff.

## 1. Thông tin chung (Meta)
*   **Module:** System / Administration
*   **Route / URL:** `/admin/users`
*   **Layout sử dụng:** `AdminLayout.vue`
*   **Quyền truy cập:** `[PER-ADMIN]` (Chỉ Super Admin mới thấy Tab Staff).
*   **Component con (Children):**
    *   `ListenerTab.vue` (Tab hiển thị danh sách Listener).
    *   `ArtistTab.vue` (Tab hiển thị danh sách Artist).
    *   `StaffTab.vue` (Tab hiển thị danh sách Staff).

## 2. Thành phần giao diện (UI Elements) & Logic Từng Tab

### [SCR-ADM-03A] Quản lý Người Nghe (Listener Management)
*   **UI Elements:**
    *   `Bảng dữ liệu`: Cột (Email, Tên, Ngày đăng ký, Trạng thái Active/Banned).
    *   `Thanh tìm kiếm`: Input text tìm theo Tên/Email.
    *   `Hành động (Actions)`: Nút `Khóa tài khoản (Ban)` màu đỏ, Nút `Reset Pass` (Gửi email).
*   **Quy tắc nghiệp vụ:**
    *   **Không có tính năng Create:** Listener tự đăng ký ngoài public.
    *   **[RULE-ADM-LST-01]:** Khóa tài khoản (Ban) chỉ cập nhật `status = 'banned'` (Soft Delete / Deactivate), không xóa vật lý khỏi DB để giữ nguyên lịch sử nghe nhạc và dữ liệu Streams. Listener bị khóa sẽ nhận lỗi `[ERR-AUTH-403]` khi cố đăng nhập.

### [SCR-ADM-03B] Quản lý Nghệ Sĩ (Artist Management)
*   **UI Elements:**
    *   `Bảng dữ liệu`: Cột (Nghệ danh, Email, Tổng bài hát, Tổng Streams, Trạng thái).
    *   `Nút + Tạo mới Nghệ Sĩ`: Mở Form tạo trực tiếp (Bỏ qua khâu gửi Invite).
    *   `Form Tạo/Sửa`: Input (Nghệ danh, Email, Mật khẩu tạm).
    *   `Hành động`: Nút `Xem Chi tiết (View Profile)`, Nút `Sửa thông tin`, Nút `Khóa (Suspend)`.
*   **Màn hình Chi tiết Nghệ sĩ (Artist Profile View):** 
    *   Khi bấm "Xem chi tiết", Admin được chuyển đến một trang hiển thị riêng về Nghệ sĩ này.
    *   Trang này cung cấp cái nhìn tổng quan về Album, Bài hát của riêng Artist đó.
    *   Tích hợp các nút hành động nhanh: **`+ Tải bài hát cho Nghệ sĩ này`** và **`+ Tạo Album cho Nghệ sĩ này`**. Khi bấm, hệ thống sẽ mở Component Form Upload của màn hình `[SCR-ADM-10]` nhưng tự động điền sẵn (Auto-fill) tên Nghệ sĩ để tránh nhầm lẫn.
*   **Quy tắc nghiệp vụ:**
    *   **[RULE-ADM-ART-01] - Tạo trực tiếp:** Khi Admin chủ động tạo Artist, Backend tự động Insert vào `[DB-users]` với role `Artist` và khởi tạo luôn 1 dòng rỗng bên bảng `[DB-artist_profiles]`.
    *   **[RULE-ADM-ART-02] - Hệ quả khi Khóa (Cascade Suspend):** Việc khóa Artist sẽ ngay lập tức ẩn toàn bộ bài hát và album của họ khỏi hệ thống Public. Tuy nhiên, Backend **tuyệt đối không** chạy vòng lặp cập nhật `status = 'hidden'` vào bảng `[DB-songs]` (để tránh mất trạng thái gốc Approved/Pending khi mở khóa sau này). Thay vào đó, áp dụng **Global Scope (Tầng Truy Vấn)**: Mọi API Public gọi bài hát đều bị kẹp thêm điều kiện `WHERE artist.status = 'active'`.

### [SCR-ADM-03C] Quản lý Quản trị viên (Staff / System Admin Management)
*   **UI Elements:**
    *   `Bảng dữ liệu`: Cột (Tên nhân viên, Email, Vai trò phân quyền, Lần đăng nhập cuối).
    *   `Nút + Thêm Nhân Viên`: Mở form tạo Staff.
    *   `Form Phân Quyền`: Checkbox hoặc Select chọn Nhóm quyền (Roles: Content Moderator, Finance, Super Admin).
    *   `Hành động`: Nút `Đổi quyền (Assign Role)`, Nút `Thu hồi (Revoke/Delete)`.
*   **Quy tắc nghiệp vụ:**
    *   **[RULE-ADM-STF-01] - RBAC (Role-Based Access Control):** Việc phân quyền áp dụng mô hình RBAC. Giám đốc có thể cấp quyền `Content Moderator` (Chỉ được duyệt nhạc) hoặc `Finance` (Chỉ xem doanh thu).
    *   **[RULE-ADM-STF-02] - Super Admin Guard:** Nhân viên không thể tự xóa hoặc tự hạ quyền của chính mình. Chỉ có Super Admin cấp cao nhất mới có quyền truy cập vào Tab `[SCR-ADM-03C]` này.

## 3. Liên kết dữ liệu & Logic (State & APIs)
### Tương tác API (Network)
*   `[API-320]` - `GET /api/v1/admin/users?role={listener|artist|staff}&search={text}&page={1}` (Lấy danh sách phân trang, kết hợp tìm kiếm).
*   `[API-321]` - `POST /api/v1/admin/users/artist` (Tạo nhanh Artist).
*   `[API-322]` - `PUT /api/v1/admin/users/{id}/status` (Khóa/Mở khóa User).
*   `[API-ADM-06]` - `PUT /api/v1/admin/users/{id}/roles` (Gán quyền cho Staff).

## 4. Tác động Cơ sở dữ liệu (Database Impact)
*   **Ghi (Write):** 
    *   Update trạng thái cột `status` trong `[DB-users]`.
    *   Insert vào `[DB-users]` và `[DB-artist_profiles]` (Nếu dùng tính năng Tạo Artist).
    *   Update bảng trung gian phân quyền `[DB-model_has_roles]` (Nếu sử dụng package Spatie Permission cho RBAC).
