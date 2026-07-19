# Kế hoạch Triển khai (Implementation Plan) - Kho Nhạc & S3 Upload (Inventory)

Tài liệu này đặc tả kiến trúc và các bước triển khai luồng Quản lý Kho Nhạc (`SCR-ADM-10-song-inventory`), đặc biệt tập trung vào giải pháp **AWS S3 Pre-signed URL** để upload nhạc tối ưu.

---

## 1. Mục tiêu (Objectives)
*   Phát triển giao diện Quản lý Bài hát & Album dành cho Admin.
*   Tránh tắc nghẽn băng thông server Laravel bằng cách cho phép Client (Trình duyệt) upload trực tiếp file âm thanh (lớn) lên AWS S3 (hoặc MinIO) thông qua Pre-signed URL.
*   Xây dựng Form Upload linh hoạt, có thể dùng ở Màn hình Kho Nhạc hoặc Màn hình Chi tiết Nghệ sĩ.

---

## 2. Kiến trúc Data Flow (Luồng Upload S3 Pre-signed URL)

Bảo mật và tối ưu băng thông là ưu tiên hàng đầu. Luồng upload gồm 4 bước chính:

1.  **Request Upload (Client -> Laravel):** 
    Khi Admin chọn file Audio (`.mp3`/`.wav`), Frontend gửi request chứa `file_name` và `content_type` lên Backend.
2.  **Generate URL (Laravel -> S3 -> Client):** 
    Laravel gọi AWS SDK tạo ra một **Pre-signed URL** (có thời hạn 5-10 phút) và trả URL này về cho Frontend.
3.  **Direct Upload (Client -> S3):** 
    Frontend dùng `Axios PUT` đẩy trực tiếp byte file âm thanh lên S3 URL vừa nhận, kèm theo thanh Progress Bar theo dõi tiến độ (`onUploadProgress`). Laravel không can thiệp vào luồng truyền tải này.
4.  **Save Metadata (Client -> Laravel):** 
    Khi S3 trả về `200 OK` (Tải lên thành công), Frontend gửi thông tin Metadata (Tên bài hát, Nghệ sĩ, Thể loại, đường dẫn S3 Key) về Laravel API để lưu DB. Backend tạo Job FFmpeg ngầm và đánh dấu bài hát là `Approved`.

---

## 3. Cấu trúc Component Frontend (FSD Pattern)

*   **Views:**
    *   `src/views/admin/inventory/InventoryView.vue`: Layout chính, chứa Header và 2 Tabs.
    *   `src/views/admin/inventory/components/tabs/SongTab.vue`: Bảng danh sách Bài hát (có filter, actions).
    *   `src/views/admin/inventory/components/tabs/AlbumTab.vue`: Bảng danh sách Album.
*   **Features (Chức năng Upload):**
    *   `src/components/admin/features/inventory/AdminUploadForm.vue`: Form bự xử lý Metadata và File input.
    *   `src/components/admin/features/inventory/UploadDrawer.vue` (hoặc `UploadModal.vue`): Trình bọc Form Upload.
*   **Services:**
    *   `src/services/s3UploadService.ts`: Tách riêng service chuyên thực hiện gọi Pre-signed API và Axios PUT file.

---

## 4. State Management (Pinia Store)

**Store:** `src/stores/inventoryStore.ts`

*   **State:** 
    *   `songs`, `albums` (Danh sách dữ liệu cho bảng).
    *   `isLoading`, `error` (Trạng thái API).
    *   `uploadState`: Theo dõi trạng thái tiến trình (Ví dụ: `idle`, `requesting_url`, `uploading`, `saving_metadata`, `success`).
    *   `uploadProgress` (0 - 100%).
*   **Actions:**
    *   `fetchSongs(params)`
    *   `fetchAlbums(params)`
    *   `processSongUpload(file, metadata)`: Hàm Master gọi chuỗi liên hoàn (Lấy Presigned URL -> Upload S3 -> Save Metadata).

---

## 5. UI/UX Rules & Ràng buộc
1.  **Dropzone & Progress Bar:** Khu vực chọn nhạc phải hỗ trợ kéo thả (Drag & Drop), khi đang up phải khóa Form và hiện thanh Progress Bar.
2.  **Gán Nghệ Sĩ (Artist Autocomplete):** Ô nhập Nghệ sĩ trong Form upload không được liệt kê hàng ngàn Artist mà phải dùng kỹ thuật Async Search (Gõ text -> Gọi API tìm kiếm `[API-ADM-27]` -> Hiển thị kết quả).
3.  **Read-only Mode:** Nếu Form upload được kích hoạt từ trang Chi tiết của 1 Nghệ sĩ cụ thể, input gán Nghệ Sĩ phải khóa cứng (disabled) để tránh nhầm lẫn.
4.  **I18n:** Mọi text phải dùng `$t('admin.inventory.xxx')`.

---

## 6. Lộ trình Thực thi Tiếp theo
*   **Bước 1:** Backend cần chuẩn bị trước các endpoint cho Inventory, đặc biệt là endpoint tạo `Presigned URL`.
*   **Bước 2:** Frontend sẽ dựng UI Tab, Bảng, và giao diện Form Upload.
*   **Bước 3:** Ghép nối `s3UploadService` và Pinia store để hoàn thành luồng tải file.
