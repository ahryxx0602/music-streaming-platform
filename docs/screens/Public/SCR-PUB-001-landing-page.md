# [SCR-PUB-001] Landing Page

> [!IMPORTANT]
> **I18N REQUIREMENT:** Tất cả các đoạn Text, Label, Placeholder, Message hiển thị trong tài liệu Screen Specs này khi triển khai vào code thực tế đều **KHÔNG ĐƯỢC HARDCODE**. Bắt buộc phải sử dụng key đa ngôn ngữ qua hàm `$t()` của `vue-i18n`.


> **Mô tả ngắn:** Trang chủ tĩnh giới thiệu về nền tảng Music Streaming Platform, thu hút người dùng đăng ký tài khoản (Listener hoặc Artist). Đây là trang đầu tiên khách vãng lai (Guest) nhìn thấy khi truy cập tên miền gốc mà chưa đăng nhập.

## 1. Thông tin chung (Meta)
*   **Module:** Public / Marketing
*   **Route / URL:** `/`
*   **Layout sử dụng:** `PublicLayout` (Header trong suốt, không có Sidebar).
*   **Quyền truy cập:** `[PER-GUEST]` (Mọi đối tượng).
*   **Component con (Children):**
    *   `HeroSection.vue` (Banner chính + CTA)
    *   `FeaturesSection.vue` (Giới thiệu tính năng nổi bật)
    *   `TrendingPreview.vue` (Hiển thị Top 5 bài hát thịnh hành)
    *   `Footer.vue`

## 2. Thành phần giao diện (UI Elements)

*   **Header (Navbar):**
    *   Logo nền tảng.
    *   Nút `Đăng nhập` (Login).
    *   Nút `Đăng ký miễn phí` (Sign up).
*   **Hero Section:**
    *   Tiêu đề lớn (Headline): "Khám phá thế giới âm nhạc không giới hạn".
    *   Nút CTA: `Nghe ngay` (Chuyển hướng đến Explore Page).
*   **Trending Preview:** 
    *   Slider/Grid hiển thị danh sách bài hát đang hot.
    *   Nút Play (Guest có thể click để nghe preview 30s).
*   **Artist Call-To-Action:**
    *   Section giới thiệu: "Bạn là nghệ sĩ? Khởi nghiệp âm nhạc cùng chúng tôi".
    *   Nút CTA: `Tìm hiểu thêm về Artist`.

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **Lấy danh sách nhạc trending preview:**
    *   `[API-012]` - `GET /api/v1/guest/explore/trending`
*   **Lấy thông tin banner tĩnh:**
    *   `[API-011]` - `GET /api/v1/guest/explore/banners`

## 4. Quy tắc nghiệp vụ (Business Rules)

*   **[RULE-PUB-01] - Chuyển hướng khi đã đăng nhập:** Nếu người dùng đã có Cookie/Session hợp lệ (Listener hoặc Artist) truy cập vào route `/`, hệ thống tự động redirect (chuyển hướng) họ vào trang `/explore` (dành cho Listener) hoặc `/artist/dashboard` (dành cho Artist).
*   **[RULE-PUB-02] - Guest Audio Preview:** Nhấn nút Play ở trang Landing Page sẽ gọi `[API-017]` để lấy đoạn cắt 30s âm thanh, Guest không thể nghe toàn bộ bài hát nếu chưa đăng ký.

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Đọc (Read):** Lấy dữ liệu từ cache Redis (danh sách trending) để tránh hit database liên tục. Không có tương tác Write.
