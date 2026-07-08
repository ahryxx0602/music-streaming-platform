# [SCR-ADM-04] Kiểm duyệt Bài hát (Song Content Moderation)

> **Mô tả ngắn:** Màn hình quan trọng nhất dành cho nhân viên Content Moderator. Liệt kê và cung cấp công cụ để nghe thử, kiểm tra thông tin các bài hát đang ở trạng thái `Pending` do Artist tải lên, từ đó đưa ra quyết định Duyệt (Approve) hoặc Từ chối (Reject).

## 1. Thông tin chung (Meta)
*   **Module:** System / Moderation
*   **Route / URL:** `/admin/moderation/songs`
*   **Layout sử dụng:** `AdminLayout.vue`
*   **Quyền truy cập:** `[PER-MODERATOR]` (Các tài khoản Staff được phân quyền duyệt nội dung).
*   **Component con (Children):**
    *   `ModerationTable.vue` (Bảng danh sách bài hát Pending).
    *   `PreviewModal.vue` (Popup chi tiết để nghe thử và xem Lyrics/Metadata).
    *   `RejectReasonModal.vue` (Popup nhập lý do nếu chọn Reject).

## 2. Thành phần giao diện (UI Elements)
*   **Khu vực Danh sách (Data Table):**
    *   `Cột hiển thị`: Ảnh cover (nhỏ), Tên bài hát, Nghệ sĩ đăng tải, Ngày nộp, Trạng thái.
    *   `Nút Review`: Mở Popup kiểm duyệt chi tiết.
*   **Khu vực Kiểm duyệt chi tiết (Review Modal):**
    *   `Mini Audio Player`: Tích hợp Hls.js để nghe thử luồng `.m3u8` vừa được Server cắt xong.
    *   `Khối Metadata`: Hiển thị Tên bài, Thể loại (Genre), Ảnh cover phóng to.
    *   `Khối Lyrics`: Textbox chỉ đọc chứa lời bài hát để Moderator check ngôn từ vi phạm.
*   **Khu vực Quyết định (Action Buttons):**
    *   `Nút Approve (Xanh)`: Duyệt bài.
    *   `Nút Reject (Đỏ)`: Mở popup nhập lý do từ chối.

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **Lấy dữ liệu (Fetch):**
    *   `[API-350]` - `GET /api/v1/admin/moderation/songs?status=pending&page=1` (Lấy danh sách chờ duyệt, có phân trang).
    *   `[API-ADM-08]` - `GET /api/v1/admin/moderation/songs/{id}` (Lấy chi tiết Metadata để review).
*   **Ghi dữ liệu (Mutations):**
    *   `[API-352]` - `PUT /api/v1/admin/moderation/songs/{id}/approve` (Duyệt).
    *   `[API-353]` - `PUT /api/v1/admin/moderation/songs/{id}/reject` (Từ chối).
    *   Payload của Reject: `{ "reject_reason": "Văn bản lý do" }`

### State Management (Pinia)
*   **Store:** `useModerationStore.js`
*   **Actions:** `fetchPendingSongs()`, `approveSong(id)`, `rejectSong(id, reason)`. Khi duyệt/từ chối xong, tự động gỡ dòng đó khỏi Table UI.

## 4. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-ADM-MOD-01] - Điều kiện hiển thị:** Chỉ những bài hát đã đi qua quá trình Async Transcoding (Xử lý FFmpeg thành công) và mang trạng thái `Pending Review` mới được hiện lên bảng này. Các bài đang `Processing` hoặc `Draft` sẽ bị ẩn.
*   **[RULE-ADM-MOD-02] - Logic Approve (Duyệt):** Khi bấm Approve, Backend cập nhật `status = 'approved'`. Kể từ giây phút này, bài hát chính thức xuất hiện trên App của Listener (Public). Kích hoạt **[RULE-001]**.
*   **[RULE-ADM-MOD-03] - Logic Reject (Từ chối):** Khi bấm Reject, Admin bắt buộc phải điền `reject_reason`. Backend cập nhật `status = 'rejected'`, lưu lý do vào CSDL. 
*   **[RULE-ADM-MOD-04] - Thông báo (Notification):** Bất kể Approve hay Reject, Backend đều phải trigger một Event gửi Email/In-app Notification về cho Artist để họ biết kết quả (Kích hoạt **[RULE-006]**).

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Đọc (Read):** Bảng `[DB-songs]` kết hợp `[DB-users]` (Lấy thông tin Artist).
*   **Ghi (Write):** 
    *   Cập nhật cột `status` và `reject_reason` (nếu có) trong bảng `[DB-songs]`.
    *   Insert vào bảng `[DB-notifications]` để báo cho Artist.
    *   *(Hệ quả)*: Xóa cache danh sách `New Releases` trong Redis để Home Explore cập nhật nhạc mới.
