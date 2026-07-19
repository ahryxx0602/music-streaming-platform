# Kế hoạch Triển khai Backend API - Quản lý Kho Nhạc & S3 Upload

Tài liệu này mô tả chi tiết phương án kỹ thuật triển khai backend cho chức năng Kho Nhạc (`SCR-ADM-10-song-inventory`), ứng dụng mô hình Direct Upload S3 qua Pre-signed URL.
Chi tiết Endpoints: **[`API-07-SongInventory.md`](./API-07-SongInventory.md)**.

## 1. Cấu hình MinIO / AWS S3
- **Gói yêu cầu:** `composer require league/flysystem-aws-s3-v3 "^3.0"`
- **Biến môi trường (`.env`):**
  - Cần cấu hình `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, `AWS_ENDPOINT` (Dành cho MinIO).
- **Disk Configuration (`config/filesystems.php`):**
  - Đảm bảo disk `s3` đã được setup đầy đủ.

## 2. Thiết kế Cơ sở Dữ liệu (`songs` table)
- Bảng `songs` cần đảm bảo các trường lưu trữ Metadata:
  - `title`, `slug`.
  - `artist_id` (FK), `genre_id` (FK).
  - `s3_key` (Đường dẫn gốc trên S3).
  - `status` (Enum: `Approved`, `Pending`, `Hidden`). Mặc định Admin upload -> `Approved`.
  - `uploader_id` (FK tới Admin thực hiện upload) để tracking (đáp ứng `RULE-ADM-INV-02`).

## 3. Controller & Luồng xử lý
- **Controller:** `Modules/Music/Http/Controllers/AdminSongController.php`.
- **Hàm `generatePresignedUrl`:**
  - Inject `Storage::disk('s3')`.
  - Validate định dạng file (chỉ cho phép audio).
  - Khởi tạo 1 S3 Key duy nhất (ví dụ: `songs/raw/2026/07/uuid.mp3`).
  - Gọi `$client->createPresignedRequest()` với Command `PutObject` và thời hạn 10 phút.
- **Hàm `store`:**
  - Nhận metadata và `s3_key`.
  - Lưu vào DB.
  - Dispatch FFmpeg Transcoding Job (Sẽ xử lý trong Phase khác, tạm thời log lại thông báo).
- **Hàm `index`:**
  - Lấy danh sách kết hợp Eager Loading (`artist`, `genre`) và phân trang.

## 4. Các câu hỏi làm rõ (Q&A) dành cho System Architect:
1. Bạn đang sử dụng AWS S3 thật hay giả lập MinIO trên Local? Nếu dùng MinIO, tôi sẽ lưu ý cấu hình `use_path_style_endpoint = true` trong config để tránh lỗi resolve domain.
2. Bạn có muốn kích hoạt (dispatch) Job FFmpeg ngay lập tức ở hàm `store` luôn không, hay tạm thời cứ lưu Metadata thành công là đủ, phần Job sẽ tách ra một Phase riêng?
