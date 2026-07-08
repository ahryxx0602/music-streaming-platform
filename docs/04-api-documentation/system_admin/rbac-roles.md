# Nhóm Tính năng: Phân quyền Spatie (RBAC Roles)

**Mô tả:** Giao diện cho phép Super Admin tạo ra các Role (Nhóm quyền) mới và cấp phát các Permission (Quyền hạn) cho chúng. (Hệ thống tích hợp package `spatie/laravel-permission`).

**Liên kết giao diện:** 
- [SCR-ADM-06 Phân quyền](../../screens/Admin/SCR-ADM-06-roles.md)

---

## 💻 Chi tiết các APIs

### 1. [API-404] Lấy danh sách toàn bộ Permissions
**Tính năng:** Lấy các quyền gốc của hệ thống (VD: `approve_songs`, `delete_users`). Dùng để check list lúc tạo Role.
* **Endpoint:** `GET /api/v1/admin/permissions`
* **Auth:** Bearer Token (Super Admin)

### 2. [API-400] Lấy danh sách Roles
**Tính năng:** Liệt kê các Role hiện có (Admin, Moderator, Content Creator...) kèm theo số lượng User đang có Role đó.
* **Endpoint:** `GET /api/v1/admin/roles`
* **Auth:** Bearer Token (Super Admin)

### 3. [API-401] Tạo Role mới
**Tính năng:** Tạo một nhóm quyền mới và map các Permission cho nó.
* **Endpoint:** `POST /api/v1/admin/roles`
* **Auth:** Bearer Token (Super Admin)

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| name | string | required | Tên role (VD: marketing_lead) |
| permissions | array | required | Mảng ID các quyền được gán |

### 4. [API-402] Cập nhật Role
**Tính năng:** Đổi tên hoặc check/uncheck các quyền của một Role.
* **Endpoint:** `PUT /api/v1/admin/roles/{id}`
* **Auth:** Bearer Token (Super Admin)

### 5. [API-403] Xóa Role
**Tính năng:** Xóa vĩnh viễn một Role. *Lưu ý: Sẽ văng lỗi 400 Bad Request nếu Role này vẫn đang có User sử dụng.*
* **Endpoint:** `DELETE /api/v1/admin/roles/{id}`
* **Auth:** Bearer Token (Super Admin)
