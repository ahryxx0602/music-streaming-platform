# Kiến trúc Backend API: Phân hệ Nghệ sĩ (Artist Workspace) - Khởi tạo & Dashboard

## 1. Yêu cầu Hệ thống
Artist Workspace là không gian riêng biệt dành cho người dùng có role `artist`. Các API liên quan đến không gian này phải được bảo vệ bởi middleware xác thực role `artist`.

## 2. API Endpoints [SCR-ART-02] Dashboard Nghệ sĩ

### API 1: Lấy Thống kê Tổng quan (Overview Stats)
- **Endpoint:** `GET /api/v1/artist/dashboard/stats`
- **Controller:** `Modules\Artist\Http\Controllers\ArtistDashboardController`
- **Logic:**
  1. Lấy thông tin `ArtistProfile` của user hiện tại (`Auth::user()->artistProfile`).
  2. Truy vấn đếm tổng số lượng Followers (Giả định thông qua bảng `user_follows_artist` nếu có, hoặc trả về 0 nếu chưa làm tính năng Follow).
  3. Truy vấn tổng số `play_count` từ tất cả các bài hát thuộc về Artist này trong bảng `songs`.
  4. Đếm tổng số bài hát (`songs` where `artist_id` = ID).
  5. Tính toán doanh thu hiện tại (Giả định là tổng lượt nghe x 0.01$).
- **Response Format:**
  ```json
  {
      "success": true,
      "data": {
          "total_followers": 1500,
          "total_streams": 152300,
          "total_tracks": 12,
          "balance_usd": 152.30
      }
  }
  ```

### API 2: Lấy Bài hát nổi bật (Top Tracks)
- **Endpoint:** `GET /api/v1/artist/dashboard/top-tracks`
- **Logic:** Lấy ra 5 bài hát của Artist này có `play_count` cao nhất, sắp xếp giảm dần. Trả về cả tên bài hát, hình cover album và số lượt nghe.

### API 3: Lấy Biểu đồ Lượt nghe (Streams Chart)
- **Endpoint:** `GET /api/v1/artist/dashboard/streams-chart`
- **Logic:** Tạm thời tạo mock data ngẫu nhiên cho biểu đồ 30 ngày (mỗi ngày có 100 - 500 lượt nghe).

## 3. Cấu trúc Routing
- Trong thư mục `backend/Modules/Artist/routes/api.php`:
  ```php
  Route::prefix('artist/dashboard')->middleware(['auth:sanctum', 'role:artist'])->group(function () {
      Route::get('/stats', [ArtistDashboardController::class, 'stats']);
      Route::get('/top-tracks', [ArtistDashboardController::class, 'topTracks']);
      Route::get('/streams-chart', [ArtistDashboardController::class, 'streamsChart']);
  });
  ```

## 4. Nhiệm vụ của Backend Agent
1. Tạo `ArtistDashboardController.php`.
2. Định nghĩa các API trên và code logic truy vấn dữ liệu từ DB (Dựa vào model `Song` và `ArtistProfile`).
3. Cập nhật `routes/api.php` của module Artist.
4. Đảm bảo chạy `php artisan test` (tạo test nếu cần) và push code.
