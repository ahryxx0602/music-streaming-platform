# Tài liệu Đặc tả API & Kiến trúc (API Docs) - Quản lý Playlist Hệ thống (Admin)

Tài liệu này định nghĩa các quy tắc nghiệp vụ và API cho việc Quản lý Playlist Hệ thống (System Playlists) dành cho Admin (`SCR-ADM-11`).

---

## 1. Quy tắc Nghiệp vụ (Business Rules)
1. **[RULE-ADM-PL-01] Quyền sở hữu (Ownership):** Playlists tạo ra từ Admin panel mang cờ `type = 'system'`. `user_id` gán cho Admin hiện tại (`Auth::id()`).
2. **[RULE-ADM-PL-02] Trạng thái Bài hát:** Khi tìm kiếm bài hát để cho vào Playlist, chỉ được phép sử dụng API có filter `status=Approved`. Tuyệt đối không cho phép add nhạc `Pending` hoặc `Rejected` vào Playlist.
3. **[RULE-ADM-PL-03] Thứ tự Bài hát (Ordering):** Frontend sẽ gửi mảng `song_ids[]` theo đúng thứ tự. Backend sẽ xóa hết data cũ trong `playlist_songs` và insert lại mảng này kèm cột `position` (từ 0 -> n).

---

## 2. Đặc tả API (AdminPlaylistController)

Khởi tạo Controller mới: `Modules\Playlist\Http\Controllers\AdminPlaylistController`

### 2.1. [GET] /api/v1/admin/playlists
*   **Mô tả:** Lấy danh sách Playlist của hệ thống.
*   **Tham số:** `page`, `filter[search]`.
*   **Logic:**
    *   Query: `Playlist::where('type', 'system')`
    *   Load kèm `withCount('songs')` để hiển thị số lượng nhạc bên trong.
    *   Phân trang (paginate 15).

### 2.2. [POST] /api/v1/admin/playlists
*   **Mô tả:** Tạo Playlist hệ thống mới.
*   **Request Body:** `title`, `description`, `privacy` ('Public'|'Private'), `song_ids` (mảng ID bài hát).
*   **Logic:**
    *   Tạo `Playlist`: `title`, `description`, `privacy`, `type = 'system'`, `user_id = Auth::id()`.
    *   Nếu có `song_ids`, duyệt mảng và tạo mới records trong bảng `playlist_songs` với `position` tăng dần.

### 2.3. [PUT] /api/v1/admin/playlists/{id}
*   **Mô tả:** Cập nhật Playlist & Xếp lại thứ tự bài hát.
*   **Request Body:** `title`, `description`, `privacy`, `song_ids` (mảng, bắt buộc truyền đủ tất cả ID bài hát có trong playlist theo thứ tự mới).
*   **Logic:**
    *   Tìm `Playlist` theo ID và thuộc về hệ thống (`type = 'system'`).
    *   Update thông tin cơ bản.
    *   Nếu có mảng `song_ids`:
        *   Xóa sạch records cũ: `DB::table('playlist_songs')->where('playlist_id', $id)->delete()`.
        *   Insert lại danh sách mới kèm `position`.

### 2.4. [DELETE] /api/v1/admin/playlists/{id}
*   **Mô tả:** Soft delete Playlist.
*   **Logic:** `Playlist::findOrFail($id)->delete()`.

---

## 3. Cập nhật Routes (`api.php`)
Trong nhóm routes `admin`, thêm:
```php
Route::apiResource('playlists', \Modules\Playlist\Http\Controllers\AdminPlaylistController::class)->names('admin.playlists');
```
*(Ghi chú: Để gọi tìm kiếm bài hát, Frontend hãy tận dụng lại route `GET /api/v1/admin/songs?status=Approved&search={text}` của `AdminSongController`)*
