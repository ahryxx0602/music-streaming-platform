# Kế hoạch Triển khai Frontend - Kiểm duyệt Bài hát (Song Moderation)

Tài liệu này đặc tả kiến trúc UI/UX và luồng State Management cho tính năng Kiểm duyệt Bài hát dành cho Admin (`SCR-ADM-04`).

---

## 1. Cấu trúc Component Dự kiến
Tạo folder mới: `src/views/admin/moderation/`
*   `src/views/admin/moderation/ModerationView.vue`: Màn hình chính chứa Bảng danh sách chờ duyệt.
*   `src/components/admin/features/moderation/ModerationTable.vue`: Bảng danh sách các bài hát Pending.
*   `src/components/admin/features/moderation/PreviewModal.vue`: Popup "Nghe thử & Kiểm tra Metadata".
    *   Tích hợp thẻ `<audio>` để phát `hls_path` (Sử dụng thư viện Hls.js nếu cần thiết, hoặc thẻ audio HTML5 thường vì file gốc HLS có thể chơi qua HLS.js). *Lưu ý: Nếu chưa tích hợp HLS.js ở Frontend, có thể tạm dùng link gốc `original_file_path` để nghe bằng trình duyệt HTML5 `<audio>` cho nhanh ở khâu Admin.*
*   `src/components/admin/features/moderation/RejectReasonModal.vue`: Popup nhập văn bản lý do từ chối.

---

## 2. Store Management (`moderationStore.ts`)
Tạo store `src/stores/moderationStore.ts`:
*   **State:** 
    *   `pendingSongs`: Danh sách bài hát chờ duyệt.
    *   `selectedSong`: Bài hát đang được mở trong Popup.
*   **Actions:**
    *   `fetchPendingSongs()`: Gọi `GET /api/v1/admin/moderation/songs`.
    *   `approveSong(id)`: Gọi `PUT /api/v1/admin/moderation/songs/{id}/approve`. Sau khi thành công, lọc bỏ bài hát đó khỏi mảng `pendingSongs`.
    *   `rejectSong(id, reason)`: Gọi `PUT /api/v1/admin/moderation/songs/{id}/reject`. Lọc bỏ khỏi mảng sau khi thành công.

---

## 3. UI/UX Rules & Ràng buộc
1. **Flow Kiểm duyệt:** 
   - Trên bảng danh sách có cột "Thao tác" với nút "Kiểm duyệt".
   - Bấm vào nút này -> Mở `PreviewModal`. 
   - Trên `PreviewModal` sẽ hiển thị đầy đủ Metadata (Ảnh to, Tên bài, Thể loại, Lời bài hát) và một Mini Audio Player.
   - Ở dưới cùng Popup có 2 nút to: `Approve` (Xanh) và `Reject` (Đỏ).
2. **Logic Reject:**
   - Nếu bấm Reject -> Đóng `PreviewModal` và mở đè `RejectReasonModal`.
   - `RejectReasonModal` yêu cầu nhập Textarea lý do (Bắt buộc). Nút Confirm chỉ sáng lên khi textarea không rỗng.
3. **Menu Sidebar:**
   - Cần bổ sung Route `/admin/moderation/songs` vào Router (`router/index.ts`).
   - Thêm tab "Kiểm duyệt Bài hát" vào Sidebar UI của Admin nếu chưa có.
