# Kế hoạch Refactor - Quản lý Người dùng (Frontend)

Dựa trên tài liệu đặc tả [SCR-ADM-03](../../docs/screens/System/SCR-ADM-03-user-management.md) và đợt review code vừa qua, chúng ta cần tái cấu trúc (Refactor) lại giao diện Quản lý Người dùng (`UsersView.vue`) để đảm bảo đáp ứng chính xác các yêu cầu nghiệp vụ khắt khe của hệ thống đa vai trò.

## 1. Mục tiêu tái cấu trúc
- **Tách biệt hiển thị:** Không dùng một bảng chung (Generic Table) cho tất cả Role. Mỗi Role (Listener, Artist, Staff) sở hữu những cột dữ liệu và logic hoàn toàn khác nhau.
- **Tuân thủ UI/UX Đặc tả:** Tạo giao diện 3 Tab độc lập.
- **Tích hợp tính năng:** Đưa các tính năng Khóa tài khoản, Tạo nhanh Artist và Phân quyền Staff vào hoạt động thực tế.

## 2. Kiến trúc Component mới

Cấu trúc thư mục `src/views/admin/users/` sẽ được chia nhỏ như sau:

```text
src/views/admin/users/
├── UsersView.vue             (Component Cha: Quản lý Header và 3 Tabs)
├── components/
│   ├── tabs/
│   │   ├── ListenerTab.vue   (Bảng dữ liệu riêng cho Listener)
│   │   ├── ArtistTab.vue     (Bảng dữ liệu riêng cho Artist)
│   │   └── StaffTab.vue      (Bảng dữ liệu riêng cho Admin/Staff)
│   ├── modals/
│   │   ├── CreateArtistModal.vue  (Form tạo nhanh Nghệ sĩ)
│   │   ├── ConfirmStatusModal.vue (Cảnh báo khi Ban/Suspend user)
│   │   └── AssignRoleModal.vue    (Form cấp quyền cho Staff)
```

## 3. Chi tiết triển khai từng Component

### 3.1. UsersView.vue (Parent Container)
- **Nhiệm vụ:** Layout vỏ bọc, chứa tiêu đề "Quản lý Tài khoản" và thanh điều hướng Tabs (Listener | Artist | Admin).
- **Logic:** Quản lý state `activeTab` (`'listener'`, `'artist'`, `'admin'`). Truyền component động bằng `<component :is="activeTabComponent">`.

### 3.2. ListenerTab.vue
- **UI Bảng:** Cột: Avatar + Tên, Email, Trạng thái (Active/Banned), Ngày tham gia.
- **Hành động (Actions):** Nút Khóa tài khoản (hiển thị popup đỏ cảnh báo). Nút Reset Mật Khẩu (gọi API gửi mail).
- **API sử dụng:** `GET /admin/users?filter[role]=listener`

### 3.3. ArtistTab.vue
- **UI Bảng:** Cột: Avatar + Nghệ danh (Stage Name), Email, Tổng bài hát, Tổng Streams, Trạng thái.
- **Nút Đặc biệt:** Nút `+ Tạo nhanh Artist` (mở `CreateArtistModal.vue`).
- **Hành động:** Xem chi tiết Profile, Khóa (Suspend) Nghệ sĩ (Lưu ý Event: Ẩn bài hát ở FE).
- **API sử dụng:** `GET /admin/users?filter[role]=artist`

### 3.4. StaffTab.vue
- **UI Bảng:** Cột: Tên nhân viên, Email, Vai trò phân quyền (Badge màu đặc biệt), Lần đăng nhập cuối.
- **Hành động:** Nút Đổi Quyền (Assign Role) mở `AssignRoleModal.vue`, Nút Thu hồi (Revoke).
- **Bảo mật:** Chỉ user có quyền `Super Admin` mới nhìn thấy Tab này.
- **API sử dụng:** `GET /admin/users?filter[role]=admin`

## 4. Tích hợp API mới
Sử dụng các API chuẩn hóa được đặc tả trong tài liệu [API-03-UserManagement](../../backend/docs/API-03-UserManagement.md):
- Tích hợp gọi hàm `storeArtist` khi Submit form `CreateArtistModal`.
- Tích hợp gọi hàm `updateStatus` với `PUT /api/v1/admin/users/:id/status`. Cập nhật trạng thái trực tiếp trên mảng Vue thay vì fetch lại toàn bộ danh sách để tối ưu hiệu năng.

## 5. Các bước thực hiện
1. **Bước 1:** Di chuyển toàn bộ code HTML/Logic của `UsersView.vue` hiện tại vào `ListenerTab.vue` (để tận dụng lại Base).
2. **Bước 2:** Cập nhật lại `UsersView.vue` thành bộ khung Tabs rỗng.
3. **Bước 3:** Clone `ListenerTab.vue` sang `ArtistTab.vue` và tinh chỉnh lại các Cột hiển thị (Thêm Tổng bài hát).
4. **Bước 4:** Xây dựng `CreateArtistModal.vue` và viết code Submit form.
5. **Bước 5:** Xây dựng tính năng Ban/Suspend thông qua Context Menu.
