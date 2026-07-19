# API Reference: User Management CRUD (Administration)

Đặc tả các Endpoints CRUD bổ sung dành cho Admin để thao tác trực tiếp với thông tin chi tiết của người dùng.

## 1. Cập nhật thông tin User (Đa vai trò)
- **Mã API:** `[API-326]`
- **Method:** `PUT`
- **Endpoint:** `/api/v1/admin/users/{id}`
- **Middleware:** `auth:sanctum`, `role:admin`

### Request Body (JSON)
```json
{
  "name": "Nguyễn Thanh Tùng M-TP",
  "email": "sontung@mtpent.com",
  "stage_name": "Sơn Tùng M-TP (Update)" 
}
```
*(Ghi chú: `stage_name` chỉ xử lý khi user có `role='artist'`)*

### Response Thành công (200 OK)
```json
{
  "success": true,
  "message": "Cập nhật thông tin thành công.",
  "data": {
    "user": {
       "id": 2,
       "name": "Nguyễn Thanh Tùng M-TP",
       "email": "sontung@mtpent.com",
       "role": "artist",
       "artist_profile": {
           "stage_name": "Sơn Tùng M-TP (Update)"
       }
    }
  }
}
```

---

## 2. Tạo Tài khoản Nhân viên (Staff)
- **Mã API:** `[API-327]`
- **Method:** `POST`
- **Endpoint:** `/api/v1/admin/users/staff`
- **Middleware:** `auth:sanctum`, `role:super-admin`

### Request Body (JSON)
```json
{
  "name": "Trần Văn A",
  "email": "vana@aurora.com",
  "password": "Password123@",
  "roles": ["Content Moderator"]
}
```

### Response Thành công (201 Created)
```json
{
  "success": true,
  "message": "Tạo tài khoản nhân viên thành công.",
  "data": {
    "user": {
       "id": 10,
       "name": "Trần Văn A",
       "email": "vana@aurora.com",
       "role": "admin",
       "roles": ["Content Moderator"]
    }
  }
}
```

---

## 3. Xóa Người dùng (Soft Delete)
- **Mã API:** `[API-328]`
- **Method:** `DELETE`
- **Endpoint:** `/api/v1/admin/users/{id}`
- **Middleware:** `auth:sanctum`, `role:admin`

### Yêu cầu đặc biệt
- Trả về mã lỗi 403 Forbidden nếu Admin cố gắng truyền ID của chính mình (`id` == `Auth::id()`).
- Hệ thống tự động thu hồi toàn bộ Access Token của user.

### Response Thành công (200 OK)
```json
{
  "success": true,
  "message": "Đã xóa người dùng thành công."
}
```
