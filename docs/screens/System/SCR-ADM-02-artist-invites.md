# [SCR-ADM-02] Quản lý Mã mời Nghệ sĩ (Artist Invitations)

> **Mô tả ngắn:** Màn hình dành riêng cho Admin để tạo mã Token độc quyền và gửi email mời các đối tác/ca sĩ tham gia nền tảng với tư cách Artist.

## 1. Thông tin chung (Meta)
*   **Module:** Administration
*   **Route / URL:** `/admin/artist-invitations`
*   **Layout sử dụng:** `AdminLayout.vue`
*   **Quyền truy cập:** `[PER-ADMIN]`
*   **Component con (Children):**
    *   `InvitationTable.vue` (Bảng thống kê các mã mời đã tạo và trạng thái của chúng)
    *   `SendInviteModal.vue` (Popup nhập Email để hệ thống gửi thư mời)

## 2. Thành phần giao diện (UI Elements)
*   **Khu vực điều khiển (Controls):**
    *   `+ Mời Nghệ Sĩ`: Nút gọi mở Popup.
    *   `Thanh tìm kiếm`: Input text để filter Email.
*   **Popup Gửi Mã Mời:**
    *   `Email đích`: Input email để gửi thư.
    *   `Gửi Lời Mời`: Nút Submit.
*   **Bảng dữ liệu (Datatable):**
    *   Cột: Email, Link mời (Nút copy), Trạng thái (Pending/Registered/Expired), Hành động.
    *   Hành động: Nút `Thu hồi` (Revoke) màu đỏ (Chỉ hiện khi trạng thái đang là Pending).

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **Lấy dữ liệu (Fetch):**
    *   `[API-340]` - `GET /api/v1/admin/invitations` (Lấy danh sách mã mời, có phân trang).
*   **Gửi dữ liệu (Mutations):**
    *   `[API-341]` - `POST /api/v1/admin/invitations` (Tạo mã mời mới và gửi Email).
    *   Payload: `email` (Bắt buộc).

### State Management (Pinia)
*   **Store:** `useAdminInvitationStore.js`
*   **Actions:** 
    *   `fetchInvitations()`
    *   `generateAndSendInvite(email)`

## 4. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-ADMIN-INV-01] - Chống Spam:** Không được gửi mã mời cho một Email đang có mã mời ở trạng thái `Pending` (chưa hết hạn và chưa xài). Trả lỗi `[ERR-ADMIN-422]` nếu vi phạm.
*   **[RULE-ADMIN-INV-02] - Giới hạn thời gian (TTL):** Mã mời được sinh ra mặc định chỉ có hiệu lực trong vòng **7 ngày** (Lưu vào cột `expires_at`).
*   **[RULE-ADMIN-INV-03] - Trạng thái mã mời:** Bảng dữ liệu UI (Datatable) sẽ dựa vào DB để hiển thị 3 trạng thái:
    *   🟡 `Pending`: Cột `used_at` = null VÀ chưa qua `expires_at`.
    *   🟢 `Registered`: Cột `used_at` != null (Đã tạo tài khoản thành công).
    *   🔴 `Expired`: Cột `used_at` = null VÀ đã qua `expires_at`.
*   **[RULE-ADMIN-INV-04] - Hủy ngang (Revoke):** Admin có quyền ấn nút "Thu hồi" đối với các mã `Pending`. Khi đó Backend cập nhật `expires_at` = `now()` để chặn ngay lập tức.

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Đọc (Read):** Bảng `[DB-artist_invitations]`.
*   **Ghi (Write):** 
    *   Thêm mới bản ghi vào `[DB-artist_invitations]` với chuỗi `token` được tạo random bằng `Str::random(64)`.
    *   *Side Effect:* Backend sẽ Dispatch một Queue Job `[JOB-SEND-ARTIST-INVITE]` để máy chủ đẩy Email chứa nội dung mẫu: *"Chào mừng... đây là link đăng ký độc quyền của bạn: domain.com/artist/register?token=XYZ"*.
