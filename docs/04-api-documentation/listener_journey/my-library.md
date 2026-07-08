# Nhóm Tính năng: Thư viện Cá nhân & Thông báo (My Library)

**Mô tả:** Tài liệu định nghĩa các API vận hành Thư viện cá nhân của Listener: Quản lý Yêu thích (Tim), Lịch sử nghe nhạc, Theo dõi (Follow) Nghệ sĩ, Tự tạo Playlist cá nhân (CRUD) và Hệ thống Thông báo (In-app).

**Liên kết giao diện:** 
- [SCR-LST-05 Thư viện & Lịch sử](../../screens/Listener/SCR-LST-05-library.md)

---

## 🔄 Tóm tắt luồng gọi API (Workflow)

1. **Quản lý Yêu thích:** Hệ thống tách riêng 4 API GET để lấy danh sách Bài hát, Album, Nghệ sĩ và Playlist đã thả tim.
2. **Quản lý Playlist Cá nhân:** 
   - Listener bấm "Tạo mới" -> `[API-141]`.
   - Listener mở Playlist cá nhân ra xem -> Gọi `[API-142]`.
   - Vào bài hát, bấm "Thêm vào Playlist" -> Gọi `[API-145]`.
   - Kéo thả thứ tự bài hát trong Playlist -> Gọi `[API-147]`.

---

## 💻 Chi tiết các APIs - Yêu thích (Favorites) & Follow

### 1. [API-120] Lấy danh sách Bài hát Yêu thích
**Tính năng:** Trả về danh sách bài hát đã thả tim, sắp xếp theo thời gian mới nhất. Phân trang 20 items.
* **Endpoint:** `GET /api/v1/listener/library/favorites/songs`
* **Auth:** Bearer Token (Listener) 

### 2. [API-121, 122, 123] Danh sách Album, Nghệ sĩ, Playlist Yêu thích
*Tương tự API-120, chỉ khác Endpoint:*
* **[API-121]:** `GET /api/v1/listener/library/favorites/albums`
* **[API-122]:** `GET /api/v1/listener/library/favorites/artists`
* **[API-123]:** `GET /api/v1/listener/library/favorites/playlists`

### 3. [API-130] Theo dõi / Hủy theo dõi (Toggle Follow Artist)
**Tính năng:** Bấm nút Follow ở trang Artist Profile. Nếu đang Follow thì thành Unfollow.
* **Endpoint:** `POST /api/v1/listener/artists/{id}/follow`
* **Auth:** Bearer Token (Listener)

### 4. [API-131] Danh sách Đang theo dõi (Following)
**Tính năng:** Liệt kê các Nghệ sĩ mà user đang Follow.
* **Endpoint:** `GET /api/v1/listener/following`
* **Auth:** Bearer Token (Listener)

---

## 💻 Chi tiết các APIs - Lịch sử nghe nhạc (History)

### 5. [API-125] Lấy Lịch sử nghe nhạc
**Tính năng:** Lấy danh sách 50 bài hát được nghe gần đây nhất. Dữ liệu này được insert khi gọi API `[API-111] Anti-cheat`.
* **Endpoint:** `GET /api/v1/listener/library/history`
* **Auth:** Bearer Token (Listener)

### 6. [API-126] Xóa một bài khỏi Lịch sử
**Tính năng:** Xóa bài hát nhạy cảm khỏi lịch sử nghe.
* **Endpoint:** `DELETE /api/v1/listener/library/history/{id}`
* **Auth:** Bearer Token (Listener)

### 7. [API-127] Xóa toàn bộ Lịch sử
**Tính năng:** Dọn sạch toàn bộ History.
* **Endpoint:** `DELETE /api/v1/listener/library/history`
* **Auth:** Bearer Token (Listener)

---

## 💻 Chi tiết các APIs - User Playlists (CRUD)

### 8. [API-140] Lấy danh sách Playlist cá nhân
**Tính năng:** Lấy các Playlist do user tự tạo (Ví dụ: Nhạc tập Gym).
* **Endpoint:** `GET /api/v1/listener/playlists`
* **Auth:** Bearer Token (Listener)

### 9. [API-141] Tạo mới Playlist
**Tính năng:** Khởi tạo một Playlist rỗng.
* **Endpoint:** `POST /api/v1/listener/playlists`
* **Auth:** Bearer Token (Listener)

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| title | string | required, max:255 | Tên Playlist |
| is_public | boolean | nullable, default:true | Hiển thị công khai hay ẩn |

### 10. [API-142] Chi tiết Playlist Cá nhân
**Tính năng:** Trả về Metadata và mảng chứa các bài hát bên trong Playlist.
* **Endpoint:** `GET /api/v1/listener/playlists/{id}`
* **Auth:** Bearer Token (Listener)

### 11. [API-143] Cập nhật Metadata Playlist
**Tính năng:** Đổi tên, đổi trạng thái Public/Private.
* **Endpoint:** `PUT /api/v1/listener/playlists/{id}`
* **Auth:** Bearer Token (Listener)

### 12. [API-144] Xóa Playlist (Soft Delete)
**Tính năng:** Xóa Playlist cá nhân.
* **Endpoint:** `DELETE /api/v1/listener/playlists/{id}`
* **Auth:** Bearer Token (Listener)

### 13. [API-145] Thêm bài hát vào Playlist
**Tính năng:** Từ màn hình đang phát nhạc, bấm "Add to Playlist" -> chọn Playlist để nhét bài này vào.
* **Endpoint:** `POST /api/v1/listener/playlists/{id}/songs`
* **Auth:** Bearer Token (Listener)

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| song_id | integer | required | ID bài hát cần thêm |

### 14. [API-146] Xóa bài hát khỏi Playlist
**Tính năng:** Vuốt sang trái để xóa 1 bài khỏi Playlist cá nhân.
* **Endpoint:** `DELETE /api/v1/listener/playlists/{id}/songs/{song_id}`
* **Auth:** Bearer Token (Listener)

### 15. [API-147] Thay đổi thứ tự (Reorder Songs)
**Tính năng:** Kéo thả bài hát để đảo vị trí lên xuống.
* **Endpoint:** `PATCH /api/v1/listener/playlists/{id}/songs`
* **Auth:** Bearer Token (Listener)

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| song_ids | array | required, array, max:100 | Mảng chứa danh sách ID bài hát theo thứ tự mới |
| song_ids.* | integer | required, distinct, exists:songs,id | Từng ID phải tồn tại và không trùng lặp |

---

## 💻 Chi tiết các APIs - Thông báo (Notifications)

### 16. [API-160] Lấy danh sách Thông báo (In-app)
**Tính năng:** Trả về danh sách thông báo (Có bài hát mới từ nghệ sĩ bạn theo dõi, Playlist của bạn được thả tim...).
* **Endpoint:** `GET /api/v1/listener/notifications`
* **Auth:** Bearer Token (Listener)

### 17. [API-161] Đánh dấu Đã đọc 1 mục
**Tính năng:** Khi user bấm vào xem thông báo.
* **Endpoint:** `PUT /api/v1/listener/notifications/{id}/read`
* **Auth:** Bearer Token (Listener)

### 18. [API-162] Đánh dấu Đã đọc Tất cả
**Tính năng:** Bấm nút "Đánh dấu tất cả đã đọc" ở góc trên màn hình.
* **Endpoint:** `PUT /api/v1/listener/notifications/read-all`
* **Auth:** Bearer Token (Listener)
