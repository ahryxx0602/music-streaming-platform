# Kế hoạch Triển khai (Implementation Plan) - Xử lý Âm thanh FFmpeg (Audio Processing Phase)

Tài liệu này đặc tả kiến trúc luồng xử lý âm thanh ngầm (Background Audio Processing) sử dụng **FFmpeg** kết hợp **Laravel Queue (Redis)**. Mục tiêu là chuyển đổi các file nhạc thô (Raw Audio) thành chuẩn **HLS (HTTP Live Streaming)** để tối ưu hóa việc phát nhạc trên trình duyệt và di động, chống giật lag và chống tải file lậu.

---

## 1. Mục tiêu (Objectives)
*   Tự động tính toán thời lượng bài hát (Duration).
*   Chuyển đổi file `.mp3`/`.wav` thô thành định dạng HLS (Playlist `.m3u8` và các phân mảnh `.ts`).
*   Thực hiện hoàn toàn dưới Background Job (Queue) để không làm treo Request của người dùng.
*   Lưu trữ kết quả đã xử lý lên lại AWS S3 / MinIO.

---

## 2. Kiến trúc Hệ thống (Architecture)

Luồng xử lý gồm 4 bước chính trong 1 Job:
1.  **Trigger (Kích hoạt):** 
    Sau khi API Lưu Metadata (`AdminSongController@store`) chạy xong, Laravel sẽ *Dispatch* (đẩy) một Job có tên `ProcessAudioJob` vào hàng đợi (Queue) của Redis.
2.  **Download & Extract (Tải & Trích xuất):** 
    Worker (chạy ngầm) bắt được Job -> Tiến hành tải file thô từ S3 về thư mục tạm (Local Temp) -> Dùng FFmpeg đọc file để lấy thông số `duration` (giây).
3.  **Transcode (Chuyển đổi định dạng):** 
    Sử dụng FFmpeg cắt nhỏ file audio thành các đoạn (segment) dài 10 giây (chuẩn HLS) và tạo file Master Playlist (`.m3u8`).
4.  **Upload & Cleanup (Tải lên & Dọn dẹp):** 
    Đẩy toàn bộ thư mục HLS (gồm file `.m3u8` và các file `.ts`) ngược lên lại S3 -> Xóa các file rác ở Local Temp -> Cập nhật Database (lưu `duration`, `hls_path` và đổi trạng thái xử lý thành `completed`).

---

## 3. Cập nhật Database (Migration)

Cần tạo 1 file migration để thêm các cột quản lý tiến trình vào bảng `songs`:
*   `duration` (int, nullable): Thời lượng bài hát (tính bằng giây).
*   `hls_path` (string, nullable): Đường dẫn tới file `.m3u8` trên S3 (Ví dụ: `songs/hls/2026/07/uuid/master.m3u8`).
*   `processing_status` (enum: `pending`, `processing`, `completed`, `failed`): Trạng thái xử lý FFmpeg (Mặc định: `pending`).

---

## 4. Công cụ & Thư viện (Dependencies)
*   **Hệ điều hành:** Cần cài đặt sẵn phần mềm `ffmpeg` và `ffprobe` (trên WSL hoặc Docker container).
*   **Package PHP:** Sử dụng package `protonemedia/laravel-ffmpeg` (Là wrapper tốt nhất của FFmpeg cho Laravel, hỗ trợ HLS và tích hợp sẵn Laravel Storage S3).
*   **Queue Driver:** Đảm bảo `.env` đang set `QUEUE_CONNECTION=redis` và Redis đang chạy.

---

## 5. Lộ trình Thực thi (Dành cho Backend)
*   **Bước 1:** Cài đặt package `protonemedia/laravel-ffmpeg` và kiểm tra config.
*   **Bước 2:** Chạy migration thêm cột cho bảng `songs`.
*   **Bước 3:** Viết Job `ProcessAudioJob.php` tuân thủ đúng 4 bước Architecture.
*   **Bước 4:** Quay lại `AdminSongController@store` và mở comment `// Todo: FFmpeg Job dispatch`, gọi lệnh `ProcessAudioJob::dispatch($song);`.
*   **Bước 5:** Bật `php artisan queue:work` và test thử luồng chạy thực tế.
