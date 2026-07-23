# Kiến trúc Backend API: [SCR-LST-06] Tìm kiếm (Search Engine)

## 1. Yêu cầu & Luồng xử lý
Tính năng Tìm kiếm (Global Search) cho phép người dùng gõ từ khóa (keyword) và hệ thống trả về kết quả tổng hợp từ nhiều nguồn dữ liệu: **Bài hát (Songs), Nghệ sĩ (Artists), và Album/EP (Albums)**.
Vì đây là chức năng được gọi nhiều (có thể thông qua Debounce gõ phím), API cần phải tối ưu query để tránh làm nghẽn DB.

## 2. Thiết kế API Endpoint

### `GET /api/v1/listener/search`
- **Controller:** `Modules/Music/Http/Controllers/ListenerSearchController.php`
- **Middleware:** Public (Không cần Auth).
- **Query Parameter:** `?q=keyword` (bắt buộc).

**Logic tìm kiếm:**
Nếu `q` có độ dài < 2 ký tự, trả về rỗng (hoặc lỗi validation) để giảm tải.
Truy vấn sử dụng `LIKE %keyword%` (hoặc Full-Text Search nếu MySQL được cấu hình):
1. **Songs:** Tìm trong cột `title` (chỉ lấy bài hát có `status = published`), limit 10.
2. **Artists:** Tìm trong bảng `users` (join với `artist_profiles`) thông qua cột `name` hoặc `stage_name`, limit 5.
3. **Albums:** Tìm trong bảng `albums` thông qua cột `title` (chỉ lấy published), limit 5.

**Cấu trúc JSON Trả về:**
```json
{
  "success": true,
  "data": {
    "songs": [
      {
        "id": 1,
        "title": "Shape of You",
        "artist": { "id": 5, "stage_name": "Ed Sheeran" },
        "cover_url": "...",
        "audio_url": "...",
        "duration": 233
      }
    ],
    "artists": [
      {
        "id": 5,
        "stage_name": "Ed Sheeran",
        "avatar_url": "..."
      }
    ],
    "albums": [
      {
        "id": 10,
        "title": "Divide",
        "cover_url": "...",
        "artist": { "stage_name": "Ed Sheeran" }
      }
    ]
  }
}
```

## 3. Công việc cho Backend Agent
1. **Controller:** Tạo `ListenerSearchController` với method `index(Request $request)`.
2. **Logic Truy vấn song song:** 
   - Lấy query `$q = $request->get('q')`.
   - Nếu empty hoặc len < 2 -> return empty array for all.
   - Viết 3 truy vấn Eloquent độc lập nhưng gói trong 1 hàm trả về.
3. **Hiệu suất (Chống N+1):** Bắt buộc dùng `with(['artistProfile'])` khi query Songs và Albums để tránh dính N+1 query.
4. **Viết Test:** `ListenerSearchControllerTest` giả lập dữ liệu và gọi `/search?q=test` để kiểm tra JSON Structure. Báo cáo kết quả, TUYỆT ĐỐI KHÔNG PUSH.
