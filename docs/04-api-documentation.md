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

> **Lưu ý:** Toàn bộ API ID tuân theo hệ thống thống nhất trong [07-api-registry.md](./07-api-registry.md). Mọi thay đổi API phải cập nhật Registry trước.

### 3.1 Guest API (Không yêu cầu Auth)

Các API dành cho người dùng chưa đăng nhập (Khách vãng lai). Prefix: `/api/v1/guest`

#### 3.1.1 Authentication

| Endpoint | Method | Description |
|---|---|---|
| `/guest/auth/login` | POST | [API-002] Đăng nhập và nhận Session Cookie |
| `/guest/auth/register` | POST | [API-003] Đăng ký tài khoản Listener mới |
| `/guest/auth/forgot-password` | POST | [API-004] Gửi email reset link |
| `/guest/auth/reset-password` | POST | [API-005] Đặt mật khẩu mới (token + new password) |
| `/guest/auth/email/verify/{id}/{hash}` | GET | [API-006] Xác thực email (Signed URL) |
| `/guest/auth/artist-register/validate` | GET | [API-007] Validate invitation token |
| `/guest/auth/artist-register` | POST | [API-008] Đăng ký tài khoản Artist (Invite-Only) |
| `/guest/auth/redirect/{provider}` | GET | [API-009] OAuth2 redirect (Google/Facebook) |
| `/guest/auth/callback/{provider}` | GET | [API-010] OAuth2 callback handler |

#### 3.1.2 Content (Public)

| Endpoint | Method | Description |
|---|---|---|
| `/guest/explore/banners` | GET | [API-011] Banner trang chủ (Cached) |
| `/guest/explore/trending` | GET | [API-012] Bài hát thịnh hành |
| `/guest/explore/new-releases` | GET | [API-013] Nhạc mới phát hành |
| `/guest/genres` | GET | [API-014] Danh sách thể loại (Cached) |
| `/guest/search` | GET | [API-015] Tìm kiếm tổng hợp (Song, Artist, Album, Playlist) |
| `/guest/songs/{id}` | GET | [API-016] Chi tiết bài hát (metadata) |
| `/guest/songs/{id}/preview` | GET | [API-017] Preview 30s (Signed URL, không cần auth) |
| `/guest/albums/{id}` | GET | [API-018] Chi tiết Album (songs list, metadata) |
| `/guest/artists/{id}` | GET | [API-019] Artist Profile public (bio, stats) |
| `/guest/artists/{id}/top-tracks` | GET | [API-020] Top tracks của artist |
| `/guest/artists/{id}/albums` | GET | [API-021] Albums của artist |
| `/guest/playlists/{id}` | GET | [API-022] Chi tiết Playlist public |

---

### 3.2 Listener API (Role: Listener)

Các API dành riêng cho người dùng đã đăng nhập với vai trò Listener. Prefix: `/api/v1/listener`
*Yêu cầu: Client đính kèm HttpOnly Cookie hợp lệ.*

#### 3.2.1 Authentication & Profile

| Endpoint | Method | Description |
|---|---|---|
| `/listener/auth/me` | GET | [API-101] Thông tin tài khoản cá nhân |
| `/listener/auth/logout` | POST | [API-102] Đăng xuất |
| `/listener/auth/email/resend` | POST | [API-103] Gửi lại email xác thực |
| `/listener/auth/password` | PUT | [API-104] Đổi mật khẩu |
| `/listener/auth/sessions` | GET | [API-105] Danh sách sessions đang hoạt động |
| `/listener/auth/sessions/{id}` | DELETE | [API-106] Revoke session |
| `/listener/profile` | PUT | [API-107] Cập nhật profile (name, avatar) |

#### 3.2.2 Streaming

| Endpoint | Method | Description |
|---|---|---|
| `/listener/explore/recommendations` | GET | [API-108] Gợi ý cá nhân hóa |
| `/listener/stream/url/{id}` | GET | [API-110] Sinh Signed URL nghe nhạc (HLS m3u8) |
| `/listener/stream/track/{id}` | POST | [API-111] Ghi nhận lượt nghe hợp lệ (Anti-cheat) |

**Cơ chế Track Stream (Listener):**
1. Gọi `GET /listener/stream/url/{id}` [API-110] để lấy `audio_url` (m3u8).
2. Khi phát qua 30s, gọi `POST /listener/stream/track/{id}` [API-111] kèm payload:
  ```json
  {
    "session_id": "uuid-v4-frontend",
    "duration_listened": 35
  }
  ```

#### 3.2.3 Library & Favorites

