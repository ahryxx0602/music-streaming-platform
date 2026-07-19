# [SCR-ART-02] Dashboard Phân tích Nghệ sĩ (Artist Analytics Dashboard)

> [!IMPORTANT]
> **I18N REQUIREMENT:** Tất cả các đoạn Text, Label, Placeholder, Message hiển thị trong tài liệu Screen Specs này khi triển khai vào code thực tế đều **KHÔNG ĐƯỢC HARDCODE**. Bắt buộc phải sử dụng key đa ngôn ngữ qua hàm `$t()` của `vue-i18n`.


> **Mô tả ngắn:** Màn hình đầu tiên (Homepage) của phân hệ Artist Workspace. Cung cấp cái nhìn tổng quan về hiệu suất của nghệ sĩ thông qua các số liệu thống kê (Lượt nghe, Số người theo dõi mới, Doanh thu tạm tính).

## 1. Thông tin chung (Meta)
*   **Module:** Artist Workspace / Analytics
*   **Route / URL:** `/artist/dashboard`
*   **Layout sử dụng:** `ArtistLayout.vue` (Giao diện mang tính sáng tạo, Dark mode mặc định).
*   **Quyền truy cập:** `[PER-ARTIST]`
*   **Component con (Children):**
    *   `ArtistStatWidgets.vue` (Các khối hiển thị số liệu nổi bật).
    *   `ArtistStreamChart.vue` (Biểu đồ tăng trưởng lượt nghe).
    *   `TopTracksList.vue` (Bảng xếp hạng 5 bài hát hot nhất của Nghệ sĩ).

## 2. Thành phần giao diện (UI Elements)
*   **Khu vực Widgets Thống kê (Overview Cards):**
    *   `Tổng lượt Streams`: Lượt nghe hợp lệ trong 30 ngày qua (Có màu xanh lá biểu thị tăng trưởng so với tháng trước).
    *   `Followers mới`: Số lượng User vừa bấm Follow nghệ sĩ này.
    *   `Doanh thu ước tính`: Tính dựa trên tổng Streams x Đơn giá (Tùy chọn hiển thị).
*   **Khu vực Biểu đồ Tăng trưởng (Line Chart):**
    *   Trục X: Thời gian (Theo ngày).
    *   Trục Y: Số lượt stream.
    *   Bộ lọc nhanh: 7 Ngày, 30 Ngày, Năm nay.
*   **Khu vực Top 5 Bài Hát (Top Performing Tracks):**
    *   Danh sách ngắn hiển thị 5 bài hát có lượt nghe cao nhất của Artist này trong khoảng thời gian đang lọc. Cột hiển thị: Ảnh bìa mini, Tên bài, Số lượt Stream.

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **Lấy dữ liệu (Fetch):**
    *   `[API-240]` - `GET /api/v1/artist/analytics/overview?range=30d` (Trả về cục Data gồm số tổng, mảng chart và mảng top 5 tracks).

### State Management (Pinia)
*   **Store:** `useArtistAnalyticsStore.ts`
*   **Actions:** `fetchOverview(range)`

## 4. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-ART-DASH-01] - Giới hạn Dữ liệu (Data Isolation):** Ở tầng Backend (Controller/Service), API `[API-240]` BẮT BUỘC phải lấy `artist_id` từ Token đăng nhập (Context/Session) của User hiện tại. Tuyệt đối không cho phép truyền `artist_id` qua URL hay Payload để tránh việc Artist này xem lén doanh thu của Artist khác (IDOR Vulnerability).
*   **[RULE-ART-DASH-02] - Bộ đệm Số liệu (Data Caching):** Giống với Admin Dashboard, việc đếm tổng Stream của một Artist trong bảng `[DB-streams]` là thao tác tốn tài nguyên. Dữ liệu này phải được Cronjob chạy đêm tổng hợp lại và lưu vào Redis. Số liệu trên màn hình này chấp nhận độ trễ (Delay) 24 giờ.

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Đọc (Read):** Lấy dữ liệu từ Redis Cache hoặc bảng Summary trung gian (Không truy vấn trực tiếp bảng `streams`).
*   **Ghi (Write):** Không.
