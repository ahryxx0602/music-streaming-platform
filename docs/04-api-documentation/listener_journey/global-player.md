# Nhóm Tính năng: Global Audio Player (Trình phát nhạc cốt lõi)

**Mô tả:** Tài liệu định nghĩa các API dành riêng cho Component Global Audio Player - trái tim của hệ thống Streaming. Xử lý bảo mật luồng phát, chống gian lận (Anti-cheat) và ghi nhớ tọa độ nghe nhạc.

**Liên kết giao diện:** 
- [SCR-LST-04 Global Audio Player](../../screens/Listener/SCR-LST-04-global-player.md)

---

## 🔄 Tóm tắt luồng gọi API (Workflow)

1. **Bắt đầu phát:** (Guest) Gọi `[API-017]` để nghe thử 30s. (Listener) Gọi `[API-110]` để lấy Signed URL bản Full và `stream_token`.
2. **Resume Position:** Quá trình nghe, Player gọi ngầm `[API-128]` (mỗi 10s) để lưu vị trí giây đang phát (VD: đang nghe đến giây thứ 45).
3. **Anti-Cheat (Lượt nghe):** Khi Listener nghe vượt mốc 30s liên tục, gọi `[API-111]` đính kèm `stream_token` để Server cộng +1 View và tính tiền bản quyền cho Artist.
4. **Tương tác:** Bấm Trái tim -> Gọi `[API-124]` để thêm/xóa bài hát khỏi Thư viện.

---

## 💻 Chi tiết các APIs

### 1. [API-017] Lấy URL nghe thử 30s (Preview)
**Tính năng:** Trả về file audio chất lượng thấp (128kbps) đã bị cắt 30s dành cho người dùng Guest (chưa đăng nhập).
* **Endpoint:** `GET /api/v1/guest/songs/{id}/preview`
* **Auth:** Không yêu cầu

**Success Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "preview_url": "https://minio.../preview/song_105.mp3"
  }
}
```

---

### 2. [API-110] Lấy URL Stream Full & Mã Token (HLS)
**Tính năng:** Trả về link HLS (master.m3u8) có gắn Signature của MinIO (hết hạn sau 15 phút) để bảo vệ file nhạc. Đồng thời sinh mã `stream_token` để chuẩn bị cho bước Anti-cheat.
* **Endpoint:** `GET /api/v1/listener/stream/url/{id}`
* **Auth:** Bearer Token (Listener)

**Success Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "hls_url": "https://minio.../streams/105/master.m3u8?signature=...",
    "stream_token": "eyJhbGciOiJIUzI1NiIsInR..."
  }
}
```

---

### 3. [API-128] Đồng bộ Tọa độ Phát (Sync Resume Position)
**Tính năng:** Lưu lại vị trí (giây) đang phát để lần sau User mở app lên có thể nghe tiếp.
* **Endpoint:** `PUT /api/v1/listener/library/history/sync`
* **Auth:** Bearer Token (Listener)
* **Lưu ý FE:** Cần Debounce mỗi 10 giây gọi 1 lần để tránh Spam Server.

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| song_id | integer | required | ID bài hát đang phát |
| current_position | integer | required, min:0 | Vị trí hiện tại tính bằng giây |

**Success Response (204 No Content):**
*(Tối ưu băng thông, Backend không trả về Body)*

---

### 4. [API-111] Xác nhận Lượt nghe (Anti-Cheat Validation)
**Tính năng:** Ghi nhận 1 Lượt nghe hợp lệ. Trừ tiền tài khoản (nếu có Premium) và cộng Royalties cho Artist. Bắt buộc có token xác thực.
* **Endpoint:** `POST /api/v1/listener/stream/track/{id}`
* **Auth:** Bearer Token (Listener)
* **Headers:** `X-Stream-Token: <giá trị lấy từ API-110>`

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| duration_listened | integer | required, min:30 | Số giây thực tế đã nghe |
| device | string | required | "desktop", "mobile", "web" |
| session_id | string | required | ID phiên đăng nhập hiện tại |

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Stream recorded successfully"
}
```

**Error Response (429 Too Many Requests):**
*Lưu ý: Không chỉ tin tưởng trường `duration_listened` do Client gửi lên. Backend cần đối chiếu `created_at` của `stream_token` (API-110) với thời điểm API-111 được gọi. Nếu khoảng cách < 30 giây -> Throw 429 Too Many Requests (Khóa 24h vì phát hiện dùng Tool).*
```json
{
  "status": "error",
  "message": "Anti-cheat validation failed."
}
```

---

### 5. [API-124] Bật/Tắt Yêu thích (Toggle Favorite)
**Tính năng:** Thêm hoặc Xóa bài hát khỏi thư viện (Tim). Có thể dùng chung cho Song, Album, Artist, Playlist.
* **Endpoint:** `POST /api/v1/listener/library/toggle-favorite`
* **Auth:** Bearer Token (Listener)

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| entity_type | string | required | in: song, album, artist, playlist |
| entity_id | integer | required | ID của đối tượng |

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Đã thêm vào thư viện",
  "data": {
    "is_favorited": true
  }
}
```
