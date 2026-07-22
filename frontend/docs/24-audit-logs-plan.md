# Kế hoạch Frontend: [SCR-ADM-08] Nhật ký Hệ thống (Audit Logs)

## 1. Mục tiêu
Xây dựng một màn hình giám sát trung tâm (Monitoring Dashboard) cho phép Admin theo dõi mọi hoạt động đang diễn ra trên hệ thống theo thời gian thực (hoặc gần như thời gian thực). Giao diện cần trực quan, dễ lọc và tìm kiếm dữ liệu.

## 2. Bố cục Layout (`AdminAuditLogView.vue`)
Kế thừa cấu trúc giao diện Admin của hệ thống:
- **Header**: Thanh công cụ tìm kiếm và các bộ lọc (Filters).
- **Body**: Bảng dữ liệu (Data Table) hiển thị chi tiết các dòng sự kiện (Logs).
- **Pagination**: Phân trang ở cuối bảng (Vì lượng Log thường rất lớn, bắt buộc phải dùng Server-side Pagination).

## 3. Chức năng Chi tiết

### 3.1. Thanh Công Cụ (Toolbar)
- **Ô Tìm kiếm (Search Box)**: Tìm kiếm theo IP, ID người dùng, hoặc keyword trong mô tả log.
- **Bộ lọc (Filter dropdowns)**:
  - **Theo Loại hành động (Action Type)**: `Create`, `Update`, `Delete`, `Login`, `Failed Login`.
  - **Theo Model/Phân hệ**: `User`, `Song`, `Album`, `System`.
- **Bộ lọc Thời gian (Date Picker)**: Lọc log từ ngày A đến ngày B.

### 3.2. Bảng Dữ Liệu Logs (Data Table)
Các cột hiển thị:
- **Timestamp**: Thời gian xảy ra (Format: `DD/MM/YYYY HH:mm:ss`). Hiển thị highlight đỏ nếu log vừa xảy ra trong vòng 5 phút.
- **User**: Hiển thị ID và Tên của người thực hiện. (Nếu là Guest/Hacker thì hiển thị "Khách" hoặc "Hệ thống").
- **Hành động (Action)**: Thẻ Badge có màu (Create - Xanh lá, Update - Xanh dương, Delete - Đỏ, Login - Tím).
- **Phân hệ (Resource)**: Ví dụ "Song #105", "User #2".
- **Địa chỉ IP (IP Address)**: Kèm biểu tượng lá cờ quốc gia (nếu có thư viện hỗ trợ) hoặc tooltip.
- **Thao tác**: Nút "Xem chi tiết" (Icon Con mắt).

### 3.3. Modal Chi Tiết Log (Log Detail Modal)
Khi Admin bấm "Xem chi tiết":
- Hiển thị cửa sổ Modal.
- Cung cấp **Before/After JSON Comparison**: 
  - Hiển thị cục JSON của trường `old_values` và `new_values`.
  - Thiết kế tương tự như Git Diff (Dòng màu đỏ gạch đi cho dữ liệu cũ, dòng màu xanh lá cho dữ liệu mới) để Admin biết chính xác field nào bị thay đổi.

## 4. UI/UX Style
- Tiếp tục duy trì phong cách **OLED Dark Mode**.
- Bảng dữ liệu có viền mờ `border-white/5` và hiệu ứng hover từng dòng (Row Highlight).
- Font chữ: `Inter` (hoặc `Fira Code` cho đoạn hiển thị JSON để nhìn chuẩn kỹ thuật).

## 5. Tích hợp API (Dự kiến)
- `GET /api/v1/admin/audit-logs`: (Hỗ trợ query params: `search`, `action`, `resource`, `start_date`, `end_date`, `page`).
- `GET /api/v1/admin/audit-logs/{id}`: Xem chi tiết payload JSON.
