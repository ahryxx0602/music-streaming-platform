# [SCR-ADM-09] Dashboard Tổng quan & Thống kê (Analytics)

> **Mô tả ngắn:** Màn hình đầu tiên mà Admin nhìn thấy khi đăng nhập. Cung cấp bức tranh toàn cảnh về sức khỏe của nền tảng (Lượt nghe, User mới, Doanh thu), đồng thời tích hợp công cụ tra cứu số liệu chi tiết của từng Artist.

## 1. Thông tin chung (Meta)
*   **Module:** System / Analytics
*   **Route / URL:** `/admin/dashboard`
*   **Layout sử dụng:** `AdminLayout.vue`
*   **Quyền truy cập:** `[PER-ADMIN]` (Có thể ẩn hiện một số biểu đồ tùy theo Role con, ví dụ Role Finance mới xem được Doanh thu).
*   **Component con (Children):**
    *   `StatWidgets.vue` (Các thẻ thống kê tổng quan: Tổng User, Lượt Stream, Doanh thu).
    *   `LineChartOverview.vue` (Biểu đồ tăng trưởng 30 ngày).
    *   `ArtistLookupTool.vue` (Form tìm kiếm để tra cứu số liệu riêng của một Artist).

## 2. Thành phần giao diện (UI Elements)
*   **Khu vực Widgets Tổng quan (Top Section):**
    *   `Thẻ Thống kê (Cards)`: Hiển thị con số nổi bật kèm % tăng/giảm so với tháng trước (Ví dụ: 1.2M Streams, 500 New Users).
*   **Khu vực Biểu đồ (Charts):**
    *   `Biểu đồ đường (Line Chart)`: Trục X là ngày, Trục Y là số lượng Stream.
    *   `Bộ lọc Chart`: Dropdown (7 ngày, 30 ngày, Năm nay).
*   **Khu vực Tra cứu Nghệ sĩ (View Artist Analytics - FEAT-013):**
    *   `Input Tìm kiếm`: Nhập tên Nghệ danh hoặc Email của Artist.
    *   `Bảng Kết quả`: Khi chọn 1 Artist, hiển thị nhanh Báo cáo của riêng Artist đó (Tổng số bài hát, Bài hát nào nghe nhiều nhất, Tổng tiền bản quyền dự kiến).
*   **Khu vực Báo cáo/Cảnh báo (Alerts/Reports):**
    *   `Danh sách Comment bị Report`: Bảng nhỏ hiển thị các Comment bị hệ thống hoặc User đánh cờ vi phạm, kèm nút `Xóa nhanh (Soft Delete)`.

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **Lấy dữ liệu (Fetch):**
    *   `[API-310]` - `GET /api/v1/admin/analytics/overview?range=30d` (Lấy dữ liệu cho Widget và Biểu đồ toàn hệ thống).
    *   `[API-ADM-24]` - `GET /api/v1/admin/analytics/artists/{id}` (Lấy dữ liệu chi tiết của một Artist cụ thể).
*   **Ghi dữ liệu (Mutations):**
    *   `[API-420]` - `DELETE /api/v1/admin/comments/{id}` (Xóa bình luận vi phạm).

### State Management (Pinia)
*   **Store:** `useDashboardStore.js`
*   **Actions:** `fetchOverview()`, `lookupArtist(id)`.

## 4. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-ADM-DASH-01] - Cache Heavy Queries:** Các con số thống kê Tổng ở `[API-310]` (ví dụ SUM tất cả các luồng stream của cả nền tảng) là những truy vấn (Queries) cực kỳ nặng. Backend BẮT BUỘC phải tính toán thông qua Cronjob chạy hàng đêm và lưu kết quả vào Redis hoặc bảng Summary. Không được dùng `COUNT()` hay `SUM()` trực tiếp trên bảng `[DB-streams]` mỗi lần load trang để tránh làm sập DB.
*   **[RULE-ADM-DASH-02] - Giới hạn Truy cập Dữ liệu Tài chính:** Cột "Doanh thu / Tiền bản quyền" trong Dashboard và trong bảng tra cứu Artist chỉ được phép hiển thị cho Super Admin hoặc nhân viên có Role `Finance`. Nếu là Moderator thông thường, Frontend phải ẩn các Widget này đi.
*   **[RULE-ADM-DASH-03] - Công thức tính Tiền bản quyền (Royalty Calculation):** Doanh thu hiển thị không phải con số Hardcode mà được tính ngầm dựa trên công thức: `Tổng tiền = Tổng số lượt Streams (Chỉ tính stream quá 30s) x Đơn giá Payout`. Đơn giá này (VD: 0.005$/stream) không được fix cứng trong code mà phải lấy từ `[DB-settings]` hoặc biến môi trường để linh hoạt thay đổi theo từng tháng.

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Đọc (Read):** Đọc từ Redis Cache (chứa kết quả tính toán sẵn).
*   **Ghi (Write):** Cập nhật `deleted_at` cho bảng `[DB-comments]` khi Xóa bình luận vi phạm.
