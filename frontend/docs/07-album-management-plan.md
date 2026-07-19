# Kế hoạch Triển khai Frontend - Quản lý Album (Album Management)

Tài liệu này đặc tả kiến trúc UI/UX và luồng State Management cho tính năng Quản lý Album dành cho Admin, tương ứng với Phase 2 của `SCR-ADM-10-song-inventory`.

---

## 1. Cấu trúc Component Dự kiến
*   `src/views/admin/inventory/components/tabs/AlbumTab.vue`: Nâng cấp Empty State hiện tại thành bảng danh sách Album (Bao gồm Filter tìm kiếm và bảng dữ liệu có cột "Số lượng bài hát").
*   `src/components/admin/features/inventory/AlbumDrawer.vue`: Ngăn kéo (Drawer) bọc Form Tạo/Sửa Album.
*   `src/components/admin/features/inventory/AdminAlbumForm.vue`: Giao diện Form chính. Bao gồm:
    *   **Nửa trên (Thông tin cơ bản):** Upload Cover Image, Tên Album, Dropdown Artist (dùng lại Async Search của bài hát), Loại (Single/EP/Album), Ngày phát hành.
    *   **Nửa dưới (Track Selector):** Giao diện Dual Listbox (hoặc 2 cột kéo thả). Bên trái là "Nhạc lẻ chưa gán", Bên phải là "Nhạc trong Album".

---

## 2. Store Management (`albumStore.ts`)
Tạo mới store `src/stores/albumStore.ts` để quản lý độc lập với `inventoryStore`:
*   **State:** 
    *   `albums` (Danh sách Album cho bảng).
    *   `unassignedSongs` (Chứa danh sách nhạc lẻ để làm giao diện kéo thả).
*   **Actions:**
    *   `fetchAlbums()`: Gọi API `GET /api/v1/admin/albums`.
    *   `fetchUnassignedSongs(artistId)`: Gọi API `GET /api/v1/admin/songs/unassigned?artist_id=X`. Tự động clear danh sách này nếu người dùng chọn Nghệ sĩ khác.
    *   `saveAlbum(payload, isEdit)`: Gọi API `POST` hoặc `PUT` kèm theo ảnh (sử dụng `FormData`) và mảng `song_ids`.
    *   `deleteAlbum(id)`: Gọi API `DELETE` kèm confirm cảnh báo.

---

## 3. UI/UX Rules & Ràng buộc
1. **Dynamic Track Selector (Luật Chọn Nhạc):**
   - Vùng chọn nhạc (Nửa dưới form) sẽ BỊ ẨN ĐI (hoặc Disable) nếu chưa chọn Nghệ sĩ ở ô phía trên.
   - Ngay khi User chọn 1 Nghệ sĩ, Frontend tự động Trigger hành động `fetchUnassignedSongs(artistId)` và hiển thị ra các bài hát cho phép kéo thả.
   - Nếu User thay đổi Nghệ sĩ khác giữa chừng, toàn bộ danh sách nhạc đang kéo thả phải bị Reset lại từ đầu.
2. **Xử lý mảng `song_ids`:**
   - Dù hiển thị dạng danh sách kéo thả trực quan, khi submit form, Frontend phải extract danh sách các bài hát nằm ở ô "Nhạc trong Album" thành 1 mảng các số nguyên (ví dụ: `[1, 5, 8]`) để nhồi vào `FormData` dưới key `song_ids[]`.
3. **Đa ngôn ngữ (i18n):**
   - Mọi text phải được bọc bằng `$t('admin.albums...')`.
   - Update file `vi.json` để bổ sung phần dịch thuật cho Album (Single, EP, Album).
