# Kế hoạch Triển khai Frontend: [SCR-ADM-06] Phân quyền Quản trị (Roles & Permissions)

## 1. Mục tiêu
Hoàn thiện giao diện Quản lý Vai trò (Roles Management). Giao diện này cho phép Super Admin tạo ra các nhóm chức danh (ví dụ: "Content Moderator", "Customer Support") và tích chọn các quyền tương ứng bằng Checkbox.

## 2. Giao diện (RolesManagementView.vue)
- **File**: `frontend/src/views/admin/roles/RolesManagement.vue` (Hoặc tạo mới nếu chưa đầy đủ).
- **Cấu trúc UI**:
  1. **Danh sách Roles**: Render dạng Grid (Card) hoặc Table. Hiển thị Tên Role, Số lượng nhân viên đang sở hữu, và 1-2 dòng tóm tắt quyền hạn.
  2. **Modal Thêm/Sửa Role (`RoleFormModal.vue`)**:
     - Input: Tên Vai trò (Role Name).
     - Bảng Checkbox Ma trận Quyền (Permissions Matrix): Trình bày các quyền theo từng nhóm (Module) cho dễ nhìn.

### 2.1 Cấu trúc Ma trận Quyền trên UI (Nên nhóm lại để hiển thị):
Khi gọi API `GET /api/v1/admin/permissions`, FE sẽ nhận được 14 permissions. Cần dùng JS để group chúng lại trên UI:
- **Nhóm Hệ thống**: `view_dashboard`, `view_audit_logs`, `manage_settings`, `manage_roles`.
- **Nhóm Người dùng**: `view_users`, `manage_users`, `delete_users`, `manage_invites`.
- **Nhóm Kho Nhạc**: `view_inventory`, `manage_inventory`, `moderate_songs`.
- **Nhóm Nội dung**: `manage_genres`, `manage_playlists`, `manage_banners`.

Giao diện form gán quyền sẽ có nút "Chọn tất cả" ở mỗi nhóm.

## 3. UI Guard (Ẩn/Hiện Menu theo quyền)
- Để hoàn thiện 100% tính năng phân quyền ở Frontend, ta cần bọc các router link trong `AdminSidebar.vue` bằng các điều kiện `v-if="hasPermission('tên_quyền')"`.
- **Yêu cầu**: `authStore` hoặc `userStore` phải lưu trữ mảng permissions của người dùng hiện tại (lấy từ API login/me). Frontend Agent cần viết composable `usePermissions()` hoặc hàm `hasPermission()` để dùng toàn cục.

## 4. Nhiệm vụ của Frontend Agent
1. Dựng (hoặc refactor) trang `RolesManagement.vue` thành giao diện thẻ Card (hoặc Table) chuẩn Design System.
2. Thiết kế `RoleFormModal.vue` hiển thị Ma trận quyền (Group bằng Checkbox).
3. Connect Pinia Store (`roleStore.ts` đã có chưa?) với các API.
4. Xây dựng logic `hasPermission(permissionName)` để ẩn/hiện Sidebar Navigation.
5. Push code cẩn thận, test UX kĩ.
