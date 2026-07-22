# [SCR-ADM-02] Front-End Artist Invites Plan

## Tổng quan
Màn hình quản lý Mã Mời (Artist Invitations) nằm trong khu vực Administration dành cho Admin. Tính năng này cho phép Admin theo dõi danh sách mã mời đã phát hành, tạo mới mã mời cho nghệ sĩ, và sao chép đường link đăng ký gửi cho họ. Nếu phát hiện mã bị rò rỉ, Admin có thể thu hồi (Revoke).

## Kiến trúc thư mục (Folder Structure)
Sẽ code chủ yếu bên trong Module `admin` của Frontend:

```text
src/modules/admin/
├── stores/
│   └── artistInviteStore.ts
├── views/
│   └── ArtistInvitesManagement.vue
└── components/
    └── artist-invites/
        ├── InviteList.vue
        └── CreateInviteModal.vue
```

## 1. Store: `artistInviteStore.ts` (Pinia)
- **State**:
  - `invites`: Mảng `ArtistInvitation[]`.
  - `pagination`: Thông tin phân trang (current_page, last_page, total).
  - `isLoading`: `boolean`.
- **Actions**:
  - `fetchInvites(page = 1, filters = {})`: Gọi `GET /api/v1/admin/artist-invites`.
  - `createInvite(payload)`: Gọi `POST /api/v1/admin/artist-invites`. Trả về `registration_url` để Frontend hiển thị hoặc copy ngay.
  - `revokeInvite(id)`: Gọi `DELETE /api/v1/admin/artist-invites/{id}`. Sau khi xóa thành công sẽ gọi lại `fetchInvites()` để cập nhật List.

## 2. Giao diện (UI Design)

### Màn hình chính: `ArtistInvitesManagement.vue`
- Cấu trúc: 
  - **Header**: Tiêu đề trang, kèm nút "Tạo Mã Mới" (Mở Modal).
  - **Table/List**: Hiển thị danh sách Invite theo phong cách thiết kế hiện đại, responsive. (Sử dụng `InviteList.vue`).
  - **Pagination**: Thành phần chuyển trang dưới cùng.

### Component: `InviteList.vue`
Hiển thị danh sách các mã mời với các cột chính:
1. **Email / Tên gợi nhớ**: Nếu Null thì hiện chữ "N/A" mờ.
2. **Registration Link (Token)**:
   - Dùng ô Input (Readonly) hoặc Khung Text ngắn gọn kèm nút **Icon Copy**.
   - Khi click nút "Copy", sử dụng `navigator.clipboard.writeText()` sao chép URL và hiện Toast "Đã chép vào bộ nhớ tạm".
3. **Người tạo**: Hiển thị Tên Admin (từ `created_by_user.name`).
4. **Hết hạn lúc (Expires At)**: Format ngày tháng dễ đọc (`dd/MM/yyyy HH:mm`).
5. **Trạng thái (Status Badge)**:
   - 🟢 `Valid`: Hiển thị "Có hiệu lực" - xanh lá.
   - 🔴 `Expired`: Hiển thị "Hết hạn" - đỏ/cam.
   - 🔵 `Used`: Hiển thị "Đã sử dụng" - xanh lam (đi kèm thời gian `used_at`).
6. **Thao tác (Actions)**:
   - Nút **Revoke** (Xóa): Chỉ hiển thị/enable khi Status != `Used`. Hiển thị icon thùng rác, bấm vào hỏi xác nhận (Confirm) trước khi xóa.

### Component: `CreateInviteModal.vue`
- Một Headless UI / Tailwind Modal.
- **Form Fields**:
  - **Email (Optional)**: Input text, placeholder "artist@example.com".
  - **Thời hạn (Validity)**: Select box/Radio buttons cho phép chọn nhanh: "24 Giờ", "7 Ngày", "30 Ngày" (Tương ứng value `1`, `7`, `30`).
- **Nút Submit**: "Tạo mã & Copy Link".
- **Luồng xử lý**: 
  1. Submit form qua Action Store `createInvite`.
  2. Thành công -> Đóng Modal -> Tự động bật hàm `navigator.clipboard.writeText(registration_url)` -> Hiện Toast báo "Mã mời đã được tạo và chép link!".
  3. Reload lại danh sách.

## 3. Đa ngôn ngữ (i18n)
Toàn bộ Text phải được wrap trong thẻ `$t()` hoặc hàm `t()` của `vue-i18n`. Các key chuẩn bị sẵn trong file `vi.json`/`en.json` nhánh `admin.invites`:
- `admin.invites.title`
- `admin.invites.create_new`
- `admin.invites.status.valid`, `.expired`, `.used`
- `admin.invites.messages.copied_success`
- `admin.invites.messages.revoke_confirm`
