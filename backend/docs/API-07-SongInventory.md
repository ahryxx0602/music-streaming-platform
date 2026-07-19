# API Reference: Quản lý Kho Nhạc (Song Inventory) & S3 Upload

Đặc tả các Endpoints dành cho Admin để quản lý Bài hát và luồng Upload file âm thanh lớn trực tiếp lên AWS S3 / MinIO thông qua Pre-signed URL.

## 1. Lấy Pre-signed URL (Upload Trực tiếp S3)
- **Mã API:** `[API-ADM-27]`
- **Method:** `POST`
- **Endpoint:** `/api/v1/admin/songs/presigned-url`
- **Middleware:** `auth:sanctum`, `role:admin|content-manager`

### Request Body (JSON)
```json
{
  "file_name": "bai-hat-moi.mp3",
  "content_type": "audio/mpeg",
  "file_size": 15432000
}
```

### Response Thành công (201 Created)
```json
{
  "success": true,
  "message": "Tạo S3 Presigned URL thành công.",
  "data": {
    "upload_url": "https://s3.amazonaws.com/my-bucket/songs/raw/2026/07/uuid-bai-hat-moi.mp3?X-Amz-Algorithm=AWS4-HMAC-SHA256&...",
    "s3_key": "songs/raw/2026/07/uuid-bai-hat-moi.mp3",
    "expires_in": 600
  }
}
```

---

## 2. Lưu Metadata Bài hát (Sau khi Client Upload S3 xong)
- **Mã API:** `[API-ADM-28]`
- **Method:** `POST`
- **Endpoint:** `/api/v1/admin/songs`
- **Middleware:** `auth:sanctum`, `role:admin|content-manager`

### Request Body (JSON)
```json
{
  "title": "Tên bài hát mới",
  "artist_id": 12,
  "genre_id": 4,
  "s3_key": "songs/raw/2026/07/uuid-bai-hat-moi.mp3"
}
```

### Response Thành công (201 Created)
```json
{
  "success": true,
  "message": "Lưu thông tin bài hát thành công. Đang tiến hành xử lý Audio.",
  "data": {
    "song": {
      "id": 105,
      "title": "Tên bài hát mới",
      "status": "Approved",
      "s3_key": "songs/raw/2026/07/uuid-bai-hat-moi.mp3"
    }
  }
}
```
*(Lưu ý: Ngay sau khi API này trả về, Backend sẽ tự động dispatch một Job FFmpeg để chuyển đổi mp3 thành HLS).*

---

## 3. Lấy Danh sách Bài hát (Phân trang & Lọc)
- **Mã API:** `[API-360]`
- **Method:** `GET`
- **Endpoint:** `/api/v1/admin/songs`
- **Middleware:** `auth:sanctum`, `role:admin|content-manager`

### Query Parameters
- `page`: int
- `search`: string (Tìm theo tên bài hát)
- `status`: string (Approved, Pending, Hidden)
- `artist_id`: int

### Response Thành công (200 OK)
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 105,
        "title": "Tên bài hát mới",
        "artist": {
           "id": 12,
           "stage_name": "Sơn Tùng M-TP"
        },
        "genre": {
           "id": 4,
           "name": "Bolero"
        },
        "status": "Approved",
        "streams": 0
      }
    ],
    "total": 1
  }
}
```
