# Nhóm Tính năng: Cập nhật Hồ sơ Nghệ sĩ (Artist Profile Manager)

**Mô tả:** Khác với tài khoản Listener thông thường chỉ có Tên và Avatar, tài khoản Artist có một trang Hồ sơ (Profile) phức tạp hơn để hiển thị public cho khán giả. Nhóm API này giúp Artist quản lý Nghệ danh, Tiểu sử (Bio), Links mạng xã hội và cập nhật hình ảnh độ phân giải cao.

**Liên kết giao diện:** 
- [SCR-ART-05 Cài đặt Hồ sơ Nghệ sĩ](../../screens/Artist/SCR-ART-05-profile-settings.md)

---

## 🔄 Tóm tắt luồng gọi API (Workflow)

1. Khi Artist vào trang Cài đặt Hồ sơ, gọi `[API-210]` để lấy dữ liệu hiện tại (stage_name, bio, links).
2. Khi Artist sửa text và bấm Lưu, gọi `[API-211]` qua phương thức PUT.
3. Khi Artist thay đổi Avatar hoặc Ảnh Cover, vì có xử lý File Upload nên sẽ gọi riêng rẽ `[API-212]` hoặc `[API-213]` qua phương thức POST (Multipart).

---

## 💻 Chi tiết các APIs

### 1. [API-210] Lấy dữ liệu Profile hiện tại
**Tính năng:** Trả về thông tin Profile đang có của Artist.
* **Endpoint:** `GET /api/v1/artist/profile`
* **Auth:** Bearer Token (Artist)

**Success Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "stage_name": "Sơn Tùng M-TP",
    "bio": "Nghệ sĩ độc quyền của M-TP Entertainment.",
    "social_links": {
      "facebook": "https://facebook.com/MTP.Fan",
      "instagram": "https://instagram.com/sontungmtp",
      "youtube": "https://youtube.com/c/sontungmtp"
    },
    "cover_image": "https://minio.../cover.jpg",
    "is_verified": true
  }
}
```

---

### 2. [API-211] Cập nhật Thông tin Text
**Tính năng:** Cập nhật các trường văn bản của Profile.
* **Endpoint:** `PUT /api/v1/artist/profile`
* **Auth:** Bearer Token (Artist)
* **Content-Type:** `application/json`

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| stage_name | string | required, max:100 | Nghệ danh hiển thị |
| bio | string | nullable, max:1000 | Tiểu sử / Giới thiệu |
| social_links | object | nullable | JSON chứa link MXH |

---

### 3. [API-212] Cập nhật Ảnh đại diện (Avatar)
**Tính năng:** Upload Avatar của Artist.
* **Endpoint:** `POST /api/v1/artist/profile/avatar`
* **Auth:** Bearer Token (Artist)
* **Content-Type:** `multipart/form-data`

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| avatar | file | required, image, max:2048 | Ảnh vuông (Max 2MB) |

---

### 4. [API-213] Cập nhật Ảnh Bìa (Cover Image)
**Tính năng:** Upload ảnh Cover ngang xuất hiện trên đầu trang Profile của Artist.
* **Endpoint:** `POST /api/v1/artist/profile/cover`
* **Auth:** Bearer Token (Artist)
* **Content-Type:** `multipart/form-data`

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| cover_image | file | required, image, max:5120 | Ảnh ngang độ phân giải cao (Max 5MB) |
