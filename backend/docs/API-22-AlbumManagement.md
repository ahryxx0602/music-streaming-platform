# Kiến trúc Backend API: Phân hệ Nghệ sĩ - [SCR-ART-04] Quản lý Album & EP

## 1. Cấu trúc Database (Phân tích & Cải tiến)
Bảng `albums` đã có đầy đủ các trường cơ bản:
- `title`, `artist_id`, `cover_image`, `release_date`, `type` (Single/EP/Album), `description`, `status`.

**Mối quan hệ:** Một Album có nhiều bài hát (One-to-Many). Bảng `songs` lưu khóa ngoại `album_id`.

**⚠️ PHÁT HIỆN LỖ HỔNG THIẾT KẾ:**
Hiện tại bảng `songs` chưa có cột để lưu trữ vị trí thứ tự bài hát trong Album. Để phục vụ tính năng "Kéo thả cập nhật thứ tự" một cách chính xác, **BẮT BUỘC** phải bổ sung thêm cột `track_number` (Integer, default 0) vào bảng `songs`. Việc này sẽ được thực hiện thông qua một file Migration mới (`add_track_number_to_songs_table`).

## 2. Giải pháp cập nhật thứ tự (Drag & Drop) hiệu quả cao
Khi Frontend gửi lên mảng `song_ids: [105, 108, 102]`, yêu cầu là phải cập nhật lại thứ tự của 3 bài này thành track 1, 2, 3 mà **không gây ra lỗi truy vấn N+1**.

**Phương pháp tối ưu nhất (Single Query Bulk Update):**
Sử dụng tính năng `upsert` của Eloquent (được hỗ trợ rất tốt từ Laravel 8+). Phương pháp này biên dịch mảng dữ liệu thành một câu lệnh SQL duy nhất dạng `INSERT ... ON DUPLICATE KEY UPDATE` (MySQL) hoặc `INSERT ... ON CONFLICT DO UPDATE` (PostgreSQL).

```php
$songIds = $request->input('song_ids'); // [105, 108, 102]
$upsertData = [];

foreach ($songIds as $index => $songId) {
    $upsertData[] = [
        'id' => $songId,
        'track_number' => $index + 1
    ];
}

// Chạy 1 query duy nhất, không bị N+1
Song::upsert($upsertData, ['id'], ['track_number']);
```

## 3. Thiết kế RESTful API Endpoints

**Base Route:** `/api/v1/artist/albums`
**Middleware:** `auth:sanctum`, `role:Artist|artist`

### 3.1 Lấy danh sách Album
- **Method:** `GET /`
- **Logic:** Lấy danh sách Album của `artist_id` hiện tại, có phân trang, sort theo thời gian tạo. Kèm theo số lượng bài hát (`withCount('songs')`).

### 3.2 Tạo Album mới
- **Method:** `POST /`
- **Payload (`multipart/form-data`):**
  - `title` (string, required)
  - `type` (enum: Single, EP, Album, required)
  - `release_date` (date, optional)
  - `description` (text, optional)
  - `cover_image` (file: jpeg,png,jpg, max 5MB, optional)
  - `song_ids` (array, optional) - Danh sách các bài hát gán ngay vào album lúc tạo.
- **Logic:**
  1. Validate dữ liệu.
  2. Upload `cover_image` lên S3 (nếu có).
  3. Tạo record trong bảng `albums`.
  4. Nếu có mảng `song_ids`, tiến hành update `album_id` và `track_number` cho các bài hát này.

### 3.3 Xem chi tiết Album
- **Method:** `GET /{id}`
- **Logic:** Trả về thông tin Album và danh sách bài hát (`with(['songs' => function($q) { $q->orderBy('track_number'); }])`).
- **Authorization:** Phải check quyền sở hữu (Album này phải của chính Artist đang đăng nhập).

### 3.4 Cập nhật thông tin Album
- **Method:** `POST /{id}` (Dùng POST kèm `_method=PUT` do có upload ảnh, hoặc dùng PUT body json nếu không đổi ảnh).
- **Payload:** Tương tự API Tạo mới, các field optional.
- **Logic:** Update info, thay ảnh bìa (xóa ảnh cũ trên S3 nếu có ảnh mới).

### 3.5 Reorder Bài hát trong Album (Kéo thả)
- **Method:** `PUT /{id}/reorder-songs`
- **Payload (`application/json`):**
  - `song_ids` (array of integers, required): Mảng ID bài hát theo thứ tự mới.
- **Logic:** 
  1. Validate `song_ids` đều phải thuộc về `$album->id` và thuộc về Artist này.
  2. Dùng thuật toán `Song::upsert()` như phân tích ở Mục 2 để ghi đè thứ tự bằng 1 query.

### 3.6 Xóa Album
- **Method:** `DELETE /{id}`
- **Logic:**
  1. Check quyền sở hữu.
  2. [RULE-ALB-02]: Gỡ `album_id` của tất cả bài hát thuộc album này (Update `album_id = null`).
  3. Xóa mềm (Soft Delete) album.
  4. Không xóa ảnh trên S3 để có thể phục hồi nếu cần.

## 4. Công việc cho Backend Developer
1. **Migration:** Tạo migration bổ sung cột `track_number` vào bảng `songs`.
2. **Model:** Khai báo relation `songs()` trong `Album` và `album()` trong `Song`.
3. **Controller:** Khởi tạo `ArtistAlbumController` và viết toàn bộ 6 API trên.
4. **Testing:** Viết `ArtistAlbumControllerTest` với DB Transactions và Mock S3. Đảm bảo kiểm tra kỹ tính năng `upsert` reorder bài hát.