| Endpoint | Method | Description |
|---|---|---|
| `/listener/library/favorites/songs` | GET | [API-120] DS bài hát yêu thích (paginated) |
| `/listener/library/favorites/albums` | GET | [API-121] DS album yêu thích |
| `/listener/library/favorites/artists` | GET | [API-122] DS artist yêu thích |
| `/listener/library/favorites/playlists` | GET | [API-123] DS playlist yêu thích |
| `/listener/library/toggle-favorite` | POST | [API-124] Toggle favorite (type + id trong payload) |
| `/listener/library/history` | GET | [API-125] Lịch sử nghe nhạc (Resume listening) |
| `/listener/library/history/{id}` | DELETE | [API-126] Xóa một mục lịch sử |
| `/listener/library/history` | DELETE | [API-127] Xóa toàn bộ lịch sử |

#### 3.2.4 Follow

| Endpoint | Method | Description |
|---|---|---|
| `/listener/artists/{id}/follow` | POST | [API-130] Toggle Follow/Unfollow artist |
| `/listener/following` | GET | [API-131] DS artist đang follow |

#### 3.2.5 Playlist Management

| Endpoint | Method | Description |
|---|---|---|
| `/listener/playlists` | GET | [API-140] DS playlist cá nhân |
| `/listener/playlists` | POST | [API-141] Tạo playlist mới |
| `/listener/playlists/{id}` | GET | [API-142] Chi tiết playlist |
| `/listener/playlists/{id}` | PUT | [API-143] Cập nhật playlist (title, privacy, cover) |
| `/listener/playlists/{id}` | DELETE | [API-144] Xóa playlist (soft delete) |
| `/listener/playlists/{id}/songs` | POST | [API-145] Thêm bài vào playlist |
| `/listener/playlists/{id}/songs` | DELETE | [API-146] Xóa bài khỏi playlist |
| `/listener/playlists/{id}/reorder` | PUT | [API-147] Sắp xếp lại vị trí bài hát |

#### 3.2.6 Comments

| Endpoint | Method | Description |
|---|---|---|
| `/listener/songs/{id}/comments` | GET | [API-150] DS comments (paginated, threaded) |
| `/listener/songs/{id}/comments` | POST | [API-151] Tạo comment/reply |
| `/listener/comments/{id}` | DELETE | [API-152] Xóa comment của chính mình |

#### 3.2.7 Notifications

| Endpoint | Method | Description |
|---|---|---|
| `/listener/notifications` | GET | [API-160] DS thông báo (paginated) |
| `/listener/notifications/{id}/read` | PUT | [API-161] Đánh dấu đã đọc |
| `/listener/notifications/read-all` | PUT | [API-162] Đánh dấu tất cả đã đọc |

#### 3.2.8 Search History

| Endpoint | Method | Description |
|---|---|---|
| `/listener/search/recent` | GET | [API-170] Lịch sử tìm kiếm (max 20) |
| `/listener/search/recent/{id}` | DELETE | [API-171] Xóa một từ khóa |
| `/listener/search/recent` | DELETE | [API-172] Xóa toàn bộ lịch sử tìm |

---

### 3.3 Artist API (Role: Artist)

Các API dành riêng cho không gian làm việc của Nghệ sĩ (Artist Workspace). Prefix: `/api/v1/artist`
*Yêu cầu: Client đính kèm HttpOnly Cookie hợp lệ và User có Role = `artist`.*

#### 3.3.1 Authentication

| Endpoint | Method | Description |
|---|---|---|
| `/artist/auth/me` | GET | [API-201] Thông tin Artist Profile |
| `/artist/auth/logout` | POST | [API-202] Đăng xuất |
| `/artist/auth/password` | PUT | [API-203] Đổi mật khẩu |

#### 3.3.2 Profile Management

| Endpoint | Method | Description |
|---|---|---|
| `/artist/profile` | GET | [API-210] Lấy profile hiện tại |
| `/artist/profile` | PUT | [API-211] Cập nhật profile (stage_name, bio, social links) |
| `/artist/profile/avatar` | POST | [API-212] Upload avatar |
| `/artist/profile/cover` | POST | [API-213] Upload cover image |

#### 3.3.3 Song Management

| Endpoint | Method | Description |
|---|---|---|
| `/artist/songs` | GET | [API-220] DS bài hát của artist |
| `/artist/songs` | POST | [API-221] Upload bài hát mới (`multipart/form-data`) |
| `/artist/songs/{id}` | PUT | [API-222] Cập nhật metadata bài hát |
| `/artist/songs/{id}` | DELETE | [API-223] Xóa mềm bài hát |
| `/artist/songs/{id}/status` | GET | [API-224] Trạng thái processing FFmpeg |
| `/artist/songs/{id}/retry` | POST | [API-225] Retry bài hát Failed |
| `/artist/songs/unassigned` | GET | [API-226] Bài hát chưa thuộc album |

#### 3.3.4 Album Management

