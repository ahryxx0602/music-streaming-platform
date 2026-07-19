# [UI-CMP-01] Sidebar (Thanh Điều Hướng Trái)

> [!IMPORTANT]
> **I18N REQUIREMENT:** Tất cả các đoạn Text, Label, Placeholder, Message hiển thị trong tài liệu Screen Specs này khi triển khai vào code thực tế đều **KHÔNG ĐƯỢC HARDCODE**. Bắt buộc phải sử dụng key đa ngôn ngữ qua hàm `$t()` của `vue-i18n`.


> **Mô tả ngắn:** Thanh điều hướng chính của ứng dụng, nằm bên trái màn hình. Menu sẽ thay đổi linh hoạt (Dynamic Rendering) tùy thuộc vào `role` của người dùng (Guest, Listener, Artist).

## 1. Thông tin chung
- **Vị trí:** Cạnh trái của `[LYT-01]` MainLayout.
- **Kích thước:** `width: 280px`.
- **Trạng thái (State):** Có thể mở/đóng (Collapsed/Expanded) trên Mobile.

## 2. Thành phần giao diện (UI Elements)
- **Logo Area:** Nằm trên cùng, hiển thị Logo "Music Streaming" (Click vào quay về `/`).
- **Main Menu (Nhóm 1 - Khám phá):**
  - Trang chủ (Home / Khám phá)
  - Tìm kiếm (Search)
  - Thư viện của tôi (Your Library) - *Yêu cầu Login*
- **User Specific Menu (Nhóm 2):**
  - **Nếu là Guest/Listener:** Hiển thị "Tạo Playlist mới", "Bài hát đã thích (Liked Songs)".
  - **Nếu là Artist:** Hiển thị "Quản lý bài hát (My Tracks)", "Thống kê (Analytics)", "Đăng tải (Upload)".
- **Footer Area:** Nằm dưới cùng của Sidebar, chứa các link Tải App, Chính sách bảo mật, và Ngôn ngữ.

## 3. Liên kết dữ liệu & Logic (State)
- **Store:** Đọc state `authStore.role` và `authStore.isAuthenticated` từ Pinia để quyết định render nhóm menu nào.
- **Active Route:** Các thẻ link (Vue Router `<router-link>`) cần có class `.router-link-active` với chữ màu `var(--color-primary)` và icon đậm hơn khi đang ở đúng trang đó.
- **Kéo thả (Drag & Drop):** Hỗ trợ kéo thả bài hát từ Content Area thả vào các Playlist nằm trên Sidebar (Tương lai).

## 4. Tác động CSS (Aesthetics)
- Nền: `var(--color-bg-dark)` (Gần như trùng với màu nền chính, phân biệt bằng border mờ hoặc shadow).
- Hiệu ứng Hover vào menu item: Text sáng lên, có nền mờ nhỏ.
- Icon: Sử dụng `@tabler/icons-vue`, kích thước đồng nhất `24px`.
