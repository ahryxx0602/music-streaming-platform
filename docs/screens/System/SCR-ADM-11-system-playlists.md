# [SCR-ADM-11] Quản lý Playlist Hệ thống (System / Curated Playlists)

> **Mô tả ngắn:** Phân hệ dành cho Biên tập viên Nội dung (Curators / Admins) tạo ra các Playlist chính thức của nền tảng (Ví dụ: "Top 50 V-Pop", "Nhạc Xuân 2026", "Lofi Cực Chill"). Đây là nguồn nội dung cực kỳ quan trọng để hiển thị ở Trang chủ hoặc chèn vào Banner quảng cáo.

## 1. Thông tin chung (Meta)
*   **Module:** System / Content Management
*   **Route / URL:** `/admin/playlists`
*   **Layout sử dụng:** `AdminLayout.vue`
*   **Quyền truy cập:** `[PER-CONTENT-MANAGER]` hoặc `[PER-ADMIN]`
*   **Component con (Children):**
    *   `SystemPlaylistTable.vue` (Bảng danh sách Playlist của Admin).
    *   `PlaylistBuilderModal.vue` (Màn hình chi tiết để kéo thả, nhặt bài hát vào Playlist).

## 2. Thành phần giao diện (UI Elements)
*   **Khu vực Danh sách Playlist:**
    *   `Cột hiển thị`: Ảnh cover, Tên Playlist, Mô tả ngắn, Số lượng bài hát, Lượt Favorite (Like), Trạng thái (Public/Private).
    *   `Hành động`: Nút `Sửa nội dung (Builder)`, Nút `Sao chép Link`.
*   **Khu vực Biên tập Playlist (Playlist Builder):**
    *   **Split View (Chia đôi màn hình):**
        *   **Bên trái (Kho nhạc):** Ô tìm kiếm bài hát theo tên hoặc theo Nghệ sĩ. Hiển thị danh sách kết quả (Có nút Play nhỏ để Admin nghe thử trước khi nhặt).
        *   **Bên phải (Nội dung Playlist):** Danh sách các bài hát đã thêm vào Playlist. Hỗ trợ biểu tượng icon Kéo Thả (Drag & Drop) để xếp hạng thứ tự bài hát (Track 1, Track 2...).
    *   `Metadata Playlist`: Form bên trên cùng để đổi Tên, Mô tả, và Upload Ảnh bìa cực nét.

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **Fetch & Search:**
    *   `[API-ADM-34]` - `GET /api/v1/admin/playlists` (Danh sách Playlist của hệ thống).
    *   `[API-ADM-35]` - `GET /api/v1/admin/search/songs?q={text}` (Tìm kiếm bài hát để thêm vào Playlist).
*   **Mutations:**
    *   `[API-ADM-36]` - `POST /api/v1/admin/playlists` (Tạo Playlist rỗng).
    *   `[API-ADM-37]` - `PUT /api/v1/admin/playlists/{id}` (Lưu toàn bộ: cập nhật tên, ảnh, và **mảng ID bài hát** theo đúng thứ tự).
    *   `[API-ADM-38]` - `DELETE /api/v1/admin/playlists/{id}` (Soft Delete).

### State Management (Pinia)
*   **Store:** `useSystemPlaylistStore.ts`

## 4. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-ADM-PL-01] - Quyền sở hữu (Ownership):** Các Playlist tạo ra từ màn hình này sẽ được lưu vào bảng `[DB-playlists]` với `user_id` là ID của tài khoản Admin đang thao tác (Hoặc gán cho một tài khoản ảo System Account). Ở phía Client (User App), các Playlist này sẽ được gắn badge "Official" hoặc logo của nền tảng thay vì tên user bình thường.
*   **[RULE-ADM-PL-02] - Đồng bộ Banner:** Admin thường tạo Playlist ở đây xong, sau đó ra màn hình `[SCR-ADM-05]` (Banner) và chèn link (Ví dụ: `/playlist/99`).
*   **[RULE-ADM-PL-03] - Trạng thái Bài hát:** Khi tìm kiếm bài hát để đưa vào Playlist, `[API-ADM-35]` chỉ được phép trả về các bài hát đang ở trạng thái `Approved`. Các bài bị `Hidden` hoặc `Pending` tuyệt đối không được cho phép đưa vào Playlist Public.

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Đọc (Read):** Bảng `[DB-songs]`.
*   **Ghi (Write):** 
    *   Bảng `[DB-playlists]`.
    *   Bảng trung gian `[DB-playlist_songs]` (Cập nhật cột `position` dựa theo mảng ID lúc kéo thả).
