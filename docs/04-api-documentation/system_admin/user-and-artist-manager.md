# Nhóm Tính năng: Quản lý Người dùng & Nghệ sĩ (User Manager)

**Mô tả:** Định nghĩa APIs để Admin kiểm soát tài khoản của Listener (Ban/Unban) và quy trình cấp phát độc quyền tài khoản Artist.

**Liên kết giao diện:** 
- [SCR-ADM-03 Quản lý Users & Artists](../../screens/Admin/SCR-ADM-03-users.md)
- [SCR-ADM-02 Quản lý Artist Invitations](../../screens/Admin/SCR-ADM-02-invitations.md)

---

## 💻 Chi tiết các APIs - Người dùng (Listeners)

### 1. [API-320] Lấy danh sách Users
**Tính năng:** Lấy danh sách toàn bộ Users (Listener + Admin). Phân trang, có bộ lọc theo Trạng thái (Active/Banned) và Search theo email.
* **Endpoint:** `GET /api/v1/admin/users`
* **Auth:** Bearer Token (Admin)

### 2. [API-322] Đổi Trạng thái Tài khoản (Ban/Unban)
**Tính năng:** Khóa hoặc mở khóa một người dùng. Nếu bị khóa, mọi Token của user sẽ bị chặn ở Middleware.
* **Endpoint:** `PUT /api/v1/admin/users/{id}/status`
* **Auth:** Bearer Token (Admin)

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| status | string | required | "Active", "Banned" |

### 3. [API-323] Gửi link Reset Password
**Tính năng:** Ép một user phải đổi mật khẩu bằng cách gửi mail tự động.
* **Endpoint:** `POST /api/v1/admin/users/{id}/reset-password`
* **Auth:** Bearer Token (Admin)

### 4. [API-324] Xóa tài khoản Staff
**Tính năng:** Xóa cứng một tài khoản Admin/Moderator khỏi hệ thống (Không dùng để xóa Listener).
* **Endpoint:** `DELETE /api/v1/admin/users/{id}`
* **Auth:** Bearer Token (Admin)

### 5. [API-325] Cấp Role cho Staff
**Tính năng:** Chuyển role của một staff (Ví dụ: Từ Moderator lên Super Admin).
* **Endpoint:** `PUT /api/v1/admin/users/{id}/roles`
* **Auth:** Bearer Token (Admin)

---

## 💻 Chi tiết các APIs - Nghệ sĩ (Artists)

### 6. [API-330] Lấy danh sách Nghệ sĩ
**Tính năng:** Tương tự API-320 nhưng join với bảng `artist_profiles` để lấy Nghệ danh.
* **Endpoint:** `GET /api/v1/admin/artists`
* **Auth:** Bearer Token (Admin)

### 7. [API-331] Cập nhật thông tin Nghệ sĩ
**Tính năng:** Sửa chữa Bio, Tên của Artist trong trường hợp họ vi phạm nguyên tắc cộng đồng.
* **Endpoint:** `PUT /api/v1/admin/artists/{id}`
* **Auth:** Bearer Token (Admin)

### 8. [API-332] Xác minh Nghệ sĩ (Tích xanh)
**Tính năng:** Cấp hoặc thu hồi huy hiệu "Verified" (Tích xanh).
* **Endpoint:** `POST /api/v1/admin/artists/{id}/verify`
* **Auth:** Bearer Token (Admin)

### 9. [API-321] Tạo nhanh tài khoản Artist
**Tính năng:** Admin tự tay tạo luôn tài khoản Artist mà không cần gửi Invite Token.
* **Endpoint:** `POST /api/v1/admin/users/artist`
* **Auth:** Bearer Token (Admin)

---

## 💻 Chi tiết các APIs - Thư mời Nghệ sĩ (Invitations)

### 10. [API-340] Lấy danh sách Lời mời
**Tính năng:** Liệt kê các mã Token đã phát hành và trạng thái (Đã dùng, Chờ, Hết hạn).
* **Endpoint:** `GET /api/v1/admin/artist-invitations`
* **Auth:** Bearer Token (Admin)

### 11. [API-341] Tạo Thư mời mới
**Tính năng:** Sinh mã Token và đẩy vào Queue để gửi Email mời hợp tác.
* **Endpoint:** `POST /api/v1/admin/artist-invitations`
* **Auth:** Bearer Token (Admin)

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| email | string | required, email | Email nghệ sĩ sẽ nhận thư |
| expires_in_days | integer | nullable, default:7 | Hạn sử dụng (Ngày) |

### 12. [API-342] Thu hồi Thư mời
**Tính năng:** Đánh dấu Token là đã hết hạn ngay lập tức.
* **Endpoint:** `PUT /api/v1/admin/artist-invitations/{id}/revoke`
* **Auth:** Bearer Token (Admin)
