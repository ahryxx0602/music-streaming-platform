# Nhóm Tính năng: Chi tiết Bộ sưu tập & Bình luận (Collection Detail)

**Mô tả:** Tài liệu định nghĩa các APIs dùng để tải dữ liệu chi tiết của 4 Entity chính: Bài hát, Album, Nghệ sĩ, Playlist. Đồng thời chứa hệ thống Bình luận (Comments) của Listener trên từng bài hát. Các API Get chi tiết đa phần mở public cho Guest.

**Liên kết giao diện:** 
- [SCR-LST-03 Chi tiết Bài hát / Album / Playlist](../../screens/Listener/SCR-LST-03-detail-view.md)
- [SCR-LST-07 Hồ sơ Nghệ sĩ](../../screens/Listener/SCR-LST-07-artist-profile.md)

---

## 🔄 Tóm tắt luồng gọi API (Workflow)

1. **Vào trang Chi tiết Bài hát:** 
   - Load Metadata bài hát -> `[API-016]`.
   - Load danh sách Bình luận (Phân trang) -> `[API-150]`.
   - Khi Listener gửi bình luận -> `[API-151]`.
2. **Vào trang Hồ sơ Nghệ sĩ:**
   - Load Bio & Thông số -> `[API-019]`.
   - Load Top Bài hát thịnh hành -> `[API-020]`.
   - Load Danh sách Album -> `[API-021]`.

---

## 💻 Chi tiết các APIs - Lấy Dữ liệu (Guest & Listener)

### 1. [API-016] Chi tiết Bài hát (Song Detail)
**Tính năng:** Lấy thông tin Metadata (Tên, Ảnh bìa, Lời bài hát, Lượt nghe) của một bài hát.
* **Endpoint:** `GET /api/v1/guest/songs/{id}`
* **Auth:** Không yêu cầu

**Success Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "id": 105,
    "title": "Chạy Ngay Đi",
    "artist": { "id": 1, "stage_name": "Sơn Tùng M-TP" },
    "lyrics": "[Lời bài hát...]",
    "play_count": 1500000,
    "release_date": "2018-05-12",
    "is_favorited": false 
  }
}
```
*(Ghi chú: Nếu có Token của Listener gửi kèm, Backend tự động check xem bài này đã được thả tim chưa và gán vào `is_favorited`).*

### 2. [API-018] Chi tiết Album
**Tính năng:** Trả về Metadata của Album kèm theo mảng chứa toàn bộ các bài hát bên trong.
* **Endpoint:** `GET /api/v1/guest/albums/{id}`
* **Auth:** Không yêu cầu

### 3. [API-022] Chi tiết Playlist
**Tính năng:** Trả về Metadata của Playlist (cả System Playlist và User Playlist public) kèm danh sách bài hát.
* **Endpoint:** `GET /api/v1/guest/playlists/{id}`
* **Auth:** Không yêu cầu

---

## 💻 Chi tiết các APIs - Hồ sơ Nghệ sĩ (Artist Profile)

### 4. [API-019] Hồ sơ Public Nghệ sĩ
**Tính năng:** Lấy thông tin tiểu sử, ảnh Cover, Avatar và tổng lượt Follow.
* **Endpoint:** `GET /api/v1/guest/artists/{id}`
* **Auth:** Không yêu cầu

### 5. [API-020] Top Bài hát của Nghệ sĩ
**Tính năng:** Lấy 5 bài hát có lượt nghe cao nhất mọi thời đại của Nghệ sĩ này.
* **Endpoint:** `GET /api/v1/guest/artists/{id}/top-tracks`
* **Auth:** Không yêu cầu

### 6. [API-021] Danh sách Album của Nghệ sĩ
**Tính năng:** Lấy tất cả Album đã phát hành của Nghệ sĩ. Phân trang 10 item/page.
* **Endpoint:** `GET /api/v1/guest/artists/{id}/albums`
* **Auth:** Không yêu cầu

---

## 💻 Chi tiết các APIs - Hệ thống Bình luận (Comments)

### 7. [API-150] Lấy danh sách Bình luận
**Tính năng:** Tải danh sách bình luận của một bài hát. Hỗ trợ cấu trúc Threaded (Bình luận cha - con) sâu 1 cấp. Phân trang vô tận (Infinite Scroll).
* **Endpoint:** `GET /api/v1/listener/songs/{id}/comments`
* **Auth:** Bearer Token (Listener) - Guest không được xem bình luận.

**Query Params:**
| Field | Type | Rules | Description |
|---|---|---|---|
| page | integer | nullable | Số trang hiện tại |

**Success Response (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 100,
      "content": "Bài hát quá đỉnh!",
      "user": { "id": 5, "name": "Thanh" },
      "created_at": "2026-07-08T10:00:00Z",
      "replies": [
        { "id": 101, "content": "Đồng ý", "user": { "id": 6, "name": "Lan" } }
      ]
    }
  ],
  "meta": { "current_page": 1, "last_page": 5 }
}
```

### 8. [API-151] Đăng Bình luận / Trả lời
**Tính năng:** Gửi bình luận mới vào bài hát. Nếu có `parent_id` thì tính là Reply.
* **Endpoint:** `POST /api/v1/listener/songs/{id}/comments`
* **Auth:** Bearer Token (Listener)

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| content | string | required, max:1000 | Nội dung bình luận |
| parent_id | integer | nullable, exists:comments | Truyền vào nếu đang Reply bình luận khác |

**Success Response (201 Created):**
```json
{
  "status": "success",
  "data": {
    "id": 102,
    "content": "Tôi vừa đăng bình luận",
    "created_at": "..."
  }
}
```

### 9. [API-152] Xóa Bình luận của chính mình
**Tính năng:** Listener có quyền xóa bình luận do chính họ viết (Xóa mềm - hiển thị text: "Bình luận đã bị xóa").
* **Endpoint:** `DELETE /api/v1/listener/comments/{id}`
* **Auth:** Bearer Token (Listener)

**Error Response (403 Forbidden):**
*Nếu cố tình xóa bình luận của người khác.*
