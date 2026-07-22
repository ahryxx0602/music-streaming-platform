# [SCR-ADM-02] Artist Invites Management

## Tổng quan (Overview)
Module quản lý mã mời nghệ sĩ cho phép Administrator tạo, quản lý và thu hồi các mã đăng ký (Invitation Token). Nghệ sĩ nhận được đường link chứa mã này có thể sử dụng để đăng ký tài khoản có vai trò `artist` (thay vì `user` thông thường).

## Database Schema (Bảng `artist_invitations`)
Bảng này đã có sẵn trong migration `2026_07_09_060711_create_artist_invitations_table.php`:
- `id` (PK)
- `email` (string, nullable) - Tùy chọn, để ghi nhớ mã này cấp cho ai.
- `token` (string, 64 chars, unique) - Chuỗi token sinh ngẫu nhiên.
- `expires_at` (timestamp) - Hạn sử dụng của mã (1, 7, 30 ngày...).
- `used_at` (timestamp, nullable) - Thời điểm mã đã được sử dụng (khi artist đăng ký thành công).
- `created_by` (foreign key -> `users.id`) - Admin tạo mã.
- `timestamps`

## Phân quyền & Điều kiện (Policies & Rules)
- Chỉ Role `admin` mới có quyền truy cập tất cả các API quản lý mã mời này.
- [RULE-INV-01]: Mã chưa dùng (`used_at === null`) và còn hạn (`expires_at > now()`) mới được tính là **Valid**.
- [RULE-INV-02]: Hủy mã (Delete) chỉ khả dụng khi mã **chưa được dùng** (`used_at === null`). Cấm xóa mã đã dùng để lưu vết kiểm toán.

---

## API Endpoints (`AdminArtistInviteController`)
**Prefix:** `/api/v1/admin/artist-invites`
**Middleware:** `auth:sanctum` (Role Admin)

### 1. Lấy danh sách mã mời (GET `/`)
- **Mã API:** `[API-ADM-30]`
- **Tác dụng**: Liệt kê các mã mời, có phân trang.
- **Tính năng mở rộng**: 
  - `Eager Load`: Load kèm người tạo mã `createdBy:id,name,email`.
  - Hỗ trợ append trường ảo `status` (Valid, Used, Expired) vào Resource trả về.
- **Response**:
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "email": "singer@example.com",
                "token": "a1b2c3d4...",
                "expires_at": "2026-08-01T00:00:00.000000Z",
                "used_at": null,
                "created_by_user": { "id": 1, "name": "Super Admin" },
                "status": "Valid"
            }
        ],
        ...
    }
}
```

### 2. Tạo mới mã mời (POST `/`)
- **Mã API:** `[API-ADM-31]`
- **Tác dụng**: Cấp một mã mới.
- **Request Payload**:
```json
{
    "email": "singer@example.com", // Nullable
    "expires_in_days": 7 // Mặc định 7, có thể là 1, 7, 30
}
```
- **Xử lý Backend**:
  1. Validate payload.
  2. Tạo token: `Str::random(64)`.
  3. Tính `expires_at`: `now()->addDays((int) $request->expires_in_days)`.
  4. Gắn `created_by` là `Auth::id()`.
  5. Lưu Database. Trả về kèm một `registration_url` để tiện cho Frontend copy (`config('app.frontend_url') . '/artist-register?token=' . $token`).
- **Response (201 Created)**:
```json
{
    "success": true,
    "message": "Mã mời đã được tạo thành công.",
    "data": {
        "invitation": { ... },
        "registration_url": "http://localhost:5173/artist-register?token=a1b2c3d4..."
    }
}
```

### 3. Hủy mã mời (DELETE `/{id}`)
- **Mã API:** `[API-ADM-32]`
- **Tác dụng**: Xóa mã mời để vô hiệu hóa (Revoke).
- **Điều kiện**: `used_at` phải là `null`. Nếu mã đã sử dụng, trả về lỗi 403.
- **Response (200 OK)**:
```json
{
    "success": true,
    "message": "Đã thu hồi và xóa mã mời."
}
```

---

## Testing Strategy (PHPUnit)
File: `backend/Modules/Administration/Tests/Feature/AdminArtistInviteControllerTest.php`
- `test_admin_can_get_invites_list`: Kiểm tra phân trang và eager load.
- `test_admin_can_create_invite_with_url`: Kiểm tra sinh token ngẫu nhiên, tạo URL chính xác.
- `test_admin_can_revoke_unused_invite`: Xóa thành công.
- `test_admin_cannot_revoke_used_invite`: Kiểm tra Rule chặn xóa mã đã sử dụng (Expect 400 hoặc 403).