| Endpoint | Method | Description |
|---|---|---|
| `/artist/albums` | GET | [API-230] DS album |
| `/artist/albums` | POST | [API-231] Tạo album mới |
| `/artist/albums/{id}` | PUT | [API-232] Cập nhật album |
| `/artist/albums/{id}` | DELETE | [API-233] Xóa album (soft delete) |

#### 3.3.5 Analytics & Notifications

| Endpoint | Method | Description |
|---|---|---|
| `/artist/analytics` | GET | [API-240] Dashboard thống kê (Tổng stream, follower, biểu đồ) |
| `/artist/notifications` | GET | [API-250] DS thông báo |
| `/artist/notifications/{id}/read` | PUT | [API-251] Đánh dấu đã đọc |
| `/artist/notifications/read-all` | PUT | [API-252] Đánh dấu tất cả đã đọc |
| `/artist/genres/tree` | GET | [API-260] Cây thể loại cho dropdown upload |

**Luồng Upload (POST `/artist/songs` [API-221]):**
- Frontend gửi file audio + metadata + cover image.
- Backend validate → lưu MinIO → tạo record `[DB-songs]` status=`Processing` → dispatch Job FFmpeg vào Queue `media_processing`.
- Trả về HTTP 201 Created. Artist có thể poll [API-224] để theo dõi tiến trình.
- Khi FFmpeg hoàn tất: status chuyển `Pending`. Nếu thất bại: status chuyển `Failed`, Artist dùng [API-225] để retry (tối đa 3 lần, xem `[RULE-017]`).

---

### 3.4 Admin API (Role: Admin)

Các API phục vụ quản trị hệ thống. Prefix: `/api/v1/admin`
*Phân quyền chi tiết qua Spatie RBAC (xem `[FEAT-013]`, `[FEAT-014]`).*

#### 3.4.1 Authentication

| Endpoint | Method | Description |
|---|---|---|
| `/admin/auth/login` | POST | [API-301] Đăng nhập Admin |
| `/admin/auth/logout` | POST | [API-302] Đăng xuất Admin |
| `/admin/auth/password` | PUT | [API-303] Đổi mật khẩu |

#### 3.4.2 Dashboard & Analytics

| Endpoint | Method | Description |
|---|---|---|
| `/admin/dashboard` | GET | [API-310] Thống kê tổng quan (widgets) |
| `/admin/analytics/streams` | GET | [API-311] Biểu đồ streams theo ngày |
| `/admin/analytics/top-songs` | GET | [API-312] Top songs |
| `/admin/analytics/user-growth` | GET | [API-313] User growth chart |
| `/admin/analytics/artists/{id}` | GET | [API-314] Chi tiết analytics một artist |

#### 3.4.3 User Management

| Endpoint | Method | Description |
|---|---|---|
| `/admin/users` | GET | [API-320] DS users (filter: role, search, status) |
| `/admin/users/artist` | POST | [API-321] Tạo nhanh tài khoản Artist |
| `/admin/users/{id}/status` | PUT | [API-322] Ban / Unban / Suspend user |
| `/admin/users/{id}/reset-password` | POST | [API-323] Gửi email reset cho user |
| `/admin/users/{id}` | DELETE | [API-324] Xóa staff account |
| `/admin/users/{id}/roles` | PUT | [API-325] Gán role cho admin user |

#### 3.4.4 Artist Management

| Endpoint | Method | Description |
|---|---|---|
| `/admin/artists` | GET | [API-330] DS toàn bộ artist |
| `/admin/artists/{id}` | PUT | [API-331] Chỉnh sửa thông tin artist |
| `/admin/artists/{id}/verify` | POST | [API-332] Xác minh artist (tích xanh) |

#### 3.4.5 Artist Invitations

| Endpoint | Method | Description |
|---|---|---|
| `/admin/artist-invitations` | GET | [API-340] DS invitations |
| `/admin/artist-invitations` | POST | [API-341] Tạo invitation mới + gửi email |
| `/admin/artist-invitations/{id}/revoke` | PUT | [API-342] Thu hồi invitation |

#### 3.4.6 Song Moderation

| Endpoint | Method | Description |
|---|---|---|
| `/admin/moderation/songs` | GET | [API-350] DS bài hát chờ duyệt (status=Pending) |
| `/admin/moderation/songs/{id}` | GET | [API-351] Chi tiết bài hát để review |
| `/admin/moderation/songs/{id}/approve` | PUT | [API-352] Phê duyệt bài hát |
| `/admin/moderation/songs/{id}/reject` | PUT | [API-353] Từ chối (payload: `rejected_reason`) |

#### 3.4.7 Song & Album Inventory

