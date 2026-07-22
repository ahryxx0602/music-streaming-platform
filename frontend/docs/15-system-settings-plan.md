# Kế hoạch Triển khai Frontend: [SCR-ADM-12] Cài đặt Hệ thống (System Settings)

## 1. Mục tiêu
Xây dựng giao diện cho phép Super Admin cấu hình các tham số toàn cục của hệ thống (Tên website, chính sách chia tiền, chế độ bảo trì...). Giao diện cần trực quan, tuân thủ chặt chẽ Design System 3-Layer Token.

## 2. Giao diện chính (System Settings View)
- **Đường dẫn (Route)**: `/admin/settings`
- **File**: `frontend/src/views/admin/settings/SystemSettingsView.vue`
- Bố cục: Sử dụng Layout 2 cột. Cột trái là Menu điều hướng nội bộ (Tabs dọc), Cột phải là Form cấu hình tương ứng.

### 2.1. Cấu trúc Tabs:
**A. Thông tin chung (General)**
- Tên nền tảng (Site Name).
- Email Hỗ trợ (Support Email).
- Khẩu hiệu (Slogan).

**B. Tài chính & Nghệ sĩ (Monetization)**
- Tỉ lệ chia sẻ doanh thu cho Nghệ sĩ (Artist Revenue Share %) - Ví dụ: 70%.
- Ngưỡng rút tiền tối thiểu (Payout Threshold $) - Ví dụ: 50.
- (Tùy chọn) Phí nền tảng (Platform Fee).

**C. Hệ thống & Kỹ thuật (System)**
- Chế độ bảo trì (Maintenance Mode) - Dạng Toggle switch.
- Kích thước Upload tối đa (Max Upload Size) - VD: 20MB.

## 3. Quản lý Trạng thái (Store)
- **File**: `frontend/src/stores/settingStore.ts` (Dùng Pinia).
- **Trạng thái**:
  - `settings`: Một object chứa các cặp key-value. `ref({})`.
  - `loading`: Trạng thái call API.
  - `isSaving`: Trạng thái khi ấn nút Lưu.
- **Hành động (Actions)**:
  - `fetchSettings()`: Gọi `GET /api/v1/admin/settings` và gán vào `settings`.
  - `updateSettings(payload)`: Gọi `PUT /api/v1/admin/settings` để cập nhật một loạt thay đổi cùng lúc.

## 4. Nhiệm vụ của Frontend Agent
1. Tạo Store `settingStore.ts` và tích hợp call API (Axios).
2. Xây dựng trang `SystemSettingsView.vue`.
3. Sử dụng các component dùng chung hiện có:
   - `BaseAdminInput`: Dành cho text, number.
   - `BaseAdminButton`: Cho nút Save.
   - `StatusBadge` (hoặc tạo một Toggle UI đơn giản) cho chế độ Bảo trì.
4. Xử lý logic Form: Gộp dữ liệu từ các tab lại thành một object `payload` và gọi API `updateSettings` khi nhấn Lưu. Thêm Toast Notification thông báo thành công.
5. Cập nhật `router/index.ts` và check lại item trên Sidebar.
