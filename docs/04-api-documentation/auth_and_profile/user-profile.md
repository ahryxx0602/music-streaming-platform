# Nhóm Tính năng: User Profile & Security (Hồ sơ & Bảo mật)

**Mô tả:** Tài liệu này bao phủ toàn bộ các API dành cho việc quản lý tài khoản cá nhân của người dùng *đã đăng nhập*, bao gồm Lấy thông tin (Me), Chỉnh sửa Profile (Tên, Avatar, Cấu hình thông báo), Đổi mật khẩu, và Quản lý các Phiên đăng nhập (Sessions). 

Tài liệu áp dụng chung cho cả 3 nhóm quyền: Listener, Artist và Admin (Các nhóm chỉ khác nhau ở tiền tố URL: `/listener/`, `/artist/`, `/admin/`).

**Liên kết giao diện:** 
- [SCR-SHR-04 Cài đặt Tài khoản](../../screens/Auth/SCR-SHR-04-security-settings.md)
- [SCR-SHR-03 Xác thực Email](../../screens/Auth/SCR-SHR-03-email-verification.md)

---

## 1. 🆔 Thông tin Cá nhân & Phiên làm việc (Me & Logout)

### 1.1 Lấy thông tin Cá nhân (Get Profile)
**Tính năng:** Lấy thông tin của người dùng hiện tại đang đăng nhập qua Sanctum Cookie.
* **Endpoints:** 
  * `[API-101] GET /api/v1/listener/auth/me`
  * `[API-201] GET /api/v1/artist/auth/me`
* **Auth:** Bearer Token (hoặc Cookie)

**Success Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "user": {
      "id": 1,
      "name": "Sơn Tùng M-TP",
      "email": "sontung@mtpent.com",
      "role": "listener",
      "avatar": "https://minio.../avatar.jpg",
      "status": "Active",
      "notification_prefs": {
        "email_new_release": true,
        "email_promotions": false
      }
    }
  }
}
```

### 1.2 Đăng xuất (Logout)
**Tính năng:** Hủy bỏ phiên đăng nhập hiện tại. Hệ thống sẽ xóa bỏ file session ở server và xóa Cookie `laravel_session` ở Client.
* **Endpoints:** 
  * `[API-102] POST /api/v1/listener/auth/logout`
  * `[API-202] POST /api/v1/artist/auth/logout`
  * `[API-302] POST /api/v1/admin/auth/logout`
* **Auth:** Cần CSRF Token & Đã đăng nhập

**Success Response (204 No Content):**
*(Hệ thống trả về header xóa Cookie. Không có Body Data).*

### 1.3 Đăng nhập Admin (Admin Login)
**Tính năng:** Cổng đăng nhập riêng biệt dành cho Admin. Yêu cầu tài khoản phải có role Admin/Moderator.
* **Endpoint:** `[API-301] POST /api/v1/admin/auth/login`
* **Auth:** Cần CSRF Token

**Success Response (200 OK):** *(Cấu trúc tương tự API-002 ở file guest-auth.md).*

---

## 2. 📝 Cập nhật Profile (Listener)

*(Lưu ý: API cập nhật Profile chi tiết của Artist - API 210 đến 213 - được quy định riêng ở thư mục Artist Workspace do có thêm các trường Nghệ danh, Bio, Cover).*

### 2.1 [API-107] Cập nhật Thông tin & Cấu hình (Update Profile)
**Tính năng:** Cho phép Listener đổi tên và thay đổi tùy chọn thông báo (Notification Preferences).
* **Endpoint:** `PUT /api/v1/listener/profile`
* **Auth:** Bearer Token / Cookie

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| name | string | required, max:255 | Tên hiển thị mới |
| notification_prefs | object | nullable | JSON cấu hình nhận email |

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Cập nhật thông tin thành công.",
  "data": { "name": "..." }
}
```

### 2.2 [API-109] Cập nhật Ảnh đại diện (Upload Avatar)
**Tính năng:** Tải lên file ảnh Avatar mới. API này dùng phương thức POST để xử lý file Multipart/form-data mượt mà nhất.
* **Endpoint:** `POST /api/v1/listener/profile/avatar`
* **Auth:** Bearer Token / Cookie
* **Content-Type:** `multipart/form-data`

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| avatar | file | required, image, max:2048 | File ảnh (jpg, png, tối đa 2MB) |

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Cập nhật ảnh đại diện thành công.",
  "data": {
    "avatar_url": "https://minio.../new-avatar.jpg"
  }
}
```

---

## 3. 🔒 Bảo mật (Security)

### 3.1 Gửi lại Email Xác thực (Resend Verification)
**Tính năng:** Nếu người dùng chưa xác thực email, họ có thể bấm gửi lại link xác thực.
* **Endpoint:** `[API-103] POST /api/v1/listener/auth/email/resend`
* **Auth:** Cần CSRF Token

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Email xác thực mới đã được gửi."
}
```

### 3.2 Đổi Mật khẩu (Change Password)
**Tính năng:** Đổi mật khẩu tài khoản khi đang trong phiên đăng nhập.
* **Endpoints:** 
  * `[API-104] PUT /api/v1/listener/auth/password`
  * `[API-203] PUT /api/v1/artist/auth/password`
  * `[API-303] PUT /api/v1/admin/auth/password`
* **Auth:** Bearer Token / Cookie

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| current_password | string | required | Mật khẩu hiện tại |
| password | string | required, min:8, confirmed | Mật khẩu mới |

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Đổi mật khẩu thành công."
}
```

### 3.3 [API-105] Lấy danh sách Phiên đăng nhập (Active Sessions)
**Tính năng:** Liệt kê các thiết bị/trình duyệt đang đăng nhập tài khoản này (Giống Facebook/Spotify). Giúp giới hạn số thiết bị stream nhạc cùng lúc.
* **Endpoint:** `GET /api/v1/listener/auth/sessions`
* **Auth:** Bearer Token / Cookie

**Success Response (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": "abc-123",
      "ip_address": "192.168.1.1",
      "user_agent": "Mozilla/5.0 (Windows NT 10.0...) Chrome/115",
      "last_activity": "2026-07-08T10:00:00Z",
      "is_current_device": true
    },
    {
      "id": "xyz-789",
      "ip_address": "113.190.x.x",
      "user_agent": "Mozilla/5.0 (iPhone; CPU iPhone OS...) Safari",
      "last_activity": "2026-07-07T08:30:00Z",
      "is_current_device": false
    }
  ]
}
```

### 3.4 [API-106] Thu hồi Phiên đăng nhập (Revoke Session)
**Tính năng:** Đăng xuất từ xa một thiết bị khác (Xóa phiên làm việc).
* **Endpoint:** `DELETE /api/v1/listener/auth/sessions/{session_id}`
* **Auth:** Bearer Token / Cookie

**Success Response (204 No Content):**
*(Phiên làm việc trên thiết bị kia sẽ bị vô hiệu hóa).*
