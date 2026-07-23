# Kế hoạch Frontend: [SCR-ADM-12] Cài đặt Hệ thống (System Settings)

## 1. Tổng quan
Cung cấp một giao diện cho Quản trị viên (Super Admin) để tùy chỉnh các thông số hoạt động của toàn bộ nền tảng (Ví dụ: Tên website, phí bản quyền chia sẻ cho nghệ sĩ, dung lượng upload tối đa...). Giao diện cần trực quan, an toàn và lưu lại lịch sử thay đổi.

## 2. Bố cục Layout (`AdminSettingsView.vue`)
Sử dụng thiết kế **Tab Navigation** bên trái (hoặc thanh ngang trên cùng) để phân chia các nhóm cài đặt:
- **Cài đặt Chung (General)**: Tên website, logo, mô tả, email liên hệ hỗ trợ.
- **Tài chính & Hoa hồng (Finance)**: Tỷ lệ chia sẻ doanh thu cho Nghệ sĩ (ví dụ 70%), Phí đăng ký gói Premium, Giá mua lẻ bài hát.
- **Hệ thống & Lưu trữ (System)**: Giới hạn dung lượng upload file (MB), Cấu hình Storage (S3/Local).
- **Bảo trì (Maintenance)**: Nút bật/tắt chế độ bảo trì toàn trang (Maintenance Mode).

## 3. Chức năng Chi tiết

### 3.1. Nhóm Cài đặt Chung (General)
- Form nhập liệu cho `site_name`, `support_email`, `site_description`.
- Chức năng kéo thả (Drag & Drop) để thay đổi `site_logo` và `favicon`.

### 3.2. Nhóm Tài chính (Finance)
- Slider (Thanh trượt) để chỉnh `artist_revenue_share_percentage` từ 0% đến 100%. (Hiển thị số %).
- Input Number dạng Currency (VND hoặc USD) cho `premium_subscription_price`.

### 3.3. Nhóm Hệ thống (System)
- Input Number cho `max_upload_size_mb`.
- Toggle Switch (Công tắc) để Bật/Tắt tính năng Auto-Approve (Tự động duyệt bài hát khi Upload).

### 3.4. Quản lý Trạng thái Form
- Có chung một nút **"Lưu Cài đặt" (Save Settings)** ở góc phải trên cùng.
- Khi người dùng thay đổi bất kỳ ô input nào, nút "Lưu" sẽ sáng lên.
- Có cơ chế **Warning Modal** (Giống hệt phần Profile) để chặn Admin nếu lỡ tay chuyển tab hoặc thoát trang mà quên bấm Lưu.

## 4. UI/UX Style
- Phong cách OLED Dark Mode.
- Các thẻ Input sử dụng nền đen mờ `bg-black/40` với viền `border-white/10`. Khi focus có viền màu xanh (Blue/Indigo).
- Các Toggle Switches thiết kế mượt mà, đổi màu xanh lá khi Bật.

## 5. Tích hợp API (Dự kiến)
- `GET /api/v1/admin/settings`: Lấy danh sách cấu hình.
- `PUT /api/v1/admin/settings`: Lưu lại mảng cấu hình đã thay đổi.
