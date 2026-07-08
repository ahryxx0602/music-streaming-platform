# [SCR-LST-07] Hồ sơ Nghệ sĩ (Artist Profile - Public View)

> **Mô tả ngắn:** Giao diện hiển thị toàn bộ thông tin của một Nghệ sĩ cho Khán giả xem. Bao gồm Avatar, tiểu sử, số lượng người theo dõi, và phân nhóm các bản phát hành (Bài hát phổ biến, Album, Singles).

## 1. Thông tin chung (Meta)
*   **Module:** Listener / Discovery
*   **Route / URL:** `/artist/{id}-{slug}`
*   **Layout sử dụng:** `MainLayout.vue`
*   **Quyền truy cập:** Khách (Guest) và Listener.

## 2. Thành phần giao diện (UI Elements)
*   **Header Khổng lồ (Hero Banner):**
    *   Sử dụng ảnh Cover (`cover_image` trong Profile) làm ảnh nền toàn khung (Full-width).
    *   Phủ lên trên là Avatar tròn, Tên Nghệ Danh (Stage Name).
    *   Hàng nút bấm: Nút **Play (Phát tất cả)**, Nút **Follow (Theo dõi)**.
*   **Khu vực Bài hát Phổ biến (Popular Tracks):**
    *   Chỉ hiển thị top 5 bài hát có lượt Stream cao nhất của nghệ sĩ này.
    *   Cột hiển thị: Số thứ tự, Tên bài, Lượt stream, Thời lượng.
*   **Khu vực Danh sách đĩa nhạc (Discography):**
    *   Chia làm các khối: "Albums", "Singles & EPs".
    *   Dạng lưới nằm ngang (Horizontal Scroll) hoặc Lưới đa cột.
*   **Khu vực Thông tin (About):** Hiển thị Tiểu sử (Bio) và liên kết mạng xã hội (Facebook, Instagram).

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **Lấy dữ liệu (Fetch):**
    *   `[API-019]` - `GET /api/v1/artists/{id}` (Lấy thông tin profile, số follower).
    *   `[API-020]` - `GET /api/v1/artists/{id}/top-tracks` (Lấy top 5).
    *   `[API-021]` - `GET /api/v1/artists/{id}/albums` (Lấy toàn bộ Album).
*   **Mutations:**
    *   `[API-130]` - `POST /api/v1/artists/{id}/follow` (Toggle Theo dõi).

## 4. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-LST-ART-01] - Phát tất cả (Play All Logic):** Khi Khán giả bấm nút Play khổng lồ ở đầu trang, Frontend sẽ fetch toàn bộ bài hát của Nghệ sĩ này (tối đa 50 bài), trộn ngẫu nhiên (Shuffle) và nạp (Inject) trực tiếp vào Queue của `[SCR-LST-04] Global Player`, ghi đè lên Queue hiện hành.
*   **[RULE-LST-ART-02] - Đồng bộ Nút Follow:** Trạng thái của nút "Follow" phải phản hồi (Reactive) lập tức (chuyển sang "Following"). Tổng số Follower của Nghệ sĩ tăng lên 1 đơn vị ngay ở Frontend (Optimistic UI) mà không cần chờ load lại trang.

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Đọc (Read):** Bảng `[DB-users]`, `[DB-artist_profiles]`, `[DB-songs]`, `[DB-albums]`.
*   **Ghi (Write):** Insert/Delete bảng `[DB-user_follows]` khi bấm Follow.
