# Kế hoạch Triển khai Module Quản lý User (User Management)

Tài liệu này là bản kế hoạch cực kỳ chi tiết (Blueprint) dành cho cả Frontend (Vue.js) và Backend (Laravel) trong việc xây dựng Module Quản lý User. Kế hoạch được chia làm 2 Phase độc lập để đảm bảo khả năng tracking và testing.

---

## 🚀 PHASE 1: QUẢN LÝ PROFILE CÁ NHÂN (Listener & Artist)
**Mục tiêu:** Xây dựng hệ thống tự phục vụ (Self-service) cho phép người dùng cuối quản lý tài khoản, bảo mật và thiết bị đăng nhập.

### 1.1. Backend (Laravel)

#### A. Database & Models
- Bổ sung cấu hình `fillable` cho model `User` (`name`, `avatar`, `status`).
- Model `ArtistProfile`: Đảm bảo các trường `bio`, `social_links`, `is_verified` được cấu hình đúng.
- **Thách thức kỹ thuật:** Chức năng [API-105] (Quản lý Session) yêu cầu driver `database` hoặc cấu trúc Redis phù hợp để query toàn bộ session của một `user_id`. (Sẽ cần đánh giá lại `SESSION_DRIVER` hoặc dùng Sanctum Tokens).

#### B. Triển khai API (Theo chuẩn ADR)
1. **API Cập nhật Thông tin (API-107 & API-204)**
   - `PUT /{role}/profile`
   - *FormRequest:* Validate `name` (string, max:255), `bio` (đối với artist).
   - *Action:* `UpdateUserProfileAction`. Cập nhật bảng `users` và `artist_profiles`.
2. **API Cập nhật Ảnh đại diện (API-109 & API-205)**
   - `POST /{role}/profile/avatar`
   - *FormRequest:* Validate `avatar` (image, mimes:jpeg,png,webp, max:2048).
   - *Action:* `UploadAvatarAction`. Nhận file, nén/resize ảnh (nếu cần), đẩy lên **MinIO Storage** (S3 compatible), lưu URL vào database, tự động xóa ảnh cũ trên Cloud để tối ưu dung lượng.
3. **API Đổi mật khẩu (API-104 & API-203)**
   - `PUT /{role}/auth/password`
   - *FormRequest:* `current_password` (bắt buộc khớp với DB), `password` (mật khẩu mới, quy tắc strong password), `password_confirmation`.
   - *Action:* `ChangePasswordAction`. Sử dụng `Hash::check()` và `Hash::make()`.
4. **API Quản lý Thiết bị/Phiên đăng nhập (API-105 & API-106)**
   - `GET /{role}/auth/sessions` (Lấy danh sách thiết bị/IP đang đăng nhập).
   - `DELETE /{role}/auth/sessions/{id}` (Thu hồi token/session của một thiết bị cụ thể để đăng xuất từ xa).

### 1.2. Frontend (Vue.js)

#### A. Cấu trúc UI/UX
- Tạo Layout dùng chung: `SettingsLayout.vue` với Sidebar Menu (Hồ sơ, Bảo mật, Đăng ký Artist...).
- **Screen: Profile Settings (`SCR-SHR-04`)**
  - Component `AvatarUpload.vue`: Click để chọn ảnh, có chức năng Preview ảnh trước khi lưu, hiển thị thanh tiến trình (Progress bar) khi đang upload.
  - Form thay đổi thông tin (Tên hiển thị, Tiểu sử).
- **Screen: Security Settings**
  - Form đổi mật khẩu (có nút ẩn/hiện mật khẩu).
  - Component `ActiveSessions.vue`: Hiển thị danh sách thiết bị (VD: "Windows - Chrome", "iPhone 14 - Safari") kèm nút "Đăng xuất thiết bị này".

#### B. Tích hợp Store (Pinia) & Services
- Viết thêm các hàm gọi API trong `src/services/api.ts` hoặc tạo file chuyên biệt `src/services/userService.ts`.
- Mở rộng `authStore` để cập nhật trực tiếp `authStore.user.avatar` và `authStore.user.name` trên giao diện ngay khi API báo thành công (tránh phải reload trang).

