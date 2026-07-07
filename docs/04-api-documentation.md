# TÀI LIỆU API (API DOCUMENTATION)

**Dự án:** Nền tảng Âm nhạc Trực tuyến (Audio Streaming Web App)
**Phiên bản:** 1.0

---

## 1. Giới thiệu

Tài liệu này định nghĩa các chuẩn giao tiếp RESTful API giữa hệ thống Frontend (VueJS) và Backend (Laravel 13). 

- **Base URL:** `/api/v1`
- **Content-Type:** `application/json` (Đối với Upload dùng `multipart/form-data`)
- **Authentication:** Laravel Sanctum SPA (Sử dụng `HttpOnly` Cookie thay vì Bearer Token để chống XSS)

---

## 2. Tiêu chuẩn Response

Hệ thống sử dụng cấu trúc Response thống nhất (JSend Format).

### 2.1 Thành công (HTTP 200, 201)
```json
{
  "status": "success",
  "message": "Thông báo (nếu có)",
  "data": { ... }
}
```

### 2.2 Phân trang (Pagination)

**Request Payload:** Client gửi Query Parameters `?page=1&per_page=20` (Mặc định `per_page=20`).

**Response Format:**
```json
{
  "status": "success",
  "data": {
    "items": [ ... ],
    "meta": {
      "current_page": 1,
      "last_page": 5,
      "per_page": 20,
      "total": 100
    }
  }
}
```

### 2.3 Lỗi Validate (HTTP 422)
```json
{
  "status": "error",
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "email": ["Email đã tồn tại trong hệ thống."],
    "password": ["Mật khẩu phải từ 8 ký tự."]
  }
}
```

### 2.4 Lỗi Client / Server (HTTP 400, 401, 403, 404, 429, 500)
```json
{
  "status": "error",
  "message": "Resource not found."
}
```

### 2.5 Bảng mã HTTP Status Codes

Hệ thống tuân thủ chặt chẽ các mã lỗi HTTP tiêu chuẩn để Client dễ dàng handle:

| Status Code | Ý nghĩa | Mô tả |
|---|---|---|
| **200 OK** | Thành công | Yêu cầu được xử lý thành công (Thường cho GET, PUT, DELETE). |
| **201 Created** | Đã tạo | Resource mới được tạo thành công (Thường cho POST). |
| **400 Bad Request** | Request sai | Cú pháp request sai hoặc logic không hợp lệ. |
| **401 Unauthorized** | Chưa xác thực | Token hết hạn, không hợp lệ hoặc không được gửi kèm. |
| **403 Forbidden** | Không có quyền | Token hợp lệ nhưng Role không có quyền truy cập (VD: Listener gọi API của Artist). |
| **404 Not Found** | Không tìm thấy | Resource không tồn tại (Bài hát bị xóa, User không tồn tại). |
| **409 Conflict** | Xung đột phiên | Phát hiện truy cập trùng lặp từ thiết bị khác (Session Concurrency). |
| **422 Unprocessable** | Lỗi Validate | Dữ liệu gửi lên không thỏa mãn quy tắc kiểm tra (Form Validation). |
| **429 Too Many Req**| Vượt giới hạn | Bị chặn do gọi API quá số lần cho phép (Rate Limit). |
| **500 Server Error** | Lỗi hệ thống | Lỗi phát sinh từ phía Backend hoặc Database. |

---

## 3. Danh sách API Endpoints (Phân tách theo Role)

Hệ thống tuân thủ nguyên tắc **Strict Role-based API**: Không sử dụng chung Endpoint cho các Role khác nhau nhằm đảm bảo bảo mật và dễ quản lý phân quyền.

### 3.1 Guest API (Không yêu cầu Auth)

Các API dành cho người dùng chưa đăng nhập (Khách vãng lai). Prefix: `/api/v1/guest`

