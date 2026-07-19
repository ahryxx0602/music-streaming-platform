# Kế hoạch Triển khai Backend API - Admin Users CRUD

Tài liệu này đặc tả chi tiết kế hoạch triển khai các Endpoints còn lại để hoàn thiện tính năng Thêm, Sửa, Xóa cho phân hệ Quản lý Người dùng (Dành cho Admin).

## 1. Mục tiêu (Objectives)
Bổ sung các APIs để Admin có thể:
1. Chỉnh sửa thông tin cơ bản của tất cả các User (Tên, Email).
2. Chỉnh sửa thông tin mở rộng của Artist (Nghệ danh - Stage Name).
3. Khởi tạo tài khoản Staff/Admin và gán phân quyền (RBAC).
4. Xóa mềm (Soft Delete) một tài khoản ra khỏi hệ thống.

---

## 2. Các Endpoint cần phát triển

### 2.1. API Tạo Tài Khoản Nhân Viên (Create Staff)
- **Method & Route:** `POST /api/v1/admin/users/staff`
- **Mục đích:** Chỉ có Super Admin mới có quyền tạo nhân viên và gán quyền.
- **Payload yêu cầu (Form Request `StoreStaffRequest`):**
  - `name`: string, max:255, required.
  - `email`: email, unique, required.
  - `password`: string, min:8, required.
  - `roles`: array, required (VD: `['Content Moderator']`).
- **Logic thực thi:**
  1. Sử dụng `DB::transaction`.
  2. Tạo bản ghi `User` (role = 'admin', status = 'Active', verified).
  3. Gọi `$user->syncRoles($request->roles)`.
  4. Bắn email thông báo mật khẩu khởi tạo cho nhân viên (Tùy chọn - Phase sau).

### 2.2. API Cập nhật Thông tin User (Update)
- **Method & Route:** `PUT /api/v1/admin/users/{id}`
- **Mục đích:** API đa năng để sửa thông tin của bất kỳ Listener, Artist hay Staff nào.
- **Payload yêu cầu (Form Request `UpdateUserRequest`):**
  - `name`: string, max:255.
  - `email`: email, unique (ngoại trừ user hiện tại).
  - `stage_name`: string (Chỉ bắt buộc/validate nếu user đang sửa có role là `artist`).
- **Logic thực thi:**
  1. Tìm User bằng ID.
  2. Cập nhật `name`, `email`.
  3. Nếu user là `artist` và có gửi lên `stage_name`, thực hiện update vào relation `artistProfile`.

### 2.3. API Xóa Người dùng (Soft Delete)
- **Method & Route:** `DELETE /api/v1/admin/users/{id}`
- **Mục đích:** Xóa tài khoản, nhưng vẫn giữ nguyên dữ liệu về Lịch sử nghe nhạc, Bài hát đã upload (đối với Artist) để đảm bảo toàn vẹn dữ liệu hệ thống (Referential Integrity).
- **Logic thực thi:**
  1. Kiểm tra không cho phép tự xóa chính mình (`$user->id == Auth::id()`).
  2. Gọi `$user->delete()` (Sử dụng trait `SoftDeletes` trong Model User).
  3. Thu hồi toàn bộ Sanctum Tokens của user đó để ép Logout ngay lập tức.
  4. Nếu là Artist, có thể kích hoạt Event `ArtistDeleted` để vô hiệu hóa / ẩn tạm thời các bài hát của họ (Cập nhật field `status` ở bảng songs, tùy logic bài toán).

---

## 3. Lộ trình Code (Action Plan)

**Bước 1: Viết Requests Validation**
- `php artisan make:request StoreStaffRequest`
- `php artisan make:request UpdateUserRequest`

**Bước 2: Cập nhật UsersController**
- Mở `Modules/Users/Http/Controllers/UsersController.php`
- Bổ sung 3 hàm: `storeStaff`, `update`, `destroy`.

**Bước 3: Đăng ký Routes**
- Mở file `routes/api.php` của module (ví dụ `Modules/Administration/routes/api.php`).
- Khai báo 3 endpoints mới. Áp dụng middleware quyền hạn (Chỉ Admin mới được truy cập, riêng route tạo Staff chỉ Super Admin được truy cập).