| Endpoint | Method | Description |
|---|---|---|
| `/admin/inventory/songs` | GET | [API-360] DS tổng bài hát. `?status=approved&genre_id=x&artist_id=y&sort=play_count&order=desc&date_from=Y-m-d&date_to=Y-m-d` |
| `/admin/inventory/songs` | POST | [API-361] Admin upload nhạc (auto-approved sau FFmpeg) |
| `/admin/inventory/songs/{id}` | PUT | [API-362] Sửa metadata bài hát |
| `/admin/inventory/songs/{id}` | DELETE | [API-363] Soft delete / ẩn bài hát |
| `/admin/inventory/albums` | GET | [API-364] DS album |
| `/admin/inventory/albums` | POST | [API-365] Tạo album |
| `/admin/inventory/albums/{id}` | PUT | [API-366] Sửa album |
| `/admin/search/artists` | GET | [API-367] Autocomplete tìm artist |
| `/admin/search/songs` | GET | [API-368] Autocomplete tìm songs |

#### 3.4.8 Genre Management

| Endpoint | Method | Description |
|---|---|---|
| `/admin/genres` | GET | [API-370] DS thể loại (tree) |
| `/admin/genres` | POST | [API-371] Tạo thể loại |
| `/admin/genres/{id}` | PUT | [API-372] Cập nhật thể loại |

#### 3.4.9 Banner Management

| Endpoint | Method | Description |
|---|---|---|
| `/admin/banners` | GET | [API-380] DS banners |
| `/admin/banners` | POST | [API-381] Tạo banner (multipart) |
| `/admin/banners/{id}` | PUT | [API-382] Cập nhật banner |
| `/admin/banners/reorder` | PUT | [API-383] Sắp xếp lại thứ tự |

#### 3.4.10 System Playlists

| Endpoint | Method | Description |
|---|---|---|
| `/admin/playlists` | GET | [API-390] DS system playlists |
| `/admin/playlists` | POST | [API-391] Tạo system playlist |
| `/admin/playlists/{id}` | PUT | [API-392] Cập nhật playlist |
| `/admin/playlists/{id}` | DELETE | [API-393] Xóa playlist (soft) |
| `/admin/playlists/{id}/songs` | POST | [API-394] Thêm bài vào playlist |
| `/admin/playlists/{id}/songs` | DELETE | [API-395] Xóa bài khỏi playlist |

#### 3.4.11 RBAC (Roles & Permissions)

| Endpoint | Method | Description |
|---|---|---|
| `/admin/roles` | GET | [API-400] DS roles |
| `/admin/roles` | POST | [API-401] Tạo role mới |
| `/admin/roles/{id}` | PUT | [API-402] Cập nhật permissions của role |
| `/admin/roles/{id}` | DELETE | [API-403] Xóa role (nếu không có user đang giữ) |
| `/admin/permissions` | GET | [API-404] DS tất cả permissions |

#### 3.4.12 Audit & Moderation

| Endpoint | Method | Description |
|---|---|---|
| `/admin/audit-logs` | GET | [API-410] Nhật ký hệ thống (filter: date, action, admin) |
| `/admin/comments/{id}` | DELETE | [API-420] Xóa comment vi phạm |

---

## 4. Rate Limiting

Để bảo vệ hệ thống khỏi DDOS và gian lận, các API được áp dụng Rate Limit (Sử dụng Redis):

- **Global API:** 60 requests / 1 phút / IP.
- **Auth API (Login/Register/Forgot):** 5 requests / 1 phút / IP.
- **Upload API (`POST /artist/songs` [API-221]):** 10 requests / 10 phút / User.
- **Track Stream API (`POST /listener/stream/track/{id}` [API-111]):** 5 requests / 1 phút / User (Ngăn chặn dùng script bắn request cày view liên tục).
- **Email Resend ([API-103]):** 1 request / 1 phút / User.
- Khi vượt quá ngưỡng, hệ thống trả về mã lỗi HTTP `429 Too Many Requests`.

---

## 5. Chi tiết [API-111] Ghi nhận lượt nghe

### 5.1 Endpoint & Method
- **Method**: `POST`
- **Endpoint**: `/api/v1/listener/stream/track/{id}`
- **Auth**: Cookie Sanctum (Role: `Listener`, `Artist`)

### 5.2 Request Payload (Body)

| Field | Type | Required | Rule Validation |
|-------|------|----------|-----------------|
| `session_id` | string | Yes | UUID v4 |
| `duration_listened`| integer | Yes | >= 30 |

### 5.3 Logic xử lý
- Validate payload.
- Gọi `[RULE-STREAM-01]` để check điều kiện Anti-cheat.
- Insert record vào `[DB-streams]`.
- Increment `[DB-songs].play_count` atomically.

### 5.4 Response Schema
**201 Created**
```json
{
  "status": "success",
  "message": "Stream recorded"
}
```

### 5.5 Exceptions
- `429 Too Many Requests`: Vi phạm Rule Anti-cheat (Rate limit).
- `404 Not Found`: Bài hát không tồn tại.
- `403 Forbidden`: Bài hát không ở trạng thái `Approved`.