| Endpoint | Method | Description |
|---|---|---|
| `/guest/auth/register` | POST | [API-GUEST-01] Đăng ký tài khoản Listener mới |
| `/guest/auth/login` | POST | [API-GUEST-02] Đăng nhập và nhận Token |
| `/guest/songs/trending` | GET | [API-GUEST-03] Lấy danh sách bài hát thịnh hành |
| `/guest/songs/{id}` | GET | [API-GUEST-04] Xem chi tiết bài hát (metadata) |
| `/guest/genres` | GET | [API-GUEST-05] Lấy danh sách thể loại (Cached) |
| `/guest/banners` | GET | [API-GUEST-06] Lấy danh sách Banner trang chủ |
| `/guest/search` | GET | [API-GUEST-07] Tìm kiếm tổng hợp (Song, Artist, Album) |

---

### 3.2 Listener API (Role: Listener)

Các API dành riêng cho người dùng đã đăng nhập với vai trò Listener. Prefix: `/api/v1/listener`
*Yêu cầu: Client đính kèm HttpOnly Cookie hợp lệ.*

| Endpoint | Method | Description |
|---|---|---|
| `/listener/auth/me` | GET | [API-LST-01] Lấy thông tin tài khoản cá nhân |
| `/listener/auth/logout`| POST | [API-LST-02] Đăng xuất |
| `/listener/stream/url/{id}` | GET | [API-LST-03] Sinh URL nghe nhạc (Signed URL cho MinIO) |
| `/listener/stream/track/{id}`| POST | [API-LST-04] Ghi nhận lượt nghe hợp lệ (Sau khi nghe >= 30s) |
| `/listener/library/favorites`| GET | [API-LST-05] Danh sách bài hát/album/artist yêu thích |
| `/listener/library/toggle` | POST | [API-LST-06] Like/Unlike bài hát, album, artist |
| `/listener/library/history` | GET | [API-LST-07] Lịch sử nghe nhạc (Resume listening) |
| `/listener/playlists` | GET/POST| [API-LST-08] Lấy danh sách / Tạo Playlist mới |
| `/listener/playlists/{id}` | GET | [API-LST-09] Chi tiết Playlist cá nhân |
| `/listener/playlists/{id}/songs` | POST/DELETE | [API-LST-10] Thêm / Xóa bài hát khỏi Playlist |
| `/listener/playlists/{id}/reorder`| PUT | [API-LST-11] Cập nhật vị trí (Reorder) các bài hát trong Playlist |

**Cơ chế Track Stream (Listener):**
1. Gọi `GET /listener/stream/url/{id}` để lấy `audio_url`.
2. Khi phát qua 30s, gọi `POST /listener/stream/track/{id}` kèm payload:
  ```json
  {
    "session_id": "uuid-v4-frontend",
    "duration_listened": 35
  }
  ```

---

### 3.3 Artist API (Role: Artist)

Các API dành riêng cho không gian làm việc của Nghệ sĩ (Artist Workspace). Prefix: `/api/v1/artist`
*Yêu cầu: Client đính kèm HttpOnly Cookie hợp lệ và User có Role = `artist`.*

| Endpoint | Method | Description |
|---|---|---|
| `/artist/auth/me` | GET | [API-ART-01] Lấy thông tin Artist Profile |
| `/artist/auth/logout`| POST | [API-ART-02] Đăng xuất |
| `/artist/songs` | GET | [API-ART-03] Lấy danh sách bài hát của chính Artist |
| `/artist/songs` | POST | [API-ART-04] Upload bài hát mới (`multipart/form-data`) |
| `/artist/songs/{id}` | PUT | [API-ART-05] Cập nhật metadata bài hát |
| `/artist/songs/{id}` | DELETE | [API-ART-06] Xóa mềm bài hát |
| `/artist/albums` | GET/POST | [API-ART-07] Quản lý Album |
| `/artist/analytics` | GET | [API-ART-08] Thống kê Dashboard (Tổng stream, follower, biểu đồ) |

**Luồng Upload (POST `/artist/songs`):**
- Trả về HTTP 201 Created và đẩy Job xử lý FFmpeg (cắt 30s preview) vào Queue `media_processing`. Trạng thái bài hát ban đầu là `Pending`.

---

### 3.4 Admin API (Role: Admin)

