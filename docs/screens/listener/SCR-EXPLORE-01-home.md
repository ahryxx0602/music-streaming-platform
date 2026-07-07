# [SCR-EXPLORE-01] Màn hình Khám phá (Explore / Home)

> **Mô tả ngắn:** Trang chủ dành riêng cho Listener (người nghe nhạc). Hiển thị các bài hát thịnh hành, playlist đề xuất, và các album mới phát hành.

## 1. Thông tin chung (Meta)
*   **Module:** Listener / Explore
*   **Route / URL:** `/` hoặc `/explore`
*   **Layout sử dụng:** `MainLayout.vue` (Có Sidebar, Header và Global Player ở dưới cùng)
*   **Component con (Children):**
    *   `BannerCarousel.vue` (Hiển thị Banner)
    *   `SongCarousel.vue` (Danh sách bài hát cuộn ngang)
    *   `AlbumGrid.vue` (Danh sách album)

## 2. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
Màn hình này tải dữ liệu (Read-heavy) nên ưu tiên gọi các API có Cache trên Redis:
*   **Lấy dữ liệu (Fetch):**
    *   `[API-LST-01]` - `GET /api/explore/banners` (Lấy danh sách Banner sự kiện).
    *   `[API-LST-02]` - `GET /api/explore/trending` (Top bài hát thịnh hành tuần).
    *   `[API-LST-03]` - `GET /api/explore/new-releases` (Album/Bài hát mới nhất).

### State Management (Pinia)
*   **Store:** `useExploreStore.js`
*   **State:** `banners`, `trendingSongs`, `newReleases`, `isLoading`
*   **Actions:** 
    *   `fetchExploreData()`: Gọi đồng thời (Promise.all) 3 API trên để nạp dữ liệu.

## 3. Quy tắc nghiệp vụ (Business Rules)
*   Màn hình này cho phép `Guest` (chưa đăng nhập) vào xem, nhưng nếu Guest bấm nút `Play` vào bất kỳ bài hát nào ➔ Kích hoạt **[RULE-AUTH-03]**: Bật Popup yêu cầu đăng nhập và điều hướng sang `[SCR-AUTH-01]`.
*   Danh sách `Trending` chỉ hiển thị top 10 bài hát có lượt Stream cao nhất trong 7 ngày qua.

## 4. Tác động Cơ sở dữ liệu (Database Impact)
*   Đọc gián tiếp qua Redis Cache. Nếu Miss Cache, Backend sẽ query từ: 
    *   `[DB-banners]`
    *   `[DB-songs]` (kết hợp `[DB-streams]` để tính top)
    *   `[DB-albums]`
