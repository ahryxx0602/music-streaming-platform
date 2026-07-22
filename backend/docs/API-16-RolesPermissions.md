# Kế hoạch Triển khai Backend: [SCR-ADM-06] Phân quyền Quản trị (Roles & Permissions)

## 1. Mục tiêu
Hệ thống Admin đã hoàn thiện toàn bộ các phân hệ (Dashboard, Users, Inventory, Moderation, Genres, Playlists, Banners, Audit Logs, Settings). Nhiệm vụ cuối cùng là xây dựng một bộ **Ma trận Quyền (Permissions Matrix)** hoàn chỉnh bao phủ 100% các tính năng này để gán cho các Staff.

## 2. Kiến trúc Database (Spatie Permission)
Đã sử dụng package `spatie/laravel-permission`. Các bảng đã có:
- `roles`, `permissions`, `role_has_permissions`, `model_has_permissions`, `model_has_roles`.

## 3. Danh sách Permissions (Ma trận phân quyền chi tiết)
Backend cần cập nhật `RolePermissionSeeder` để seed đầy đủ các quyền (permissions) sau đây vào DB:

### 3.1. Dashboard & Audit
- `view_dashboard`: Xem tổng quan thống kê (Doanh thu, Lượt stream).
- `view_audit_logs`: Xem nhật ký hệ thống.

### 3.2. Quản lý Người dùng (Users & Invites)
- `view_users`: Xem danh sách người dùng (Listeners, Artists, Staff).
- `manage_users`: Thêm, sửa, khoá, mở khoá tài khoản.
- `delete_users`: Xoá tài khoản vĩnh viễn.
- `manage_invites`: Tạo và xoá mã mời nghệ sĩ.

### 3.3. Quản lý Kho Nhạc (Inventory & Moderation)
- `view_inventory`: Xem danh sách bài hát và album trong kho nhạc.
- `manage_inventory`: Sửa thông tin, xoá bài hát/album, phân bổ nhạc vào album.
- `moderate_songs`: Duyệt hoặc từ chối bài hát mới tải lên.

### 3.4. Quản lý Nội dung (Content & Banners)
- `manage_genres`: Thêm, sửa, xoá thể loại nhạc.
- `manage_playlists`: Thêm, sửa, xoá danh sách phát hệ thống.
- `manage_banners`: Upload ảnh, sắp xếp thứ tự hiển thị banner trang chủ.

### 3.5. Cài đặt Hệ thống (System & Roles)
- `manage_settings`: Thay đổi tỷ lệ chia tiền, bật tắt bảo trì, v.v.
- `manage_roles`: Tạo, sửa, xoá và gán quyền cho các Vai trò (Role).

## 4. API Endpoints
Backend Agent đã code sẵn `AdminRoleController` ở các buổi trước (API-ADM-33 đến API-ADM-37). Các API hiện có:
- `GET /api/v1/admin/roles`: Danh sách roles.
- `POST /api/v1/admin/roles`: Tạo role + gán permission_ids.
- `PUT /api/v1/admin/roles/{id}`: Sửa role.
- `DELETE /api/v1/admin/roles/{id}`: Xoá role.
- `GET /api/v1/admin/permissions`: Danh sách tất cả permissions.

## 5. Middleware Bảo vệ Routes
- Đảm bảo tất cả các routes trong `routes/api.php` của phân hệ Admin phải được bọc bởi middleware `permission:tên_quyền` hoặc thực hiện check `abort_if(!auth()->user()->can('tên_quyền'), 403);` trong Controller tương ứng.

## 6. Nhiệm vụ của Backend Agent
1. Cập nhật file Seeder (VD: `AdministrationDatabaseSeeder` hoặc `RolesAndPermissionsSeeder`) để chèn TOÀN BỘ 14 permissions trên.
2. Quét lại toàn bộ các `Admin___Controller` và gắn policy/middleware check permission tương ứng cho từng hàm.
3. Push code.
