# [SCR-ADM-10] Quản lý Kho Nhạc & Album (Audio Inventory & Albums)

> **Mô tả ngắn:** Phân hệ lưu trữ và quản lý toàn bộ bài hát, album trên hệ thống. Khác với màn hình Kiểm duyệt (`SCR-ADM-04`) chỉ xem các bài chờ duyệt, màn hình này cho phép Admin xem toàn bộ kho nhạc, cung cấp tính năng **Upload Nhạc & Tạo Album trực tiếp** (thường dùng cho nhạc mua bản quyền từ Hãng đĩa) mà không cần thông qua tài khoản Artist.

## 1. Thông tin chung (Meta)
*   **Module:** System / Content Management
*   **Route / URL:** `/admin/inventory`
*   **Layout sử dụng:** `AdminLayout.vue`
*   **Quyền truy cập:** `[PER-CONTENT-MANAGER]` hoặc `[PER-ADMIN]`
*   **Component con (Children):**
    *   `InventoryTabs.vue` (Tabs chuyển đổi giữa Quản lý Bài hát và Quản lý Album).
    *   `SongTable.vue` (Bảng danh sách toàn bộ bài hát).
    *   `AlbumTable.vue` (Bảng danh sách toàn bộ Album).
    *   `AdminUploadForm.vue` (Component Form tải nhạc/tạo Album dùng chung. Component này cũng được tái sử dụng khi gọi từ màn hình Chi tiết Nghệ sĩ `[SCR-ADM-03B]`).

## 2. Thành phần giao diện (UI Elements)
*   **Giao diện Tabs:** Chia màn hình làm 2 Tab chính: "Bài Hát" và "Album".
*   **Tab Bài Hát (Song Management):**
    *   `Bộ lọc`: Tìm theo tên, Trạng thái (Approved, Pending, Hidden), Thể loại, Nghệ sĩ.
    *   `Cột hiển thị`: Ảnh bìa, Tên bài hát, Nghệ sĩ biểu diễn, Người tải lên (Admin/Artist), Lượt Stream.
    *   `Hành động`: Nút `Chỉnh sửa Metadata`, Nút `Ẩn/Khóa bài hát (Hide)`.
    *   `Khu vực Upload Nhạc`: Nút "+ Tải nhạc lên", kéo thả file (`.mp3`, `.wav`), Dropdown "Gán Nghệ Sĩ", nhập Metadata. 
        *   *(Lưu ý UX: Nếu mở Component này từ màn hình `[SCR-ADM-03B]`, ô Dropdown Nghệ Sĩ sẽ tự động khóa cứng `Read-only` và điền sẵn tên Nghệ sĩ để chống gán nhầm).*
*   **Tab Album (Album Management):**
    *   `Bộ lọc`: Tìm theo tên Album, Nghệ sĩ, Trạng thái.
    *   `Cột hiển thị`: Ảnh cover, Tên Album, Loại (Single, EP, Album), Nghệ sĩ, Số bài hát, Ngày phát hành.
    *   `Hành động`: Nút `Chỉnh sửa` (Đổi tên, thêm/bớt bài hát), Nút `Ẩn Album`.
    *   `Khu vực Tạo Album`: Nút "+ Tạo Album mới", nhập thông tin và Dropdown chọn Nghệ sĩ gán vào (áp dụng luật Read-only tương tự Upload Nhạc). Có danh sách kéo thả để thêm bài hát vào Album.

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **API cho Bài Hát:**
    *   `[API-360]` - `GET /api/v1/admin/inventory/songs` (Danh sách tổng).
    *   `[API-ADM-28]` - `POST /api/v1/admin/inventory/songs` (Upload nhạc).
    *   `[API-ADM-29]` - `PUT /api/v1/admin/inventory/songs/{id}/metadata` (Sửa thông tin).
    *   `[API-363]` - `DELETE /api/v1/admin/inventory/songs/{id}` (Soft Delete / Ẩn).
*   **API cho Album:**
    *   `[API-ADM-31]` - `GET /api/v1/admin/inventory/albums` (Danh sách Album).
    *   `[API-ADM-32]` - `POST /api/v1/admin/inventory/albums` (Tạo Album).
    *   `[API-ADM-33]` - `PUT /api/v1/admin/inventory/albums/{id}` (Sửa Album & cập nhật bài hát).
*   **API Tiện ích:**
    *   `[API-ADM-27]` - `GET /api/v1/admin/search/artists?q={text}` (API phục vụ ô Autocomplete tìm Nghệ sĩ).

### State Management (Pinia)
*   **Store:** `useInventoryStore.ts`
*   **Actions:** `fetchSongs()`, `uploadSongByAdmin(payload)`.

## 4. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-ADM-INV-01] - Auto-Approved Logic:** Các bài hát do Admin (hoặc Content Manager) upload thông qua màn hình này sẽ **bỏ qua khâu Kiểm duyệt**. Ngay khi quá trình FFmpeg Transcoding hoàn tất, trạng thái của bài hát sẽ tự động chuyển thành `Approved` (Thay vì `Pending` như lúc Artist tự up).
*   **[RULE-ADM-INV-02] - Tracking Nguồn gốc (Uploader Tracking):** Trong CSDL, ngoài trường `artist_id` (Nghệ sĩ biểu diễn), cần thiết kế thêm cơ chế phân biệt ai là người tải file gốc lên. Nếu Admin tải lên, audit log phải ghi rõ là Admin upload thay cho Artist để phục vụ đối soát tiền bản quyền (Royalties) sau này với các Hãng đĩa.
*   **[RULE-ADM-INV-03] - Quản trị Tuyệt đối (Absolute Control):** Admin có quyền `Sửa Metadata` và `Ẩn (Hide)` bất kỳ bài hát nào trên hệ thống, kể cả những bài do Artist tự tải lên. Khi Admin ẩn bài hát, bài hát sẽ biến mất khỏi Home Explore của Listener, và bên Artist Dashboard sẽ hiển thị thông báo "Bài hát đã bị Admin ẩn vì vi phạm điều khoản".

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Đọc (Read):** Bảng `[DB-songs]`, `[DB-albums]`, `[DB-users]`, `[DB-genres]`.
*   **Ghi (Write):** 
    *   Insert vào bảng `[DB-songs]` với `status = Approved`.
    *   Upload file gốc lên Storage (MinIO). Kích hoạt Queue Job (FFmpeg) biến audio thành định dạng HLS Streaming.
    *   *Side Effect:* Xóa Cache Redis `explore_new_releases` tương tự như khâu duyệt bài.
