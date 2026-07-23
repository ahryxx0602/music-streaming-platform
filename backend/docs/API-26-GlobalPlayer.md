# Kiến trúc Backend API: [SCR-LST-04] Trình phát nhạc toàn cầu (Global Player)

## 1. Yêu cầu Backend
Trình phát nhạc (Global Player) ở Frontend chủ yếu là xử lý Client-side. Tuy nhiên, để Player hoạt động có ý nghĩa, Backend cần cung cấp API để trả về link Audio, Metadata của bài hát, và đặc biệt là hệ thống **Theo dõi Lượt nghe (Stream Tracking/Play Count)**.

## 2. API Cung cấp dữ liệu
Khi người dùng bấm Play một bài hát, Frontend sẽ gọi API để lấy chi tiết:
- **Endpoint:** `GET /api/v1/listener/songs/{id}`
- Trả về: `id`, `title`, `artist_name`, `cover_url`, `audio_url`, `duration`, v.v.
*(Ghi chú: Nếu hệ thống chặn hotlink file âm thanh, `audio_url` có thể là một route stream hoặc Presigned URL tùy vào cấu hình S3).*

## 3. Hệ thống Tracking Lượt nghe (Play Count)
Chỉ tăng lượt nghe khi người dùng nghe trên 30 giây (Quy định chuẩn của các nền tảng streaming).
- **Endpoint:** `POST /api/v1/listener/songs/{id}/track-play`
- **Logic:**
  1. Frontend gọi API này sau khi `currentTime` của Audio > 30 giây.
  2. Bảng `songs`: `increment('play_count')`.
  3. Bảng `artist_profiles`: `increment('total_streams')` (để Artist xem Dashboard).
  4. (Tùy chọn tương lai): Bảng `listening_history` để lưu lại người dùng nào đã nghe bài nào lúc mấy giờ (phục vụ Recommender System và trả tiền bản quyền).
- **Security:** 
  - Cần Rate Limiting khắt khe (VD: 1 user/IP chỉ được tính 1 lượt nghe cho 1 bài trong vòng 1 tiếng) để chống auto-bot cày view cho idol.

## 4. Công việc cho Backend Agent
1. **API Lấy chi tiết bài hát:** Khởi tạo `Modules/Music/Http/Controllers/ListenerSongController.php` với hàm `show`.
2. **API Tracking:** Viết hàm `trackPlay` để tăng lượt nghe.
3. Cập nhật Model `Song` đảm bảo có hàm Helper hoặc Mutator để gen ra URL ảnh bìa và file nhạc.
4. (Optional): Tạo một `PlayHistory` table/model đơn giản để log IP/User chống cheat view (chưa bắt buộc nhưng nên có).
5. Viết Test Feature chứng minh gọi `/track-play` thành công sẽ tăng `play_count` trong DB lên 1.
