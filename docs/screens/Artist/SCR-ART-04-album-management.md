# [SCR-ART-04] Quản lý & Tạo Album (Album & EP Management)

> [!IMPORTANT]
> **I18N REQUIREMENT:** Tất cả các đoạn Text, Label, Placeholder, Message hiển thị trong tài liệu Screen Specs này khi triển khai vào code thực tế đều **KHÔNG ĐƯỢC HARDCODE**. Bắt buộc phải sử dụng key đa ngôn ngữ qua hàm `$t()` của `vue-i18n`.


> **Mô tả ngắn:** Cho phép Nghệ sĩ gộp các bài hát lẻ lại thành một Bộ sưu tập (Album hoặc EP) để phát hành ra công chúng, giúp người nghe dễ dàng theo dõi theo từng dự án âm nhạc.

## 1. Thông tin chung (Meta)
*   **Module:** Artist Workspace / Content Management
*   **Route / URL:** `/artist/albums`
*   **Layout sử dụng:** `ArtistLayout.vue`
*   **Quyền truy cập:** `[PER-ARTIST]`
*   **Component con (Children):**
    *   `ArtistAlbumTable.vue` (Danh sách Album của nghệ sĩ).
    *   `AlbumBuilderModal.vue` (Form tạo Album và kéo thả bài hát vào).

## 2. Thành phần giao diện (UI Elements)
*   **Khu vực Danh sách Album:**
    *   `Cột hiển thị`: Ảnh cover, Tên Album, Loại (Single / EP / Album), Trạng thái, Ngày phát hành.
    *   `Hành động`: Nút `Chỉnh sửa`, Nút `Xóa`.
*   **Khu vực Tạo Album Mới (Album Builder):**
    *   `Metadata`: Tên Album, Upload Ảnh bìa, Chọn Loại (Dropdown: Single, EP, Album).
    *   `Vùng chọn bài hát (Track Selector)`: Split-view hoặc Dual Listbox. Bên trái là danh sách tất cả các bài hát của nghệ sĩ đó (Không phân biệt trạng thái). Bên phải là danh sách các bài hát thuộc Album.
    *   `Sắp xếp thứ tự`: Kéo thả bài hát bên tay phải để xếp Track 1, Track 2...

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **Lấy dữ liệu (Fetch):**
    *   `[API-230]` - `GET /api/v1/artist/albums` (Danh sách Album của riêng nghệ sĩ).
    *   `[API-231]` - `GET /api/v1/artist/songs/unassigned` (Lấy danh sách các bài hát chưa thuộc album nào để nhặt vào).
*   **Ghi dữ liệu (Mutations):**
    *   `[API-232]` - `POST /api/v1/artist/albums` (Tạo Album mới kèm mảng ID bài hát).
    *   `[API-233]` - `PUT /api/v1/artist/albums/{id}` (Cập nhật).

### State Management (Pinia)
*   **Store:** `useArtistAlbumStore.ts`

## 4. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-ART-ALB-01] - Điều kiện hiển thị Public (Áp dụng [RULE-002]):** Khác với bài hát, Album **Không có luồng chờ duyệt riêng**. Trạng thái hiển thị của Album hoàn toàn phụ thuộc vào trạng thái các bài hát bên trong nó. Một Album chỉ hiện ra trên trang chủ của Khán giả khi có **Ít nhất 1 bài hát bên trong nó mang trạng thái `Approved`**.
*   **[RULE-ART-ALB-02] - Giới hạn Bài hát:** Một bài hát chỉ được phép thuộc về **DUY NHẤT 1 Album**. (Khác với Playlist, 1 bài hát có thể nằm ở n playlist). Do đó, khi tạo Album mới, danh sách bài hát bên trái chỉ hiển thị các bài chưa có `album_id`.

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Ghi (Write):** 
    *   Insert bảng `[DB-albums]`.
    *   Cập nhật trường `album_id` trong bảng `[DB-songs]` cho các bài được chọn (Bắn 1 lệnh update đồng loạt bằng toán tử IN: `UPDATE songs SET album_id = X WHERE id IN (1,2,3)`).
