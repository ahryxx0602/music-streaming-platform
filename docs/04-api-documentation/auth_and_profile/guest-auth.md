# Nhóm Tính năng: Guest Authentication (Xác thực Người dùng Khách)

**Mô tả:** Tài liệu này định nghĩa toàn bộ các luồng xác thực dành cho người chưa đăng nhập (Guest). Hệ thống sử dụng Laravel Sanctum (SPA Auth) dựa trên HttpOnly Cookie, nên mọi Request trước khi POST đều phải lấy CSRF Token.

**Liên kết giao diện:** 
- [SCR-SHR-01 Đăng nhập](../../screens/Auth/SCR-SHR-01-login.md)
- [SCR-LST-01 Đăng ký Listener](../../screens/Auth/SCR-LST-01-register.md)
- [SCR-ART-01 Đăng ký Artist](../../screens/Auth/SCR-ART-01-register.md)
- [SCR-SHR-02 Quên mật khẩu](../../screens/Auth/SCR-SHR-02-password-recovery.md)

---

## 🔄 Tóm tắt luồng gọi API (Workflow)

1. **SPA Initialization:** Trước khi gọi bất kỳ API POST nào, Frontend phải gọi ngầm `[API-001]` để lấy Cookie `XSRF-TOKEN`.
2. **Luồng Đăng nhập chung:** Gọi `[API-002]` với Email/Password. Nếu thành công, Backend tự động Set-Cookie. Backend trả về thông tin User kèm Role để Frontend điều hướng (Dashboard vs Home).
3. **Luồng OAuth2:** Gọi `[API-009]` để lấy link redirect sang Google. Sau khi Google trả về, Frontend/Backend xử lý Callback tại `[API-010]`.
4. **Đăng ký Artist (Đặc thù):** Artist không được đăng ký tự do. Họ phải truy cập link chứa `token` mời. Frontend gọi `[API-007]` để validate token đó. Nếu OK mới hiện Form và gọi `[API-008]` để submit.

---

## 💻 Chi tiết các APIs

### 1. [API-001] Khởi tạo CSRF Token (Sanctum SPA)
**Tính năng:** Thiết lập Cookie bảo mật chống giả mạo Request (CSRF). Yêu cầu bắt buộc của Laravel Sanctum.
* **Endpoint:** `GET /sanctum/csrf-cookie`
* **Auth:** Không yêu cầu

**Success Response (204 No Content):**
*(Hệ thống trả về header `Set-Cookie` chứa `XSRF-TOKEN` và `laravel_session`. Không có Body Data).*

---

### 2. [API-002] Đăng nhập (Login)
**Tính năng:** Đăng nhập bằng Email và Mật khẩu. Dùng chung cho cả Listener, Artist và Admin.
* **Endpoint:** `POST /api/v1/guest/auth/login`
* **Auth:** Cần CSRF Token

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| email | string | required, email | Địa chỉ email đã đăng ký |
| password | string | required, min:8 | Mật khẩu |
| remember | boolean | nullable | Lưu phiên đăng nhập dài hạn |

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Đăng nhập thành công",
  "data": {
    "user": {
      "id": 1,
      "name": "Sơn Tùng M-TP",
      "email": "sontung@mtpent.com",
      "role": "artist",
      "avatar": "https://minio.../avatar.jpg",
      "status": "Active"
    }
  }
}
```
*(Ghi chú: Header sẽ chứa `Set-Cookie` cho phiên đăng nhập).*

---

### 3. [API-003] Đăng ký Listener
**Tính năng:** Dành cho người nghe nhạc đăng ký tài khoản tự do.
* **Endpoint:** `POST /api/v1/guest/auth/register`
* **Auth:** Cần CSRF Token

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| name | string | required, max:255 | Tên hiển thị |
| email | string | required, email, unique:users | Địa chỉ email |
| password | string | required, min:8, confirmed | Mật khẩu (cần trường password_confirmation) |

**Success Response (201 Created):**
```json
{
  "status": "success",
  "message": "Đăng ký thành công. Vui lòng kiểm tra email để xác thực tài khoản."
}
```

---

### 4. [API-004] Yêu cầu Đặt lại Mật khẩu (Forgot Password)
**Tính năng:** Gửi email chứa link khôi phục mật khẩu.
* **Endpoint:** `POST /api/v1/guest/auth/forgot-password`
* **Auth:** Cần CSRF Token

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| email | string | required, email, exists:users | Email cần khôi phục |

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Liên kết đặt lại mật khẩu đã được gửi vào email của bạn."
}
```

