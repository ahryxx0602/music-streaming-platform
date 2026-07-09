# [SCR-ART-03] Quản lý & Tải nhạc lên (Song Management & Upload)

> **Mô tả ngắn:** Khu vực dành riêng cho Nghệ sĩ quản lý danh sách bài hát cá nhân, tải lên các tệp âm thanh, điền thông tin siêu dữ liệu (Metadata) và nộp bài hát chờ kiểm duyệt. 

## 1. Thông tin chung (Meta)
*   **Module:** Artist Workspace / Content Management
*   **Route / URL:** `/artist/songs`
*   **Layout sử dụng:** `ArtistLayout.vue`
*   **Quyền truy cập:** `[PER-ARTIST]`
*   **Component con (Children):**
    *   `ArtistSongTable.vue` (Bảng danh sách bài hát với các badge trạng thái).
    *   `FileUploadZone.vue` (Khu vực kéo thả file Audio và Ảnh bìa).
    *   `MetadataForm.vue` (Form điền tên bài, thể loại, lời bài hát).
    *   `UploadProgress.vue` (Thanh bar hiển thị % upload và trạng thái xử lý).

## 2. Thành phần giao diện (UI Elements)
*   **Khu vực Danh sách Bài Hát (Data Table):**
    *   `Cột hiển thị`: Ảnh cover, Tên bài hát, Thể loại, Lượt nghe (Stream), Trạng thái (Pending, Approved, Rejected, Hidden).
    *   `Badge Trạng thái`: Vàng (Pending), Xanh (Approved), Đỏ (Rejected). Nếu Rejected, có icon để bấm xem Lý do.
    *   `Hành động`: Nút `+ Tải bài hát mới` (Góc trên), Nút `Chỉnh sửa` (Cho bài Pending/Rejected).
*   **Khu vực Upload / Chỉnh sửa File (Modal/Page):**
    *   `Vùng kéo thả File Nhạc`: Chấp nhận định dạng `.mp3`, `.wav`, `.flac`. Kích thước tối đa: 100MB.
    *   `Vùng chọn Ảnh bìa (Cover)`: Chấp nhận `.jpg`, `.png`, tỷ lệ 1:1, tối đa 5MB.
*   **Các ô nhập liệu Metadata (Inputs):**
    *   `Tên bài hát (Title)`: Input text. Bắt buộc.
    *   `Thể loại (Genre)`: Dropdown Select. Phải là Cây danh mục (Tree/Cascader Select) chọn các giá trị có trong `[DB-genres]`.
    *   `Lời bài hát (Lyrics)`: Textarea. Không bắt buộc.
*   **Các nút bấm (Buttons & Links):**
    *   `Bắt đầu Tải lên`: Nút Submit form, gọi `[API-240]`. Nút bị disable nếu chưa chọn File nhạc.
    *   `Lưu nháp (Save Draft)`: Lưu file nhưng chưa gửi đi duyệt.
    *   `Hủy bỏ`: Reset form.

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
Luồng Upload được thiết kế theo mô hình Bất đồng bộ (Async Transcoding):
1.  **Lấy Dữ liệu (Fetch):**
    *   `[API-ART-00]` - `GET /api/v1/genres/tree` (Trả về cây thư mục Thể loại để render Dropdown).
    *   `[API-220]` - `GET /api/v1/artist/songs` (Lấy danh sách bài hát CỦA RIÊNG nghệ sĩ đó).
2.  **Submit File & Data (Mutations):**
    *   `[API-221]` - `POST /api/v1/artist/songs` (Upload mới. Payload: Multipart/Form-data chứa audio, cover, metadata).
    *   `[API-222]` - `PUT /api/v1/artist/songs/{id}` (Chỉnh sửa Metadata và nộp lại).

### State Management (Pinia)
*   **Store:** `useStudioStore.ts`
*   **State:** `uploadProgress` (0 - 100%), `uploadStatus` ('idle', 'uploading', 'transcoding', 'completed', 'error').

## 4. Quy tắc nghiệp vụ (Business Rules)

*   **[RULE-UP-01] - Giới hạn Upload Frontend:** Phải Validate File ở trình duyệt trước khi cho nộp. Báo lỗi `[ERR-UI]` ngay lập tức nếu file nhạc > 100MB hoặc sai định dạng. Tránh việc tải lên 5 phút mới biết lỗi.
*   **[RULE-UP-02] - Xử lý Bất đồng bộ (Async Transcoding):**
    *   Việc tải lên 1 file WAV 50MB và băm thành chuẩn HLS tốn thời gian rất lâu. Backend API `[API-240]` KHÔNG ĐƯỢC chờ FFmpeg chạy xong mới trả Response.
    *   **Luồng xử lý:** Backend nhận file đẩy tạm vào MinIO ➔ Tạo bản ghi `[DB-songs]` trạng thái `Processing` ➔ Đẩy lệnh `[JOB-TRANSCODE-AUDIO]` vào Queue ➔ Trả ngay HTTP 201 cho Frontend.
    *   Frontend lập tức hiển thị trạng thái "Đang xử lý nền" (Transcoding).
*   **[RULE-UP-03] - Trạng thái mặc định:** Bài hát sau khi Upload và Transcode thành công, tự động chuyển sang trạng thái `Pending Review` (Chờ Admin duyệt). Public User chưa thể thấy bài hát này.
*   **[RULE-UP-04] - Cây danh mục (Tree Dropdown):** Nơi chọn Thể loại (Genre) phải hiển thị phân cấp (Ví dụ: Cha ➔ Con), hỗ trợ tìm kiếm text. Nếu Thể loại cha bị ẩn (`is_active = 0`), toàn bộ các con bên dưới cũng không được hiển thị cho Nghệ sĩ chọn.
*   **[RULE-UP-05] - Chỉnh sửa và Nộp lại (Resubmit - [RULE-006]):**
    *   Nghệ sĩ **không được** sửa Metadata của bài hát đã `Approved` (Vì đã phát hành ra public, muốn sửa phải xin phép Admin).
    *   Nếu bài hát bị `Rejected`, sau khi Nghệ sĩ sửa xong và bấm Nộp, trạng thái nhảy về lại `Pending` để Admin duyệt lại.

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Đọc (Read):** Lấy dữ liệu `[DB-genres]` đệ quy.
*   **Ghi (Write):** 
    *   Insert bản ghi mới vào bảng `[DB-songs]` với `artist_id` là ID của người đang đăng nhập.
    *   Cột `status` khởi tạo là `Draft` (nếu đang upload) hoặc `Processing` (nếu bắt đầu Transcode).
    *   *Side Effect:* Đẩy tác vụ băm HLS vào Laravel Queue/Horizon.
