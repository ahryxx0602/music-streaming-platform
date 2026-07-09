# [SCR-LST-03] Chi tiết Bộ sưu tập (Album / Playlist)

> **Mô tả ngắn:** Màn hình dùng chung để hiển thị thông tin chi tiết và danh sách bài hát của một Album (của Nghệ sĩ) hoặc một Playlist (do User tạo). Áp dụng thiết kế DRY (Don't Repeat Yourself) để tái sử dụng Component.

## 1. Thông tin chung (Meta)
*   **Module:** Listener / Streaming
*   **Route / URL:** `/album/{id}` hoặc `/playlist/{id}`
*   **Layout sử dụng:** `MainLayout.vue`
*   **Quyền truy cập:** `[PER-PUBLIC]` (Đối với Album và Playlist Public), `[PER-AUTH]` (Đối với Playlist Private).
*   **Component con (Children):**
    *   `CollectionHeader.vue` (Hiển thị Ảnh bìa lớn, Tiêu đề, Tác giả, Nút Play All, Nút thả tim).
    *   `TrackList.vue` (Danh sách bài hát với Infinite Scroll).
    *   `TrackItem.vue` (Từng dòng bài hát, hiển thị thời lượng, trạng thái tim).

## 2. Thành phần giao diện (UI Elements)
*   **Khu vực Thông tin (Hero):**
    *   `Ảnh bìa`: Hình ảnh lớn đại diện.
    *   `Nút Phát tất cả (Play All)`: Nút tròn xanh lá cây nổi bật.
    *   `Nút Yêu thích (Heart)`: Thêm Collection này vào Thư viện cá nhân.
*   **Khu vực Danh sách bài hát:**
    *   Danh sách cuộn vô tận (Infinite Scroll). 
    *   Mỗi dòng có: Số thứ tự, Tên bài, Nghệ sĩ (nếu là Playlist nhiều người), Nút Heart, Thời lượng, Menu (...) để thêm vào queue/playlist khác.

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **Lấy dữ liệu (Fetch):**
    *   `[API-LST-05]` - `GET /api/v1/collections/{type}/{id}?cursor={cursor}` (Lấy thông tin chung + Mảng `tracks` phân trang bằng Cursor).
*   **Gửi dữ liệu (Mutations):**
    *   `[API-124]` - `POST /api/v1/library/toggle-favorite` (Like/Unlike toàn bộ Album/Playlist).

### State Management (Pinia)
*   **Store:** `useCollectionStore.ts`
*   **Actions:** 
    *   `fetchCollectionDetails()`
    *   `loadMoreTracks()` (Gọi khi cuộn xuống đáy màn hình).

## 4. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-LST-02] - Cơ chế "Phát tất cả" (Play All):** Khi User ấn nút Play to ở Header, Frontend KHÔNG gọi API rời rạc. Lập tức truyền mảng `tracks` hiện tại vào `playerStore.setQueue(tracks)` và kích hoạt hàm phát bài hát có `index = 0`.
*   **[RULE-LST-03] - Lazy Loading (Pagination):** Một Playlist có thể chứa tối đa 1000 bài hát. `[API-LST-05]` sử dụng **Cursor Pagination**, mỗi lần trả về 50 bài hát. Khi scroll xuống, Frontend chèn tiếp data vào mảng `tracks`.
*   **[RULE-LST-04] - Trạng thái Yêu thích (Heart State):** 
    *   Payload API `[API-LST-05]` từ Backend trả về mảng Tracks phải đính kèm sẵn cờ `is_liked_by_user` (Xử lý tối ưu gom nhóm N+1 Query trên Server).
    *   Frontend dựa vào cờ này để render Icon Heart Đỏ/Xám ngay tức thì.

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Đọc (Read):** 
    *   Join bảng `[DB-albums]` / `[DB-playlists]` với `[DB-songs]`.
    *   Join với bảng `[DB-favorite_songs]` theo `user_id` hiện tại để tính toán cột ảo `is_liked_by_user`.
*   **Ghi (Write):** Ghi vào `[DB-favorite_albums]` hoặc `[DB-favorite_playlists]` khi ấn nút thả tim.
