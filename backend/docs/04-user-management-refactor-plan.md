# Kế hoạch Refactor - Quản lý Người dùng (Backend)

Dựa trên tài liệu đặc tả [SCR-ADM-03](../../docs/screens/System/SCR-ADM-03-user-management.md) và đợt review code vừa qua, Backend cần bổ sung các Endpoint còn thiếu và nâng cấp tính năng truy vấn cho bảng danh sách người dùng.

## 1. Mục tiêu tái cấu trúc
- **Tuân thủ Đặc tả:** Đảm bảo 100% các API cần thiết cho 3 Tab người dùng (Listener, Artist, Staff) đều sẵn sàng.
- **Tối ưu Truy vấn:** Lấy được thông số thống kê (`songs_count`, `streams_count`) cho đối tượng Nghệ sĩ.
- **Bảo mật & Phân quyền:** Ngăn chặn các User bình thường gọi API Update Status, xử lý chuẩn Role.

## 2. Nâng cấp API Truy vấn Danh sách (GET)
**File:** `Modules/Users/Http/Controllers/UsersController.php`

Hiện tại tính năng QueryBuilder đã rất tốt. Tuy nhiên cần cập nhật hàm `index`:
- Khi filter là `role=artist`, cần `withCount('songs')` để Frontend có dữ liệu hiển thị tổng số bài hát.
- Load eager relationship: `with('artistProfile')` để lấy thông tin Nghệ danh (`stage_name`).

```php
$users = QueryBuilder::for(User::class)
    ->with('artistProfile')
    ->when($request->input('filter.role') === 'artist', function ($query) {
        // Tích hợp relationships từ Module Songs (định nghĩa relation 'songs' trong User model)
        $query->withCount('songs'); 
    })
    ->allowedFilters(...)
```

## 3. Bổ sung các Endpoints Mới

Tất cả các định dạng và Payload được mô tả chi tiết tại [API-03-UserManagement.md](./API-03-UserManagement.md).

### 3.1. API Tạo nhanh Artist
- **Route:** `POST /api/v1/admin/users/artist`
- **Nhiệm vụ:**
  1. Validate Form (Tên, Nghệ danh, Email, Mật khẩu).
  2. Bắt đầu `DB::transaction`.
  3. Tạo `User` với `role = 'artist'`.
  4. Khởi tạo một row trong `artist_profiles` chứa `stage_name`.
  5. Commit transaction và trả về JSON.

### 3.2. API Khóa/Mở khóa User
- **Route:** `PUT /api/v1/admin/users/{id}/status`
- **Nhiệm vụ:**
  1. Nhận Payload `status` (`Active`, `Suspended`, `Banned`).
  2. Cập nhật `user->status`.
  3. **[RULE-ADM-LST-01] & [RULE-ADM-ART-02]:** Nếu bị `Banned` hoặc `Suspended`, bắt buộc phải gọi logic Xóa toàn bộ Session của user đó trong Redis/Database. Nếu là Artist bị Suspend, Frontend sẽ tự ẩn bài hát, tuy nhiên Backend có thể cân nhắc kích hoạt Event `ArtistSuspended` để clear Cache Public API.

### 3.3. API Phân quyền Nhân viên (Staff RBAC)
- **Route:** `PUT /api/v1/admin/users/{id}/roles`
- **Nhiệm vụ:**
  1. Dành cho Super Admin.
  2. Nhận mảng `roles` (VD: `['Content Moderator']`).
  3. Sync Roles bằng thư viện Spatie Permission.

## 4. Các bước thực hiện
1. **Tạo FormRequest mới:** `StoreArtistRequest`, `UpdateUserStatusRequest`.
2. **Cập nhật Model User:** Đảm bảo có các Relationship `songs()`, `artistProfile()` và cài đặt cấu hình đầy đủ.
3. **Bổ sung Routes:** Mở file `Modules/Administration/routes/api.php` hoặc `Modules/Users/routes/api.php` để thêm 3 Route (POST, PUT, PUT).
4. **Cập nhật Controller:** Viết 3 Method tương ứng vào `UsersController.php` (hoặc tách ra thành `UserManagementController`).
