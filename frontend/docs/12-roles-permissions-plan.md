# [SCR-ADM-06] Front-End Roles & Permissions Plan

## Tổng quan
Màn hình quản lý Vai trò (Roles) và Quyền (Permissions). Nơi Super Admin có thể tạo ra các nhóm chức danh (ví dụ: Content Moderator, Marketing, Support) và cấp phát chính xác những quyền hạn gì mà nhóm đó được phép thực thi trong CMS.

## Kiến trúc thư mục (Folder Structure)
Sẽ code chủ yếu bên trong Module `admin` của Frontend:

```text
src/modules/admin/
├── stores/
│   └── roleStore.ts
├── views/
│   └── RolesManagement.vue
└── components/
    └── roles/
        ├── RoleList.vue
        └── RoleFormModal.vue
```

## 1. Store: `roleStore.ts` (Pinia)
- **State**:
  - `roles`: Mảng `Role[]` (Kèm theo mảng con `permissions`).
  - `permissions`: Mảng toàn bộ `Permission[]` hệ thống.
  - `isLoading`: `boolean`.
- **Actions**:
  - `fetchRoles()`: Gọi `GET /api/v1/admin/roles`.
  - `fetchPermissions()`: Gọi `GET /api/v1/admin/permissions`.
  - `createRole(payload)`: Gọi `POST /api/v1/admin/roles`.
  - `updateRole(id, payload)`: Gọi `PUT /api/v1/admin/roles/{id}`.
  - `deleteRole(id)`: Gọi `DELETE /api/v1/admin/roles/{id}`.

## 2. Giao diện (UI Design)

### Màn hình chính: `RolesManagement.vue`
- Cấu trúc: 
  - **Header**: Tiêu đề trang, kèm nút "Thêm Vai Trò" (Mở Modal tạo).
  - **Grid/Cards Layout**: Thay vì dùng bảng (Table) khô khan, danh sách Role nên được hiển thị dưới dạng **Grid Cards** (Mỗi Role là một thẻ Card).
    - Trên Card ghi rõ Tên Role.
    - Hiển thị số lượng nhân sự đang mang Role này (Users count).
    - Hiển thị danh sách các quyền (Permissions) dạng Tag/Chip (vd: `[Cấp quyền sửa nhạc]`, `[Xoá User]`). Nếu dài quá thì hiển thị `+3 quyền khác`.
    - Nút Sửa / Xóa trên mỗi Card. (Disable nếu là role hệ thống).

### Component: `RoleFormModal.vue`
- Một Headless UI / Tailwind Modal dùng chung cho cả Create và Update.
- **Form Fields**:
  - **Tên Vai trò**: Input text (vd: "Quản trị viên Nội dung").
  - **Danh sách Quyền (Permissions)**:
    - Hiển thị dưới dạng **Checkbox Grid** hoặc **Switch Toggles** cực kỳ trực quan.
    - Có thể nhóm các quyền theo module (vd: Nhóm Quản lý Nhạc, Nhóm Quản lý User) nếu số lượng quyền nhiều.
- **Xử lý Logic**:
  - Truyền vào `roleData` (nếu là Edit) để binding sẵn tên và mảng permissions đang có.
  - Bấm Save -> Gọi Action tạo/sửa -> Báo Toast -> Reload lại danh sách Roles.

## 3. Đa ngôn ngữ (i18n)
Dưới namespace `admin.roles`:
- `admin.roles.title`
- `admin.roles.create_new`
- `admin.roles.system_role_warning`
- `admin.roles.users_count`
- `admin.roles.form.name_label`
- `admin.roles.form.permissions_label`
