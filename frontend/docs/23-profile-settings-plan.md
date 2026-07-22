# Kế hoạch Frontend: [SCR-ART-05] Thiết lập Profile Nghệ sĩ (Profile Settings)

## 1. Mục tiêu Giao diện
Cung cấp một trung tâm điều khiển (Control Center) cho Nghệ sĩ để cập nhật hình ảnh cá nhân, thông tin liên hệ và các liên kết mạng xã hội. Giao diện cần mang lại cảm giác cá nhân hóa cao, dễ sử dụng nhưng vẫn toát lên sự chuyên nghiệp (Glassmorphism & OLED Dark Mode).

## 2. Cấu trúc Layout (`ArtistProfileSettingsView.vue`)
Sử dụng mô hình Layout chia 2 cột truyền thống của trang Settings:
- **Cột Trái (Sidebar Navigation)**: Các tab menu cài đặt (Ví dụ: Thông tin chung, Mạng xã hội, Đổi mật khẩu, Thanh toán).
- **Cột Phải (Content Area)**: Khu vực chứa Form nhập liệu tương ứng với Tab đang chọn.

## 3. Các chức năng chi tiết trong Form

### 3.1. Tab "Thông tin chung" (General Info)
- **Khu vực Avatar & Banner**:
  - Giao diện kéo thả (Drag & Drop) để cập nhật Ảnh đại diện (Avatar). Avatar hiển thị dạng hình tròn lớn, có nút Edit overlay.
  - Tải lên Ảnh bìa (Cover Banner). (Ảnh chữ nhật dài giống Youtube/Twitter).
  - Validation: Kích thước tối đa 5MB, chuẩn JPG/PNG.
- **Form Nhập liệu**:
  - `Stage Name` (Nghệ danh - Input Text, Bắt buộc).
  - `Bio` (Tiểu sử - Textarea, Hỗ trợ nhập tối đa 500 ký tự, có đếm số ký tự).
  - `Contact Email` (Email liên hệ show cho public, Input Text).
- **Hành động**: Nút "Lưu thay đổi" (Save Changes) có trạng thái Loading.

### 3.2. Tab "Mạng xã hội" (Social Links)
- Cho phép nghệ sĩ điền các đường link dẫn đến các nền tảng khác:
  - Input: Instagram URL (Kèm Icon Instagram).
  - Input: Twitter/X URL (Kèm Icon X).
  - Input: YouTube Channel URL (Kèm Icon Youtube).
  - Input: Spotify Artist Link (Kèm Icon Spotify).
- Nút "Cập nhật Liên kết".

## 4. Quản lý trạng thái (State Management)
- Khởi tạo form data bằng cách fetch API `GET /api/v1/artist/profile`.
- Bắt sự kiện thay đổi dữ liệu để đánh dấu biến `isDirty = true` (Chỉ hiện nút Save khi có sự thay đổi).
- Gọi API `PUT /api/v1/artist/profile` khi nhấn Save. Cập nhật lại AuthStore (Pinia) để Avatar trên Header tự động đổi theo mà không cần reload trang.

## 5. UI/UX Highlights
- **Toast Notifications**: Thông báo thành công/lỗi thao tác ở góc màn hình.
- **Unsaved Changes Warning**: Nếu người dùng chuyển trang (qua Router) mà chưa bấm Save, bật Modal cảnh báo "Bạn có thay đổi chưa lưu, bạn có chắc chắn muốn rời đi?".
- **Skeleton Loading**: Khi mới vào trang Settings, hiển thị các khối xám nhấp nháy (Skeleton) trong lúc chờ API trả dữ liệu về.
