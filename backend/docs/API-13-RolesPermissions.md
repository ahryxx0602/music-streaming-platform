# [SCR-ADM-06] Roles & Permissions Management

## Tổng quan (Overview)
Hệ thống sử dụng Spatie Laravel Permission. Tính năng này cho phép Admin (thường là Super Admin) tạo các Vai trò (Role) mới, chỉnh sửa tên và cấp/thu hồi Quyền (Permissions) cho từng Vai trò đó. 

## Database Schema (Spatie)
Các bảng đã có sẵn:
- `roles` (id, name, guard_name)
- `permissions` (id, name, guard_name)
- `role_has_permissions` (role_id, permission_id)
- `model_has_roles`, `model_has_permissions`

## Phân quyền & Điều kiện (Policies & Rules)
- Chỉ User có Role `super-admin` mới có quyền truy cập module này.
- [RULE-ROLE-01]: Cấm sửa/xóa Role mặc định của hệ thống: `admin`, `artist`, `user`.
- Permissions là cố định (được Seed bởi hệ thống), API chỉ cung cấp danh sách đọc (Read-only) để Admin tick chọn gán cho Role.

---

## API Endpoints (`AdminRoleController`)
**Prefix:** `/api/v1/admin`
**Middleware:** `auth:sanctum`, `role:super-admin`

### 1. Lấy danh sách Roles (GET `/roles`)
- **Mã API:** `[API-ADM-33]`
- **Tác dụng**: Lấy toàn bộ Roles kèm danh sách permissions của chúng. Không phân trang (do số lượng Role thường ít).
- **Response**:
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Content Moderator",
            "guard_name": "web",
            "permissions": [
                { "id": 1, "name": "manage_songs" },
                { "id": 2, "name": "manage_albums" }
            ],
            "users_count": 5
        }
    ]
}
```

### 2. Tạo Role mới (POST `/roles`)
- **Mã API:** `[API-ADM-34]`
- **Tác dụng**: Tạo Role và map permissions.
- **Request Payload**:
```json
{
    "name": "Marketing Team",
    "permission_ids": [3, 4, 5]
}
```
- **Xử lý Backend**: `Role::create()` -> `$role->syncPermissions()`.

### 3. Cập nhật Role (PUT `/roles/{id}`)
- **Mã API:** `[API-ADM-35]`
- **Tác dụng**: Đổi tên Role và cập nhật tập Permissions. Cấm tác động lên role hệ thống (admin, artist, user).
- **Request Payload**: (Giống POST)

### 4. Xóa Role (DELETE `/roles/{id}`)
- **Mã API:** `[API-ADM-36]`
- **Tác dụng**: Xóa Role. 
- **Điều kiện**: Cấm xóa role hệ thống. Cấm xóa nếu đang có user gán role này (users_count > 0).

### 5. Lấy danh sách Permissions (GET `/permissions`)
- **Mã API:** `[API-ADM-37]`
- **Tác dụng**: Danh sách toàn bộ quyền hiện có trong DB để hiển thị dạng Checkbox list.
- **Response**: List of `Permission` objects.

---

## Testing Strategy (PHPUnit)
File: `backend/Modules/Administration/Tests/Feature/AdminRoleControllerTest.php`
- `test_superadmin_can_get_roles`
- `test_superadmin_can_create_role_with_permissions`
- `test_superadmin_cannot_update_system_roles` (Assert 403)
- `test_superadmin_cannot_delete_role_in_use` (Assert 400 hoặc 403)
