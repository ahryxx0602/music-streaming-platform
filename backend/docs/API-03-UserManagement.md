# API Reference: User Management (Administration)

Tài liệu đặc tả các Endpoints giao tiếp giữa Admin Frontend và Backend cho phân hệ Quản lý Người dùng ([SCR-ADM-03](../../docs/screens/System/SCR-ADM-03-user-management.md)).

## 1. Lấy danh sách Người dùng (Phân trang & Lọc)
- **Mã API:** `[API-320]`
- **Method:** `GET`
- **Endpoint:** `/api/v1/admin/users`
- **Middleware:** `auth:sanctum`, `role:admin`

### Query Parameters
| Tên | Loại | Mô tả | Ví dụ |
| :--- | :--- | :--- | :--- |
| `filter[role]` | string | Lọc theo vai trò | `listener`, `artist`, `admin` |
| `filter[status]`| string | Lọc theo trạng thái | `Active`, `Banned`, `Suspended` |
| `filter[search]`| string | Tìm kiếm theo Tên hoặc Email | `thanh@gmail.com` |
| `sort` | string | Sắp xếp (Thêm `-` để xếp giảm dần) | `-created_at`, `name` |
| `page` | int | Số trang hiện tại | `1` |
| `per_page` | int | Số dòng trên 1 trang | `15` |

### Response Thành công (200 OK)
```json
{
  "success": true,
  "message": "Lấy danh sách người dùng thành công.",
  "data": {
    "items": [
      {
        "id": 1,
        "name": "Sơn Tùng M-TP",
        "email": "sontung@mtpent.com",
        "role": "artist",
        "status": "Active",
        "avatar": "https://...",
        "created_at": "2026-07-15T00:00:00.000000Z",
        "artist_profile": {
           "stage_name": "Sơn Tùng M-TP",
           "bio": "M-TP Entertainment"
        },
        "songs_count": 24 // Trả về nếu filter[role]=artist
      }
    ],
    "meta": {
      "current_page": 1,
      "last_page": 5,
      "per_page": 15,
      "total": 75
    }
  }
}
```

---

## 2. Tạo Nhanh Nghệ Sĩ (Tạo Trực Tiếp)
- **Mã API:** `[API-321]`
- **Method:** `POST`
- **Endpoint:** `/api/v1/admin/users/artist`
- **Middleware:** `auth:sanctum`, `role:admin`

### Request Body (JSON)
```json
{
  "name": "Nguyễn Thanh Tùng",
  "stage_name": "Sơn Tùng M-TP",
  "email": "sontung@mtpent.com",
  "password": "Password123@",
  "password_confirmation": "Password123@"
}
```

### Response Thành công (201 Created)
```json
{
  "success": true,
  "message": "Tạo nghệ sĩ thành công.",
  "data": {
    "user": {
       "id": 2,
       "name": "Nguyễn Thanh Tùng",
       "email": "sontung@mtpent.com",
       "role": "artist"
    }
  }
}
```
*(Lỗi 422 nếu Email đã tồn tại hoặc mật khẩu yếu).*

---

## 3. Thay Đổi Trạng Thái (Khóa/Mở Khóa/Đình Chỉ)
- **Mã API:** `[API-322]`
- **Method:** `PUT`
- **Endpoint:** `/api/v1/admin/users/{id}/status`
- **Middleware:** `auth:sanctum`, `role:admin`

### Request Body (JSON)
```json
{
  "status": "Suspended",
  "reason": "Vi phạm bản quyền âm nhạc (Tùy chọn)"
}
```
*(Các giá trị hợp lệ: `Active`, `Suspended`, `Banned`)*

### Response Thành công (200 OK)
```json
{
  "success": true,
  "message": "Cập nhật trạng thái thành công.",
  "data": {
    "status": "Suspended"
  }
}
```

---

## 4. Gán Quyền (Assign Roles) cho Staff
- **Mã API:** `[API-ADM-06]`
- **Method:** `PUT`
- **Endpoint:** `/api/v1/admin/users/{id}/roles`
- **Middleware:** `auth:sanctum`, `role:super-admin`

### Request Body (JSON)
```json
{
  "roles": ["Content Moderator"]
}
```

### Response Thành công (200 OK)
```json
{
  "success": true,
  "message": "Cập nhật quyền thành công."
}
```
