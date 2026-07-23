# Kiến trúc Backend API: [SCR-LST-02] Trang chủ Khám phá (Home Explore)

## 1. Yêu cầu & Luồng xử lý
Trang chủ Khám phá cần gọi rất nhiều dữ liệu đa dạng (Banners, Nhạc mới, Nghệ sĩ hot, Playlists). Nếu Frontend phải gọi 4-5 API riêng lẻ thì sẽ gây chậm thời gian tải trang ban đầu (TTFB - Time to First Byte).
**Giải pháp:** Xây dựng một API Endpoint DUY NHẤT để gom (Aggregate) toàn bộ dữ liệu cần thiết cho trang chủ trả về cùng một lúc.

## 2. Cơ chế Cache Tối đa (Aggregated Caching)
Dữ liệu trang chủ không thay đổi từng giây (Trừ khi có bài hát mới phát hành). Do đó, phải Cache API này trong vòng ít nhất **30 phút** hoặc **1 tiếng** (Redis Cache). Khi có thao tác thêm/sửa từ Admin/Artist, ta có thể clear cache.

## 3. Thiết kế API Endpoint

### `GET /api/v1/listener/home-explore`
- **Controller:** `Modules/Music/Http/Controllers/ListenerExploreController.php`
- **Middleware:** Tạm thời Public (Không cần Auth, Guest cũng xem được).
- **Cấu trúc JSON Trả về:**
  ```json
  {
    "success": true,
    "data": {
      "banners": [
        // Lấy từ bảng system_banners (nếu có, hoặc mock)
        { "id": 1, "image_url": "...", "link_url": "..." }
      ],
      "new_releases": [
        // Top 10 bài hát mới nhất (order by created_at desc) status = 'published'
        {
          "id": 101,
          "title": "Neon Light",
          "artist": { "id": 5, "stage_name": "K-ICM" },
          "cover_url": "...",
          "play_count": 1200
        }
      ],
      "trending_artists": [
        // Top 5 nghệ sĩ có total_streams cao nhất (order by total_streams desc)
        { "id": 5, "stage_name": "K-ICM", "avatar_url": "..." }
      ],
      "featured_playlists": [
        // Lấy 4 system playlists nổi bật
        { "id": 1, "name": "Top 100 EDM", "cover_url": "..." }
      ]
    }
  }
  ```

## 4. Công việc cho Backend Agent
1. **Controller:** Tạo `ListenerExploreController` với method `index()`.
2. **Logic Truy vấn:** 
   - Eloquent Query lấy `new_releases` (Với quan hệ `artist_profile`).
   - Eloquent Query lấy `trending_artists`.
   - Eloquent Query lấy `featured_playlists` (hoặc mock nếu bảng playlists chưa sẵn sàng).
3. **Cơ chế Cache:** Wrap toàn bộ logic truy vấn vào `Cache::remember('home_explore_data', 3600, function () { ... })`.
4. **Viết Test:** Đảm bảo API trả về đúng cấu trúc và đạt chuẩn tốc độ (sử dụng RefreshDatabase test).
