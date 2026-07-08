# Nhóm Tính năng: Quản lý Kho nhạc & Album (Album & Song Manager)

**Mô tả:** Sau khi Upload thành công (`API-221` ở module Media Upload), các bài hát sẽ rơi vào kho nhạc của Artist. Tại đây, Artist có thể phân nhóm bài hát vào các Album, hoặc chỉnh sửa thông tin bài hát.

**Liên kết giao diện:** 
- [SCR-ART-04 Quản lý Album & Danh sách bài hát](../../screens/Artist/SCR-ART-04-album-manager.md)

---

## 🔄 Tóm tắt luồng gọi API (Workflow)

1. **Quản lý Bài hát lẻ:** Artist dùng `[API-220]` để liệt kê mọi bài hát của mình. Có thể sửa tên/metadata bằng `[API-222]` hoặc Xóa bằng `[API-223]`.
2. **Quản lý Album:** 
   - Lấy danh sách Album -> `[API-230]`.
   - Tạo Album rỗng -> `[API-231]`.
3. **Phân bổ Nhạc vào Album:** 
   - Gọi `[API-226]` để lấy danh sách "Những bài hát đang không thuộc Album nào" (Unassigned).
   - Edit Album `[API-232]` và truyền mảng các ID bài hát Unassigned vào để đính chúng vào Album.

---

## 💻 Chi tiết các APIs - Quản lý Bài hát (Songs)

### 1. [API-220] Lấy kho bài hát của Artist
**Tính năng:** Trả về tất cả bài hát do Artist tải lên, bao gồm cả các bài Đang chờ duyệt (Pending), Đã duyệt (Approved) hoặc Lỗi (Failed).
* **Endpoint:** `GET /api/v1/artist/songs`
* **Auth:** Bearer Token (Artist)

### 2. [API-222] Cập nhật Metadata Bài hát
**Tính năng:** Chỉnh sửa Tên bài, Thể loại, Lời bài hát. 
*Lưu ý: Không cho sửa File MP3. Nếu muốn đổi file MP3, bắt buộc tạo bài hát mới.*
* **Endpoint:** `PUT /api/v1/artist/songs/{id}`
* **Auth:** Bearer Token (Artist)

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| title | string | required, max:255 | Tên bài hát |
| genre_id | integer | required, exists:genres,id | Thể loại |
| lyrics | string | nullable | Lời bài hát |

### 3. [API-223] Xóa Bài hát
**Tính năng:** Soft delete bài hát. Nếu xóa, bài hát sẽ biến mất khỏi hệ thống của Listener nhưng vẫn nằm trong Database Admin để đối soát.
* **Endpoint:** `DELETE /api/v1/artist/songs/{id}`
* **Auth:** Bearer Token (Artist)

### 4. [API-226] Lấy danh sách Bài hát chưa gắn Album
**Tính năng:** Trả về các bài hát có `album_id IS NULL`. Dùng để đổ vào dropdown khi Artist muốn add bài hát vào một Album.
* **Endpoint:** `GET /api/v1/artist/songs/unassigned`
* **Auth:** Bearer Token (Artist)

---

## 💻 Chi tiết các APIs - Quản lý Album (Albums)

### 5. [API-230] Lấy danh sách Album
**Tính năng:** Trả về các Album của Artist kèm theo đếm số lượng bài hát bên trong (`songs_count`).
* **Endpoint:** `GET /api/v1/artist/albums`
* **Auth:** Bearer Token (Artist)

### 6. [API-231] Tạo Album mới
**Tính năng:** Tạo một Album. Hỗ trợ truyền mảng `song_ids` để gom luôn nhạc vào Album ngay lúc tạo.
* **Endpoint:** `POST /api/v1/artist/albums`
* **Auth:** Bearer Token (Artist)
* **Content-Type:** `multipart/form-data` (Do có upload ảnh bìa Album)

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| title | string | required, max:255 | Tên Album |
| release_date | date | required | Ngày phát hành |
| cover_image | file | required, image | Ảnh bìa Album |
| song_ids | array | nullable | Mảng ID các bài hát muốn add luôn vào |

### 7. [API-232] Cập nhật Album
**Tính năng:** Sửa thông tin Album. Nếu có truyền `song_ids`, Backend sẽ gỡ các bài hát cũ và thay bằng danh sách mới.
* **Endpoint:** `PUT /api/v1/artist/albums/{id}`
* **Auth:** Bearer Token (Artist)

### 8. [API-233] Xóa Album
**Tính năng:** Soft delete Album. 
*Lưu ý Business Logic: Khi xóa Album, các bài hát bên trong nó KHÔNG BỊ XÓA. Thuộc tính `album_id` của các bài hát đó sẽ bị reset về NULL (Chuyển thành Unassigned).*
* **Endpoint:** `DELETE /api/v1/artist/albums/{id}`
* **Auth:** Bearer Token (Artist)
