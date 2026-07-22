# Kiến trúc Backend API: Phân hệ Nghệ sĩ - [SCR-ART-03] Tải nhạc lên (Upload Song)

## 1. Yêu cầu Hệ thống & Luồng xử lý (ĐÃ ĐƯỢC CHUYÊN GIA REVIEW LẠI)
Tính năng này cho phép Artist tải lên file âm thanh (`.mp3`, `.wav`) cùng với thông tin metadata (ảnh bìa, tiêu đề, thể loại, v.v.).

**ĐÁNH GIÁ TỪ CHUYÊN GIA KIẾN TRÚC:**
Việc cho phép upload file âm thanh 50MB trực tiếp qua API Server thông qua `multipart/form-data` là **CỰC KỲ RỦI RO** vì:
1. **Nghẽn băng thông & Tràn RAM:** Các tiến trình PHP-FPM sẽ bị block trong suốt thời gian client upload, tiêu tốn lượng lớn RAM và I/O, gây sập API nếu có nhiều người upload cùng lúc.
2. **Quy tắc hệ thống:** Hệ thống đã được định hướng sử dụng **S3 Pre-signed URL** từ các Phase trước để giải bài toán chống nghẽn.

**LUỒNG XỬ LÝ CHUẨN (BẮT BUỘC SỬ DỤNG):**
1. **Bước 1:** Client gọi API `[POST] /presigned-url` để xin chữ ký tạm thời từ Backend, lấy URL của S3 (hoặc MinIO).
2. **Bước 2:** Client upload trực tiếp file audio thẳng lên S3 thông qua URL vừa nhận bằng method `PUT`. Backend không hề phải chịu tải.
3. **Bước 3:** Sau khi upload xong, Client gọi API `[POST] /songs` gửi Metadata (title, cover_image, file_path_trên_s3) cho Backend.
4. **Bước 4:** Backend lưu Metadata vào DB, gán `processing_status = 'processing'` và `status = 'pending'`, sau đó đẩy Job vào Message Queue (FFmpeg) để xử lý Audio.

## 2. Thiết kế API Endpoint

### API 1: Lấy S3 Presigned URL để Upload Audio
- **Endpoint:** `POST /api/v1/artist/songs/presigned-url`
- **Controller:** `Modules\Music\Http\Controllers\ArtistSongController`
- **Middleware:** `auth:sanctum`, `role:Artist|artist`
- **Request Format:** `application/json`
  - `file_name` (string, bắt buộc)
  - `content_type` (string, bắt buộc, ví dụ: `audio/mpeg` hoặc `audio/wav`)
- **Xử lý Logic:** 
  - Backend sử dụng AWS SDK hoặc `league/flysystem-aws-s3-v3` để sinh Presigned URL với thời hạn 10 phút.
  - Trả về `url` và `path` (để Frontend dùng ở API 2).

### API 2: Lưu Metadata Bài Hát
- **Endpoint:** `POST /api/v1/artist/songs`
- **Controller:** `Modules\Music\Http\Controllers\ArtistSongController`
- **Middleware:** `auth:sanctum`, `role:Artist|artist`
- **Request Format:** `multipart/form-data` (Chỉ dùng để upload ảnh bìa)
  - `title` (string, bắt buộc, max 255)
  - `genre_id` (integer, bắt buộc, exists in `genres.id`)
  - `audio_path` (string, bắt buộc) - Đường dẫn file S3 nhận được từ API 1
  - `cover_image` (file, không bắt buộc, mimes:jpeg,png,jpg, max:5120)
  - `lyrics` (text, không bắt buộc)
  - `album_id` (integer, không bắt buộc)
- **Xử lý Logic & Bảo mật:**
  1. **Bảo mật File Audio:** Backend KHÔNG validate file bằng `mimes:mp3,wav` qua Laravel Request nữa vì file đã nằm trên S3. Thay vào đó, Job FFmpeg ngầm chạy sau này sẽ kiểm tra headers file bằng `ffprobe`. Tránh rủi ro tin tặc giả mạo đuôi file chứa mã độc.
  2. **Xử lý Metadata (Duration):** Tuyệt đối KHÔNG tin tưởng trường `duration` do Frontend gửi lên (vì Client có thể thao túng dữ liệu này). Tạm thời lưu `duration = 0` hoặc `null`. Sau đó FFmpeg Queue Job sẽ load file, đo chính xác thời lượng và update vào Database để đảm bảo Data Integrity.
  3. Upload `cover_image` (nếu có) lên Storage, lấy đường dẫn URL.
  4. Tạo bản ghi trong bảng `songs`:
     - `title`: $request->title
     - `artist_id`: $artistProfile->id
     - `genre_id`: $request->genre_id
     - `original_file_path`: $request->audio_path
     - `cover_image_url`: $coverPath
     - `status`: 'pending' (Chờ duyệt)
     - `processing_status`: 'pending' (Chờ xử lý âm thanh)
  5. Dispatch Queue Job (`ProcessAudioJob`) để xử lý FFmpeg.
- **Response Format:**
  ```json
  {
      "success": true,
      "message": "Metadata đã được lưu. Đang xử lý âm thanh ngầm.",
      "data": {
          "id": 105,
          "title": "Neon Lights",
          "status": "pending",
          "processing_status": "pending"
      }
  }
  ```

### API 2: Lấy danh sách Thể loại (Dropdown Genres)
- **Endpoint:** `GET /api/v1/genres`
- Đã tồn tại trong `Modules/Music` hay chưa? Nếu chưa thì tạo một endpoint public (hoặc auth) để frontend lấy danh sách điền vào `<select>`.

## 3. Database
Bảng `songs` (đã có từ trước):
- Cần chú ý trường `status` phải nhận giá trị Enum hoặc chuỗi như `'Pending', 'Approved', 'Rejected'`.
- Trường `artist_id` phải mapping đúng với ID trong `artist_profiles`.
- Trường `original_file_path` để lưu file nguồn gốc tải lên.

## 4. Công việc của Backend Agent
1. **Thiết lập S3 Presigned URL:** Viết API `[POST] /presigned-url` sinh URL upload cho Audio.
2. **Cập nhật Logic Lưu Metadata:** Viết Request và Controller để nhận `audio_path`, không còn nhận `audio_file` dạng upload trực tiếp nữa.
3. **Dispatch Job:** Tích hợp `ProcessAudioJob` (Đã được triển khai ở Phase trước) để xử lý FFmpeg và duration.
4. **Viết Test Cases:** Chú ý Mock AWS S3 client để test Presigned URL, và sử dụng fake request cho cover_image.
