# [SCR-ADM-06] Phân quyền Quản trị (Roles & Permissions)

> **Mô tả ngắn:** Màn hình cấu hình phân quyền động (RBAC - Role Based Access Control) dành riêng cho Super Admin. Cho phép tạo các Nhóm quyền (Roles) và gán các Chức năng (Permissions) tương ứng cho từng nhóm.

## 1. Thông tin chung (Meta)
*   **Module:** System / Security
*   **Route / URL:** `/admin/roles`
*   **Layout sử dụng:** `AdminLayout.vue`
*   **Quyền truy cập:** `[PER-SUPER-ADMIN]` (Chỉ tài khoản cấp cao nhất mới được vào màn hình này).
*   **Component con (Children):**
    *   `RoleList.vue` (Bảng danh sách các Nhóm quyền hiện có).
    *   `RoleFormModal.vue` (Popup thêm/sửa Nhóm quyền kèm Checkbox phân quyền).

## 2. Thành phần giao diện (UI Elements)
*   **Khu vực Danh sách Role (Data Table):**
    *   `Cột hiển thị`: Tên Role (VD: Content Moderator), Mô tả, Số lượng User đang giữ Role này.
    *   `Hành động`: Nút `Sửa` (Mở form), Nút `Xóa` (Chỉ hiện nếu số User = 0).
*   **Khu vực Form Phân quyền (Role Form):**
    *   `Input Tên Role`: Dạng text (VD: Finance).
    *   `Khối Checkbox Permissions`: Chia thành các nhóm module để dễ check (Ví dụ: Nhóm Nhạc: `[x] Duyệt bài`, `[ ] Xóa bài`. Nhóm User: `[x] Khóa User`). 
    *   `Button Primary`: Nút "Lưu phân quyền".

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **Lấy dữ liệu (Fetch):**
    *   `[API-400]` - `GET /api/v1/admin/roles` (Lấy danh sách Role và kèm theo danh sách Permissions để render Checkbox).
*   **Ghi dữ liệu (Mutations):**
    *   `[API-401]` - `POST /api/v1/admin/roles` (Tạo Role mới kèm mảng ID các permission).
    *   `[API-402]` - `PUT /api/v1/admin/roles/{id}` (Cập nhật lại danh sách Permission cho Role).
    *   `[API-403]` - `DELETE /api/v1/admin/roles/{id}` (Xóa Role).

### State Management (Pinia)
*   **Store:** `useRbacStore.js`
*   **Actions:** `fetchRoles()`, `saveRole(payload)`, `deleteRole(id)`.

## 4. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-ADM-RBAC-01] - Hardcode Super Admin:** Nhóm quyền gốc `Super Admin` là bất khả xâm phạm. Frontend phải `disabled` nút Xóa và Sửa đối với Role này. Không ai có thể gỡ quyền của Super Admin.
*   **[RULE-ADM-RBAC-02] - Ràng buộc Xóa (Constraint Deletion):** Không cho phép xóa một Role nếu đang có >= 1 User được gán Role đó. Bắt buộc phải chuyển User sang Role khác (ở màn hình `[SCR-ADM-03C]`) rồi mới được xóa Role.
*   **[RULE-ADM-RBAC-03] - Checkbox Auto-sync & 403 Fallback:** Khi Super Admin tick chọn/bỏ chọn Permission và lưu lại, Backend tự động Sync lại bảng trung gian. Các User đang online sẽ bị ảnh hưởng ngay lập tức. Nếu bị tước quyền giữa chừng, API tiếp theo của User đó sẽ trả về `403 Forbidden`. Frontend bắt buộc phải bẫy lỗi (Catch Error) Global: Bắn Toast "Quyền hạn của bạn đã thay đổi" và tự động Refresh lại trang để nạp lại đúng giao diện cho phép.

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Đọc (Read):** Bảng `roles`, `permissions` (Từ package Spatie Permission).
*   **Ghi (Write):** 
    *   Insert / Delete bảng `roles`.
    *   Cập nhật bảng trung gian `role_has_permissions` (Sử dụng hàm `$role->syncPermissions()` của Laravel).
