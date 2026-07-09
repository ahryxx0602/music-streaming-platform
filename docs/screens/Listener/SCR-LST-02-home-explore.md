# [SCR-LST-02] Màn hình Khám phá / Trang chủ (Home & Explore)

> **Mô tả ngắn:** Trang chủ dành riêng cho Listener (người nghe nhạc). Hiển thị các bài hát thịnh hành, playlist đề xuất, và các album mới phát hành.

## 1. Thông tin chung (Meta)
*   **Module:** Listener / Explore
*   **Route / URL:** `/` hoặc `/explore`
*   **Layout sử dụng:** `MainLayout.vue` (Có Sidebar, Header và Global Player ở dưới cùng)
*   **Quyền truy cập:** `[PER-PUBLIC]` (Áp dụng cho cả Guest và Auth).
*   **Component con (Children):**
    *   `BannerCarousel.vue` (Hiển thị Banner)
    *   `SongCarousel.vue` (Danh sách bài hát cuộn ngang)
    *   `AlbumGrid.vue` (Danh sách album)

## 2. Thành phần giao diện (UI Elements)
*   **Khối Banner (Hero Section):**
    *   `Banner Slider`: Hiển thị ảnh bìa sự kiện. Có nút "Nghe ngay".
*   **Khối Nội dung cuộn ngang (Carousels):**
    *   `Dành cho bạn (For You)`: Danh sách gợi ý cá nhân hóa (Chỉ hiện khi User đã đăng nhập).
    *   `Top Thịnh Hành`: Card Bài hát (Ảnh bìa, tên bài). Hover vào có nút `Play` nhanh. Click vào bài hát ➔ Kích hoạt Player.
    *   `Mới Phát Hành`: Card Album (Ảnh bìa vuông, tên album). Click vào Card Album ➔ Điều hướng tới `[SCR-LST-03]` (Chi tiết Album).
*   **Global Audio Player (Luôn nằm dưới cùng màn hình):**
    *   Thanh Timeline, nút Play/Pause, Next/Prev, Volume. (Được thiết kế chi tiết ở màn hình Player riêng).

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
Màn hình này tải dữ liệu (Read-heavy) nên ưu tiên gọi các API có Cache trên Redis:
*   **Lấy dữ liệu (Fetch):**
    *   `[API-011]` - `GET /api/v1/explore/banners` (Lấy danh sách Banner sự kiện).
    *   `[API-012]` - `GET /api/v1/explore/trending` (Top bài hát thịnh hành tuần).
    *   `[API-013]` - `GET /api/v1/explore/new-releases` (Album/Bài hát mới nhất).
    *   `[API-LST-04]` - `GET /api/v1/explore/recommendations` (Gợi ý cá nhân hóa).

### State Management (Pinia)
*   **Store:** `useExploreStore.ts`
*   **State:** `banners`, `trendingSongs`, `newReleases`, `isLoading`
*   **Actions:** 
    *   `fetchExploreData()`: Gọi đồng thời (Promise.all) 3 API trên để nạp dữ liệu.

## 4. Quy tắc nghiệp vụ (Business Rules)
*   Màn hình này cho phép `Guest` (chưa đăng nhập) vào xem, nhưng nếu Guest bấm nút `Play` vào bất kỳ bài hát nào ➔ Kích hoạt **[RULE-AUTH-03]**: Bật Popup yêu cầu đăng nhập và điều hướng sang `[SCR-SHR-01]`.
*   Danh sách `Trending` chỉ hiển thị top 10 bài hát có lượt Stream cao nhất trong 7 ngày qua.
*   **[RULE-LST-01] - Dữ liệu Cá nhân hóa (Personalization):** Nếu Frontend phát hiện `isAuthenticated == true`, tự động gọi thêm `[API-LST-04]` và hiển thị khối Carousel "Dành cho bạn". Nếu là Guest thì ẩn khối này đi.

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Cơ chế Cronjob Caching (Quan trọng):**
    *   Danh sách `Trending` (Top 10 bài hát) KHÔNG được truy vấn (Query) trực tiếp từ Database khi User vào trang (để tránh Full-table scan bảng `streams` chứa hàng triệu record).
    *   Backend sẽ có một Cronjob chạy ngầm mỗi **10 phút** để Aggregate (Tổng hợp) bảng `[DB-streams]`. Sau đó đẩy danh sách Top 10 này đè vào **Redis Cache**. 
    *   API `[API-012]` của Frontend chỉ việc get JSON từ Redis ra và trả về (Response time < 50ms).
*   Đọc gián tiếp qua Redis Cache. Nếu Miss Cache (Banners, New Releases), Backend mới query từ: 
    *   `[DB-banners]`
    *   `[DB-songs]`
    *   `[DB-albums]`