Các API phục vụ quản trị hệ thống. Prefix: `/api/v1/admin`
*(Lưu ý: Phân quyền chi tiết và các tính năng cụ thể của Admin sẽ được phân tích ở giai đoạn sau)*

| Endpoint | Method | Description |
|---|---|---|
| `/admin/auth/login` | POST | [API-ADM-01] Đăng nhập dành for Admin |
| `/admin/dashboard` | GET | [API-ADM-02] Thống kê tổng quan hệ thống (Tổng User, Stream, Artist) |
| `/admin/users` | GET/PUT | [API-ADM-03] Quản lý người dùng (Lấy danh sách / Ban / Unban) |
| `/admin/artists` | GET | [API-ADM-04] Lấy danh sách toàn bộ Artist |
| `/admin/artists` | POST | [API-ADM-05] Tạo mới trực tiếp tài khoản Artist (User + Artist Profile) |
| `/admin/artists/{id}` | PUT/DELETE| [API-ADM-06] Chỉnh sửa thông tin Artist (Stage name, Bio) / Khóa Artist |
| `/admin/artists/{id}/verify`| POST | [API-ADM-07] Xác minh Artist (Cấp tích xanh `is_verified` Official) |
| `/admin/genres` | GET/POST/PUT/DELETE | [API-ADM-08] Quản lý danh mục Thể loại nhạc (CRUD) |
| `/admin/banners` | GET/POST/PUT/DELETE | [API-ADM-09] Quản lý hệ thống Banner trang chủ (CRUD) |
| `/admin/songs` | GET | [API-ADM-10] Lấy danh sách toàn bộ bài hát trên hệ thống |
| `/admin/songs/pending`| GET | [API-ADM-11] Danh sách bài hát đang ở trạng thái `Pending` chờ duyệt |
| `/admin/songs/{id}/approve`| POST | [API-ADM-12] Phê duyệt bài hát (Public) |
| `/admin/songs/{id}/reject` | POST | [API-ADM-13] Từ chối bài hát |
| `/admin/songs/{id}` | DELETE | [API-ADM-14] Admin xóa (Soft delete) hoặc buộc ẩn bất kỳ bài hát nào |
| `/admin/playlists/{id}`| DELETE | [API-ADM-15] Xóa Playlist vi phạm tiêu chuẩn cộng đồng |
| `/admin/comments/{id}` | DELETE | [API-ADM-16] Xóa bình luận tiêu cực / spam |
| `/admin/audit-logs` | GET | [API-ADM-17] Xem nhật ký hoạt động (Audit Trail) của Admin/Artist |

---

## 4. Rate Limiting

Để bảo vệ hệ thống khỏi DDOS và gian lận, các API được áp dụng Rate Limit (Sử dụng Redis):

- **Global API:** 60 requests / 1 phút / IP.
- **Upload API (`POST /artist/songs`):** 10 requests / 10 phút / User.
- **Track Stream API (`POST /songs/{id}/track-stream`):** 5 requests / 1 phút / User (Ngăn chặn dùng script bắn request cày view liên tục).
- Khi vượt quá ngưỡng, hệ thống trả về mã lỗi HTTP `429 Too Many Requests`.


## 5. Chi tiết [API-LST-04] Ghi nhận lượt nghe

# 1. Endpoint & Method
- **Method**: `POST`
- **Endpoint**: `/api/v1/listener/stream/track/{id}`
- **Auth**: Cookie Sanctum (Role: `Listener`, `Artist`)

# 2. Request Payload (Body)
| Field | Type | Required | Rule Validation |
|||
| `session_id` | string | Yes | UUID v4 |
| `duration_listened`| integer | Yes | >= 30 |

# 3. Logic xử lý
- Validate payload.
- Gọi `[RULE-STREAM-01]` để check điều kiện Anti-cheat.
- Insert record vào `[DB-streams]`.

# 4. Response Schema
**201 Created**
```json
{
  "status": "success",
  "message": "Stream recorded"
}
```

# 5. Exceptions
- `429 Too Many Requests`: Vi phạm Rule Anti-cheat (Rate limit).
- `404 Not Found`: Bài hát không tồn tại.