# [SCR-SHR-05] Trung tâm thông báo (Notification Center)

> **Mô tả ngắn:** Giao diện xem toàn bộ thông báo hệ thống dành cho Listener và Artist (cập nhật nhạc mới, kết quả duyệt bài hát, thông báo từ admin).

## 1. Thông tin chung (Meta)
*   **Module:** General / Notifications
*   **Route / URL:** `/notifications` (Trang chuyên biệt) và hiển thị dạng Dropdown ở Topbar.
*   **Layout sử dụng:** `MainLayout` (Listener) hoặc `ArtistLayout` (Artist).
*   **Quyền truy cập:** `[PER-AUTH]` (Yêu cầu đăng nhập).
*   **Component con (Children):**
    *   `NotificationList.vue` (Danh sách các thẻ thông báo)
    *   `NotificationItem.vue` (Thẻ chi tiết một thông báo)

## 2. Thành phần giao diện (UI Elements)

*   **Topbar Notification Bell:** Biểu tượng cái chuông trên Navbar có gắn Badge (chấm đỏ + số lượng thông báo chưa đọc).
*   **Danh sách thông báo:**
    *   Các thông báo chưa đọc có nền màu khác (VD: xanh nhạt) để phân biệt.
    *   Các thông báo đã đọc có nền trắng/xám, mờ hơn.
    *   Mỗi thẻ thông báo gồm: Icon, Nội dung (Title, Message), Thời gian (VD: "2 giờ trước").
*   **Các nút bấm:**
    *   `Đánh dấu tất cả đã đọc` (Mark all as read).
    *   Click vào một thông báo chưa đọc → Tự động đánh dấu đã đọc và chuyển hướng đến màn hình liên quan (VD: Trang chi tiết bài hát, Album mới).

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **Lấy danh sách thông báo:**
    *   `[API-160]` (Listener) / `[API-250]` (Artist) - `GET /api/v1/.../notifications`
*   **Cập nhật trạng thái:**
    *   `[API-161]` (Listener) / `[API-251]` (Artist) - `PUT /api/v1/.../notifications/{id}/read`
    *   `[API-162]` (Listener) / `[API-252]` (Artist) - `PUT /api/v1/.../notifications/read-all`

## 4. Quy tắc nghiệp vụ (Business Rules)

*   **[RULE-NOTI-01] - Phân loại thông báo:**
    *   *Artist Upload Result*: Khi Admin duyệt (`Approved`) hoặc từ chối (`Rejected`) bài hát. Click vào → Đến trang quản lý bài hát.
    *   *System Alert*: Thông báo hệ thống, bảo trì.
*   **[RULE-NOTI-02] - Real-time:** (Tùy chọn) Bắt sự kiện Broadcast từ Laravel (pusher/soketi) để hiển thị thông báo ngay lập tức mà không cần F5.

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Đọc (Read):** Lấy dữ liệu từ bảng `[DB-notifications]`.
*   **Ghi (Write):** Cập nhật trường `read_at` thành timestamp hiện tại trong bảng `[DB-notifications]`.