---

## 🚀 PHASE 2: HỆ THỐNG QUẢN TRỊ CMS (Admin)
**Mục tiêu:** Cung cấp công cụ mạnh mẽ để Ban quản trị điều hành, kiểm duyệt và xử lý các vấn đề về tài khoản.

### 2.1. Backend (Laravel)

#### A. Logic Truy vấn (Data Retrieval)
- **API Danh sách User (API-320)**
  - `GET /admin/users`
  - Tích hợp package `spatie/laravel-query-builder` để chuẩn hóa API.
  - Hỗ trợ Filters: `?filter[role]=listener`, `?filter[status]=Active`.
  - Hỗ trợ Search: `?filter[search]=thanh@gmail.com`.
  - Hỗ trợ Sort: `?sort=-created_at`.
  - Hỗ trợ Pagination chuẩn.

#### B. Tác vụ Quản trị (Admin Actions)
1. **API Thay đổi Trạng thái (API-322)**
   - `PUT /admin/users/{id}/status`
   - *Action:* `ChangeUserStatusAction`. Cho phép chuyển đổi giữa `Active`, `Suspended`, `Banned`.
   - **Critical Logic:** Nếu chuyển sang `Banned`, hệ thống phải kích hoạt Event để xóa/vô hiệu hóa lập tức toàn bộ Session/Token của user đó, khiến họ bị đăng xuất (Force Logout) ngay trên mọi thiết bị.
2. **API Gửi Email Ép Đổi Mật Khẩu (API-323)**
   - `POST /admin/users/{id}/reset-password`
   - *Action:* Gọi hàm sinh Token của Laravel Password Broker và bắn Notification email (tương tự chức năng Forgot Password của Guest).
3. **API Tạo Nhanh Nghệ Sĩ (API-321)**
   - `POST /admin/users/artist`
   - Bypass luồng tạo Invitation Token. Admin truyền thẳng Name, Email, Stage Name, Password để tạo ngay 1 Artist Profile hợp lệ.
4. **API Phân Quyền Quản Trị (API-325)**
   - `PUT /admin/users/{id}/roles`
   - Gán Spatie Permission/Role cho các tài khoản nội bộ (Admin, Moderator, Content Reviewer...).

### 2.2. Frontend (Vue.js)

#### A. Cấu trúc UI/UX Admin Dashboard
- **Screen: Quản lý Người dùng (`SCR-ADM-03`)**
  - Sử dụng Component `DataTable.vue` có sẵn hoặc xây dựng mới.
  - Thanh công cụ trên cùng: Ô tìm kiếm (Search bar), Dropdown bộ lọc (Role, Status).
  - Bảng dữ liệu hiển thị: ID, Tên, Email, Vai trò (Badge), Trạng thái (Badge màu xanh/đỏ/vàng), Ngày tham gia, Cột Actions.
- **Components Tương tác (Modals & Drawers)**
  - `UserDetailDrawer.vue`: Nhấn vào 1 user sẽ trượt Drawer từ phải sang, hiển thị toàn bộ thông tin chi tiết (Lịch sử nghe nhạc, Số lượng album upload nếu là Artist...).
  - `CreateArtistModal.vue`: Popup form để nhập nhanh dữ liệu tạo Artist.
  - Menu Context (Dấu 3 chấm ở cuối mỗi hàng): Tùy chọn "Khóa tài khoản", "Gửi email đổi mật khẩu", "Phân quyền".

#### B. Tích hợp & State Management
- Tạo `adminUserStore.ts` để lưu trữ trạng thái của bảng danh sách (dữ liệu đang xem, trang hiện tại, bộ lọc đang chọn), giúp Admin không bị reset bộ lọc khi chuyển qua lại giữa các trang.
- Tích hợp Toast Notification (thông báo góc màn hình) sau khi thực hiện thành công các tác vụ nhạy cảm như "Khóa tài khoản thành công".