---

### 5. [API-005] Đặt lại Mật khẩu (Reset Password)
**Tính năng:** Xử lý form đặt mật khẩu mới thông qua Token từ email.
* **Endpoint:** `POST /api/v1/guest/auth/reset-password`
* **Auth:** Cần CSRF Token

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| token | string | required | Token băm lấy từ URL email |
| email | string | required, email | Email xác nhận lại |
| password | string | required, min:8, confirmed | Mật khẩu mới |

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Mật khẩu đã được thay đổi thành công. Bạn có thể đăng nhập."
}
```

---

### 6. [API-006] Xác thực Email (Verify Email)
**Tính năng:** API endpoint cho Signed URL gửi trong email đăng ký.
* **Endpoint:** `GET /api/v1/guest/auth/email/verify/{id}/{hash}`
* **Auth:** Kèm Signature do Laravel sinh ra ở Query Params.

**Success Response (Redirect):**
*(API này thường redirect thẳng về trang Frontend kèm theo Param thông báo thành công: `https://frontend.com/login?verified=1`).*

---

### 7. [API-007] Validate Token Mời Artist
**Tính năng:** Khi Artist truy cập URL đăng ký (VD: `/artist-register?token=abc`), Frontend gọi API này để kiểm tra xem Token còn hạn không trước khi hiện Form.
* **Endpoint:** `GET /api/v1/guest/auth/artist-register/validate`
* **Query Params:** `?token=...`

**Success Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "is_valid": true,
    "email_hint": "son***@mtpent.com" 
  }
}
```

---

### 8. [API-008] Đăng ký Artist
**Tính năng:** Submit form đăng ký cho Artist, bắt buộc phải có Token mời.
* **Endpoint:** `POST /api/v1/guest/auth/artist-register`
* **Auth:** Cần CSRF Token

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| token | string | required, exists:artist_invitations | Token hợp lệ |
| name | string | required, max:255 | Tên thật hoặc tên công ty |
| stage_name | string | required, max:100 | Nghệ danh (Ví dụ: Sơn Tùng M-TP) |
| email | string | required, email, unique:users | Email đăng ký |
| password | string | required, min:8, confirmed | Mật khẩu |

**Success Response (201 Created):**
```json
{
  "status": "success",
  "message": "Đăng ký không gian nghệ sĩ thành công."
}
```

---

### 9. [API-009] Request OAuth2 Redirect
**Tính năng:** Sinh ra URL của Provider (Google/Facebook) để Frontend chuyển hướng User qua màn hình đăng nhập của Provider.
* **Endpoint:** `GET /api/v1/guest/auth/redirect/{provider}`
* **Path Variables:** `provider` (google, facebook)

**Success Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "redirect_url": "https://accounts.google.com/o/oauth2/auth?client_id=..."
  }
}
```

---

### 10. [API-010] OAuth2 Callback Handler
**Tính năng:** Nơi Provider gọi về (hoặc Frontend chuyển hướng về) kèm Authorization Code. Backend trao đổi lấy thông tin User, sau đó đăng nhập và set Cookie.
* **Endpoint:** `GET /api/v1/guest/auth/callback/{provider}`
* **Query Params:** `code`, `state` (do Provider trả về)

**Success Response (Redirect):**
*(Tạo/Merge tài khoản thành công, Set-Cookie Sanctum và Redirect về Frontend: `https://frontend.com/explore?login=success`).*
