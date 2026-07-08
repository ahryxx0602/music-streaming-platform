# [SCR-SHR-04] Cài đặt Tài khoản (Account Settings)

> **Mô tả ngắn:** Màn hình cài đặt tổng hợp cho người dùng đã đăng nhập (Listener/Artist). Giao diện dạng Tab (General Profile & Security) cho phép cập nhật thông tin cá nhân (tên, avatar) và thông tin bảo mật (mật khẩu, sessions).

## 1. Thông tin chung (Meta)
*   **Module:** Authentication / Profile
*   **Route / URL:** `/settings`
*   **Layout sử dụng:** `SettingsLayout` (Tabbed interface).
*   **Quyền truy cập:** `[PER-AUTH]` (Yêu cầu đăng nhập).
*   **Component con (Children):**
    *   `ProfileSettingsTab.vue` (Tab Cài đặt chung - Avatar, Tên)
    *   `SecuritySettingsTab.vue` (Tab Bảo mật - Đổi mật khẩu)
    *   `DeviceSessionsList.vue` (Danh sách các thiết bị đang đăng nhập)

## 2. Thành phần giao diện (UI Elements)

### Tab 1: Cài đặt chung (General Profile)
*   **Ảnh đại diện (Avatar):** Component cho phép tải lên ảnh mới, crop và preview ảnh.
*   **Các ô nhập liệu (Inputs):**
    *   `Tên hiển thị`: Input text (Bắt buộc).
    *   `Email`: Input text (Chỉ đọc, hiển thị tooltip nếu muốn đổi phải liên hệ Support).
*   **Nút bấm:** `Lưu hồ sơ` (Save Profile).

### Tab 2: Bảo mật (Security)
*   **Đổi mật khẩu:**
    *   `Mật khẩu hiện tại`: Input password (Ẩn nếu login qua OAuth2).
    *   `Mật khẩu mới` & `Xác nhận mật khẩu`: Input password.
*   **Quản lý phiên đăng nhập (Sessions):**
    *   Danh sách các thiết bị (Browser, OS, IP, Last active).
    *   Nút `Đăng xuất thiết bị này` hoặc `Đăng xuất tất cả thiết bị khác`.
*   **Xác thực 2 bước (2FA):** Banner giới thiệu (Coming Soon).

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **Tab General:**
    *   `[API-107]` - `PUT /api/v1/listener/profile` (Update name, settings)
    *   `[API-109]` - `POST /api/v1/listener/profile/avatar` (Upload avatar - multipart)
    *   *(Nếu role là Artist thì dùng `[API-211]` và `[API-212]`)*
*   **Tab Security:**
    *   `[API-104]` - `PUT /api/v1/listener/auth/password` (Đổi mật khẩu)
    *   `[API-105]` - `GET /api/v1/listener/auth/sessions` (Lấy danh sách sessions)
    *   `[API-106]` - `DELETE /api/v1/listener/auth/sessions/{id}` (Revoke session)

## 4. Quy tắc nghiệp vụ (Business Rules)

### Cập nhật Profile
*   **[RULE-PRF-01]:** Ảnh Avatar dung lượng tối đa 2MB, định dạng JPEG/PNG/WebP. Hệ thống tự động resize về 300x300px để tối ưu lưu trữ.

### Đổi mật khẩu (Change Password)
*   **[RULE-SEC-01] - Xác thực mật khẩu cũ:** Bắt buộc nhập chính xác `current_password`. Trả về 422 nếu sai.
*   **[RULE-SEC-02] - Trường hợp OAuth2 (Social Login):** Nếu user đăng nhập bằng Google/Facebook mà chưa từng thiết lập mật khẩu hệ thống, ẩn ô nhập `current_password`, chỉ hiển thị Form "Thiết lập mật khẩu".

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Ghi (Write):**
    *   Cập nhật `name`, `avatar` vào `[DB-users]`.
    *   Cập nhật `password` đã mã hóa vào `[DB-users]`.
    *   Xóa bản ghi trong bảng lưu trữ session (Session Driver DB/Redis).
