# [SCR-LST-06] Tìm kiếm & Khám phá (Search & Filter)

> [!IMPORTANT]
> **I18N REQUIREMENT:** Tất cả các đoạn Text, Label, Placeholder, Message hiển thị trong tài liệu Screen Specs này khi triển khai vào code thực tế đều **KHÔNG ĐƯỢC HARDCODE**. Bắt buộc phải sử dụng key đa ngôn ngữ qua hàm `$t()` của `vue-i18n`.


> **Mô tả ngắn:** Công cụ tìm kiếm toàn cục của hệ thống, giúp người dùng tra cứu nhanh bài hát, nghệ sĩ, hoặc album thông qua từ khóa.

## 1. Thông tin chung (Meta)
*   **Module:** Listener / Discovery
*   **Route / URL:** `/search`
*   **Layout sử dụng:** `MainLayout.vue`
*   **Quyền truy cập:** `[PER-GUEST]` hoặc `[PER-AUTH]`.

## 2. Thành phần giao diện (UI Elements)
*   **Ô Nhập Từ Khóa (Search Input):** Nằm to bản ở giữa trang, hoặc đặt trên Header. Khuyến khích người dùng nhập tên bài, lời bài hát, hoặc tên ca sĩ.
*   **Màn hình Trống (Empty State):** Khi chưa gõ gì, hiển thị các Thể loại (Genres) nổi bật dưới dạng các Card màu sắc (VD: Pop, Ballad, Rap Việt, EDM). Bấm vào Card sẽ nhảy ra danh sách nhạc thuộc Thể loại đó.
*   **Màn hình Kết quả (Results State):**
    *   Chia làm các khối độc lập (Sections): 
        *   **Top Result (Kết quả hàng đầu):** Đề xuất phù hợp nhất (Có thể là 1 Nghệ sĩ hoặc 1 Bài hát). Avatar/Cover to hơn bình thường.
        *   **Bài hát (Songs):** Danh sách max 5 bài. Có nút "Xem tất cả".
        *   **Nghệ sĩ (Artists):** Dạng lưới Avatar tròn.
        *   **Album/Playlists:** Dạng lưới ô vuông.

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **Lấy dữ liệu (Fetch):**
    *   `[API-015]` - `GET /api/v1/search?q={keyword}&type=all`
    *   `[API-014]` - `GET /api/v1/genres` (Lấy danh sách các thẻ Thể loại màu sắc).

## 4. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-SCH-01] - Debounce (Chống Spam API):** Ô Input Search BẮT BUỘC phải áp dụng kỹ thuật Debounce (Độ trễ) khoảng `500ms`. Tránh tình trạng người dùng gõ chữ "S", "ơ", "n", " ", "T" mà Frontend bắn lên 5 cái Request liên tục làm sập Backend. Chỉ bắn API khi người dùng ngừng gõ sau 0.5 giây.
*   **[RULE-SCH-02] - Bộ lọc Trạng thái (Scope):** Cú pháp truy vấn SQL/Elasticsearch ở Backend phải luôn kẹp thêm điều kiện `status = Approved` đối với Bài hát/Album và `status = Active` đối với Nghệ sĩ. Không được rò rỉ dữ liệu Pending ra ngoài (Trừ phi đó là API Search của Admin).

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Đọc (Read):** Truy vấn LIKE `%keyword%` đồng loạt trên bảng `[DB-songs]`, `[DB-users]`, `[DB-albums]`.
*   *(Gợi ý kiến trúc)* Nếu hệ thống lớn, API này phải dùng Elasticsearch hoặc Algolia thay vì truy vấn MySQL trực tiếp để tối ưu tốc độ và hỗ trợ tìm kiếm "Gần đúng" (Fuzzy Search).
