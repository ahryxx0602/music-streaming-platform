# Kế hoạch Triển khai Full CRUD & Status Management (Admin Dashboard)

Tài liệu này đặc tả toàn bộ danh sách các file cần thiết lập để hoàn thiện toàn bộ chức năng: Thêm (Create), Sửa (Update), Xóa (Delete/Soft Delete), và Thay đổi Trạng thái (Change Status) cho 3 nhóm người dùng: Listener, Artist, và Staff. 

Kiến trúc này áp dụng chặt chẽ mô hình **Feature-Sliced Design (FSD)** và tách bạch UI Admin với UI Client.

---

## 1. PHÍA FRONTEND (VUE 3 + TAILWIND)

### Level 1: Admin UI Primitives (Thành phần giao diện gốc)
Các component này không chứa logic nghiệp vụ, chỉ chứa Style (Light Theme, High Density) và phát (emit) sự kiện.
*   `src/components/admin/ui/drawer/BaseDrawer.vue`: Bọc thẻ `<Teleport to="body">`, có hiệu ứng trượt (slide) từ phải sang, nền overlay mờ.
*   `src/components/admin/ui/form/BaseAdminInput.vue`: Input form chuyên dụng cho Admin (cao 40px, viền xám, nền trắng).
*   `src/components/admin/ui/form/BaseAdminSelect.vue`: Select Box tối ưu cho Light theme.
*   `src/components/admin/ui/button/BaseAdminButton.vue`: Nút bấm (Primary, Outline, Danger).
*   `src/components/admin/ui/data-display/StatusBadge.vue`: Nhận prop `status` và tự render ra class màu tương ứng (Active, Suspended, Banned).

### Level 2: Shared Business Components
Các component có thể tái sử dụng chéo giữa nhiều Module quản trị (như Quản lý Bài hát, Quản lý Album).
*   `src/components/admin/shared/AdminDataTable.vue`: Component bảng chung nhận prop là `columns` và `data`, hỗ trợ truyền slot để render cột "Hành động" (Actions).
*   `src/components/admin/shared/ConfirmDialog.vue`: Hộp thoại xác nhận hành động nguy hiểm (Ví dụ: "Bạn có chắc muốn Xóa vĩnh viễn user này?").

### Level 3: Feature-sliced (Nghiệp vụ Quản lý User)
Chứa form nhập liệu và vỏ bọc Drawer cho từng Role.
*   **Forms (Logic Validate & Call API):**
    *   `src/components/admin/features/users/forms/ArtistForm.vue`: Form Tạo mới / Cập nhật Artist.
    *   `src/components/admin/features/users/forms/ListenerForm.vue`: Form Cập nhật Listener (Chủ yếu đổi Tên, xem Lịch sử).
    *   `src/components/admin/features/users/forms/StaffForm.vue`: Form Tạo mới / Cập nhật Staff (Có dropdown chọn Quyền).
*   **Drawers (Vỏ bọc điều phối Form):**
    *   `src/components/admin/features/users/drawers/ArtistDrawer.vue`
    *   `src/components/admin/features/users/drawers/ListenerDrawer.vue`
    *   `src/components/admin/features/users/drawers/StaffDrawer.vue`

### Level 4: Views (Giao diện hiển thị chính)
Ghép nối toàn bộ Level 1, 2, 3 lại với nhau.
*   `src/views/admin/users/UsersView.vue`: Chứa Header và thanh Tabs.
*   `src/views/admin/users/tabs/ListenerTab.vue`: Hiển thị `AdminDataTable`, tích hợp `ListenerDrawer`.
*   `src/views/admin/users/tabs/ArtistTab.vue`: Hiển thị `AdminDataTable`, tích hợp `ArtistDrawer`.
*   `src/views/admin/users/tabs/StaffTab.vue`: Hiển thị `AdminDataTable`, tích hợp `StaffDrawer`.

---

## 2. PHÍA BACKEND (LARAVEL 11)

Bổ sung các hàm, Route và Request để đáp ứng đầy đủ API CRUD cho Frontend.

### Controllers
*   **`Modules/Users/Http/Controllers/UsersController.php`**
    *   `storeStaff(Request $request)`: Tạo mới Staff và gán role.
    *   `update(Request $request, $id)`: API đa năng để cập nhật thông tin chung (Tên, Email). Tùy vào role mà update thêm `artist_profiles`.
    *   `destroy($id)`: Soft Delete người dùng.

### Form Requests (Validate dữ liệu đầu vào)
*   `Modules/Users/Http/Requests/StoreStaffRequest.php`: Bắt buộc nhập Tên, Email (unique), Password, Roles.
*   `Modules/Users/Http/Requests/UpdateUserRequest.php`: Bắt email unique (ngoại trừ user hiện tại), rule riêng cho stage_name nếu là artist.

### Routes (Admin API)
File `Modules/Administration/routes/api.php` hoặc `Modules/Users/routes/api.php`:
```php
// Tạo Staff mới
Route::post('/users/staff', [UsersController::class, 'storeStaff']);
// Cập nhật thông tin (Dùng chung)
Route::put('/users/{id}', [UsersController::class, 'update']);
// Xóa User (Soft delete)
Route::delete('/users/{id}', [UsersController::class, 'destroy']);
```

---

## 3. Thứ tự Triển khai Ưu tiên (Execution Flow)
Để không bị ngợp và đảm bảo hệ thống chạy trơn tru, chúng ta sẽ code theo thứ tự sau:
1.  **Chặng 1:** Code bộ khung `Admin UI Primitives` (FE).
2.  **Chặng 2:** Code Back-end API (Update, Delete, StoreStaff) & Test bằng Postman.
3.  **Chặng 3:** Lắp ráp Drawers & Forms cho Artist (Làm mẫu chuẩn).
4.  **Chặng 4:** Triển khai nốt cho Listener và Staff.
