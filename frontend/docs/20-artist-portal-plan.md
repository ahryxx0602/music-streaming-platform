# Kiến trúc Frontend: Phân hệ Nghệ sĩ (Artist Workspace)

## 1. Tổng quan (Overview)
Artist Portal là nơi các Nghệ sĩ (Artists) có thể đăng tải nhạc, quản lý album, xem thống kê lượt nghe và doanh thu. 
Về mặt thiết kế (UI/UX), Artist Portal cần một ngôn ngữ thiết kế **Khác biệt** so với Admin Portal.
- Admin Portal: Dùng `admin.css`, thiên về quản trị, form mẫu cứng cáp, tông màu xanh lam (Primary).
- Artist Portal: Dùng chung `app.css` với người nghe nhạc (Listener), mang phong cách **Glassmorphism**, hiện đại, phóng khoáng, tông màu chủ đạo có thể là Tím Neon (Purple Neon) kết hợp Dark Mode xuyên suốt.

## 2. Cấu trúc Layout (ArtistLayout.vue)
Cần tạo một layout riêng cho Nghệ sĩ: `frontend/src/components/artist/layout/ArtistLayout.vue`.
- **Sidebar**: Chứa menu điều hướng (Tổng quan, Tải nhạc lên, Quản lý Nhạc & Album, Thống kê, Hồ sơ).
- **Header**: Chứa Avatar, Tên nghệ sĩ, Dropdown Đăng xuất, và Nút "Tải nhạc lên" (Call-to-Action) nổi bật.
- **Main Content**: Chứa nội dung chính của các màn hình qua `<router-view>`.

## 3. Màn hình đầu tiên: [SCR-ART-02] Dashboard Nghệ sĩ
### 3.1 Giao diện (ArtistDashboardView.vue)
- Khối "Chào mừng": Hiển thị "Chào mừng trở lại, {stage_name}!"
- Khối "Tổng quan nhanh" (Stats Cards):
  - Tổng số người theo dõi (Followers).
  - Tổng lượt nghe (Total Streams).
  - Số dư hiện tại (Balance / Revenue).
  - Tổng số bài hát (Total Tracks).
- Khối "Hiệu suất bài hát" (Chart):
  - Biểu đồ line (hoặc bar) hiển thị lượt nghe trong 30 ngày qua bằng Chart.js.
- Khối "Bài hát nổi bật" (Top Tracks):
  - Bảng xếp hạng mini 5 bài hát có lượt nghe cao nhất của nghệ sĩ này.

### 3.2 State Management (Pinia)
- File: `frontend/src/stores/artistStore.ts`
- Cần có hàm `fetchDashboardStats()` gọi API backend để lấy toàn bộ dữ liệu trên.

## 4. Nhiệm vụ của Frontend Agent
1. Tạo thư mục `frontend/src/components/artist` và `frontend/src/views/artist`.
2. Code `ArtistSidebar.vue`, `ArtistHeader.vue`, `ArtistLayout.vue` áp dụng phong cách Glassmorphism.
3. Code `ArtistDashboardView.vue` với các Chart và Stats tĩnh (Mock data tạm) hoặc tích hợp API.
4. Cập nhật `router/index.ts`: Thêm `path: '/artist'` được bọc bởi `ArtistLayout` và trỏ vào Dashboard.
5. Cập nhật `authStore` để tự động điều hướng: Nếu Role = `artist`, chuyển hướng về `/artist/dashboard`.
