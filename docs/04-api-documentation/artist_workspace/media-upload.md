# Màn hình: [SCR-ART-03] Đăng tải bài hát (Media Upload Flow)

**Mô tả:** Luồng nghiệp vụ cho phép Nghệ sĩ (Artist) tải lên bài hát mới, bao gồm file âm thanh, ảnh bìa và metadata. Luồng này liên kết chặt chẽ với hệ thống xử lý bất đồng bộ (FFmpeg).

**Liên kết giao diện:** [SCR-ART-03](../../screens/Artist/SCR-ART-03-upload-song.md)

## 🔄 Tóm tắt luồng gọi API (Workflow)
1. **Khởi tạo form:** Gọi `[API-260]` để lấy cây danh mục Thể loại đổ vào Dropdown.
2. **Submit Upload:** Bấm nút Tải lên -> Gọi `[API-221]` gửi data dạng `multipart/form-data`. Nhận về `song_id` và trạng thái `Processing`.
3. **Polling Trạng thái:** Giao diện bắt đầu gọi ngầm `[API-224]` mỗi 5 giây để kiểm tra tiến trình FFmpeg. Nếu trả về `Pending` -> Thành công. Nếu trả về `Failed` -> Thất bại.
4. **Xử lý lỗi (Nếu có):** Nếu trạng thái là `Failed`, hiển thị nút "Thử lại". User bấm vào -> Gọi `[API-225]`.

---

## 💻 Chi tiết các APIs

### 1. [API-260] Lấy cây danh mục Thể loại
**Tính năng:** Lấy danh sách thể loại âm nhạc để người dùng chọn khi upload. Trả về dưới dạng cây (Tree) vì thể loại có phân cấp Cha-Con.
* **Endpoint:** `GET /api/v1/artist/genres/tree`
* **Auth:** Bearer Token (Artist)

**Success Response (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Nhạc Trẻ",
      "children": [
        { "id": 5, "name": "Pop Ballad" },
        { "id": 6, "name": "R&B Việt" }
      ]
    },
    {
      "id": 2,
      "name": "EDM",
      "children": []
    }
  ]
}
```

---

### 2. [API-221] Đăng tải bài hát mới
**Tính năng:** API nhận file vật lý (âm thanh, ảnh bìa) và metadata, lưu vào MinIO, tạo record DB và đẩy Job vào Queue.
* **Endpoint:** `POST /api/v1/artist/songs`
* **Auth:** Bearer Token (Artist)
* **Content-Type:** `multipart/form-data`

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| title | string | required, max:255 | Tên bài hát |
| genre_id | integer | required, exists:genres,id | ID thể loại |
| cover_image | file | required, image, max:2048 | Ảnh bìa (Max 2MB, jpg/png) |
| audio_file | file | required, mimes:mp3,wav, max:20480 | File nhạc gốc (Max 20MB) |
| description | string | nullable, max:1000 | Chú thích / Lời nhắn |

**Success Response (201 Created):**
```json
{
  "status": "success",
  "message": "Bài hát đã được tải lên và đang chờ xử lý âm thanh.",
  "data": {
    "song_id": 99,
    "title": "Cơn Mưa Ngang Qua",
    "status": "Processing"
  }
}
```

**Error Response (422 Unprocessable Entity):**
```json
{
  "status": "error",
  "message": "The given data was invalid.",
  "errors": {
    "audio_file": ["The audio file must not be greater than 20MB."]
  }
}
```

---

### 3. [API-224] Kiểm tra trạng thái xử lý (Polling Status)
**Tính năng:** Do FFmpeg mất thời gian chạy (có thể 1-2 phút), API này dùng để Frontend lấy trạng thái mới nhất để cập nhật thanh Loading.
* **Endpoint:** `GET /api/v1/artist/songs/{id}/status`
* **Auth:** Bearer Token (Artist)

**Success Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "song_id": 99,
    "status": "Pending", 
    "processing_error": null
  }
}
```
*(Ghi chú: Nếu `status` chuyển từ `Processing` sang `Pending`, tiến trình thành công. Nếu là `Failed`, hiển thị lỗi `processing_error`).*

---

### 4. [API-225] Yêu cầu xử lý lại (Retry Transcoding)
**Tính năng:** Nếu FFmpeg lỗi (do server nghẽn), Artist bấm Retry. Hệ thống dùng `original_file_path` để chạy lại Job.
* **Endpoint:** `POST /api/v1/artist/songs/{id}/retry`
* **Auth:** Bearer Token (Artist)

**Request Body:** *(Empty Body)*

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Đã đưa bài hát vào hàng đợi xử lý lại.",
  "data": {
    "song_id": 99,
    "status": "Processing",
    "processing_attempts": 2
  }
}
```
**Error Response (400 Bad Request):**
*Xảy ra nếu bài hát không ở trạng thái Failed, hoặc đã quá số lần Retry (Max: 3).*
```json
{
  "status": "error",
  "message": "Bài hát không ở trạng thái lỗi hoặc đã vượt quá giới hạn thử lại."
}
```
