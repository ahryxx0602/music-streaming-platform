# Kế hoạch Triển khai Frontend - Quản lý Playlist Hệ thống (System Playlists)

Tài liệu này đặc tả UI/UX và logic quản lý State cho Màn hình Quản lý Playlist Hệ thống của Admin (`SCR-ADM-11`).

---

## 1. Cấu trúc Component Dự kiến
Tạo folder mới: `src/views/admin/playlists/`
*   `src/views/admin/playlists/PlaylistView.vue`: Màn hình chứa Bảng danh sách Playlist.
*   `src/components/admin/features/playlists/PlaylistTable.vue`: Hiển thị Tên Playlist, Số bài hát, Trạng thái.
*   `src/components/admin/features/playlists/PlaylistBuilder.vue`: Giao diện đặc biệt (Drawer hoặc Split View Modal) để kéo thả nhạc.
    *   *Nửa trái:* Ô Search bài hát. Gọi API lấy list bài hát. Hiển thị kết quả. Có nút (+) để add vào list bên phải.
    *   *Nửa phải:* Form thông tin cơ bản (Tên, Mô tả) và **Danh sách bài hát đã chọn**. Cho phép xóa nhạc (-) hoặc kéo thả để đổi thứ tự (Dùng thư viện `vuedraggable` hoặc tự handle array array).

---

## 2. Store Management (`playlistStore.ts`)
Tạo store `src/stores/playlistStore.ts`:
*   **State:** 
    *   `playlists`: Mảng chứa các Playlist của hệ thống.
    *   `availableSongs`: Mảng chứa kết quả search bài hát từ kho.
*   **Actions:**
    *   `fetchPlaylists()`: Gọi `GET /api/v1/admin/playlists`.
    *   `searchSongs(query)`: Gọi `GET /api/v1/admin/songs?status=Approved&search={query}`.
    *   `savePlaylist(data)`: Gửi Request `POST` hoặc `PUT` tới `/api/v1/admin/playlists` kèm Form Data và mảng `song_ids[]`.

---

## 3. UI/UX Rules & Ràng buộc
1. **[RULE-ADM-PL-UI-01] Tìm kiếm Nhạc:** Ô search bài hát cần có debounce (khoảng 300-500ms) để không spam API khi gõ. Bắt buộc phải gắn param `status=Approved` khi gọi API.
2. **[RULE-ADM-PL-UI-02] Kéo thả (Drag & Drop):** Ở danh sách bên phải (Các nhạc đã thêm), cần có icon Drag để người dùng hiểu họ có thể kéo thả để đổi thứ tự track (1, 2, 3...). Khi bấm Lưu, Frontend sẽ xuất mảng `song_ids` theo đúng thứ tự mảng hiện tại.
3. **[RULE-ADM-PL-UI-03] Validation:** Tên Playlist là bắt buộc. Phải chọn ít nhất 1 bài hát thì mới cho phép tạo/cập nhật.

---

## 4. Routing
Cần bổ sung Route `/admin/playlists` vào Router (`router/index.ts`) và gắn Link trên thanh Sidebar Menu.
