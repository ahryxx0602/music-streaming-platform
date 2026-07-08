# [SCR-LST-04] Trình phát nhạc toàn cầu (Global Audio Player)

> **Mô tả ngắn:** Đây là trái tim của hệ thống Streaming. Nó không phải là một trang (page) riêng biệt, mà là một Component dùng chung (Global Component) luôn được ghim (Fixed) ở dưới cùng màn hình trong suốt quá trình người dùng lướt Web, giúp nhạc không bị ngắt quãng khi chuyển trang.

## 1. Thông tin chung (Meta)
*   **Module:** Listener / Streaming
*   **Vị trí Component:** Nằm ngoài cùng thẻ `<router-view>`, thường được đặt ở cuối `MainLayout.vue` hoặc `App.vue`.
*   **Quyền truy cập:** `[PER-AUTH]` (Guest bấm Play sẽ bị cản lại bởi Popup đăng nhập).
*   **Công nghệ lõi:** `HLS.js` kết hợp với HTML5 `<audio>` API (Chống tải lậu cấp độ Doanh nghiệp).

## 2. Thành phần giao diện (UI Elements)
*   **Khu vực Trái (Now Playing Info):**
    *   `Ảnh bìa`: Hình vuông nhỏ.
    *   `Tên bài hát`: Cuộn ngang (Marquee) nếu quá dài.
    *   `Tên nghệ sĩ`: Text Link (Click chuyển hướng về trang Profile Artist).
    *   `Nút Thả tim`: Toggle trạng thái Yêu thích bài hát này.
*   **Khu vực Giữa (Controls & Timeline - Controller):**
    *   `Cụm Nút Playback`: Trộn bài (Shuffle), Lùi (Prev), Phát/Tạm dừng (Play/Pause), Tiến (Next), Lặp lại (Repeat: Off/All/One).
    *   `Thanh Tiến trình (Seekbar)`: Cho phép kéo thả để tua nhạc. Hai bên hiển thị `Thời gian hiện tại / Tổng thời lượng`.
*   **Khu vực Phải (Settings & Tools):**
    *   `Thanh Âm lượng (Volume)`: Kéo thả, có nút tắt tiếng (Mute).
    *   `Nút Danh sách chờ (Up Next/Queue)`: Mở ra danh sách các bài hát sẽ phát tiếp theo.

## 3. Liên kết dữ liệu & Logic (State & APIs)

### State Management (Pinia) - Cực kỳ quan trọng
Để nhạc không bị mất khi đổi trang, toàn bộ Logic phát nhạc phải nằm trong **`usePlayerStore.js`**.
*   **State:**
    *   `currentTrack`: Object chứa data bài hát đang phát (title, artist, cover, `stream_url` - dẫn tới file `.m3u8`).
    *   `queue`: Mảng danh sách chờ (`[]`).
    *   `history`: Mảng danh sách bài đã nghe (để bấm Prev).
    *   `isPlaying`: Boolean.
    *   `currentTime`, `duration`: Quản lý thanh tiến trình.
    *   `volume`: Số (0-100), tự động sync với `localStorage` để nhớ mức âm lượng của User.
    *   `repeatMode`: 0 (Off), 1 (All), 2 (One).
    *   `isShuffle`: Boolean.

### Tương tác API (Network)
Player Frontend có nhiệm vụ tự động gọi API ngầm lên Server để ghi nhận dữ liệu:
*   **[API-111] - Ghi nhận Lượt nghe (Stream Count):**
    *   `POST /api/v1/streams/record`
    *   Payload: `song_id`.
*   **[API-LST-08] - Lịch sử nghe (Listen History):**
    *   *(Tùy chọn)* Lưu lịch sử để hiển thị mục "Vừa nghe gần đây".

## 4. Quy tắc nghiệp vụ (Business Rules)

*   **[RULE-PLAY-01] - HLS.js Lifecycle:** 
    *   Store tạo thẻ `<audio id="global-player" hidden>`.
    *   Sử dụng thư viện `Hls.js` để đọc luồng `stream_url` (định dạng m3u8) và đính kèm (attachMedia) vào thẻ audio. Hls.js sẽ lo việc nối các file `.ts` mượt mà.
    *   Store vẫn lắng nghe các Event Listener của HTML5 như: `timeupdate`, `ended`.
*   **[RULE-PLAY-02] - Điều kiện ghi nhận Stream Hợp lệ (Anti-Cheat):**
    *   Frontend chạy 1 biến đếm (Timer) chỉ tăng lên khi `isPlaying == true`.
    *   Khi người dùng nghe bài hát đạt mốc **30 giây** (Hoặc 50% thời lượng bài hát, tùy cấu hình), Frontend mới bắn `[API-111]` lên Backend để cộng 1 lượt nghe.
    *   Khi gọi API xong, đặt cờ `hasCounted = true` cho bài hát đó, để dù User có tua đi tua lại, hệ thống không bị Spam API. (Backend cũng cần Rate Limit theo IP/Session để chống Hack).
*   **[RULE-PLAY-03] - Thuật toán trộn bài (Shuffle Logic):**
    *   Khi bật tính năng Shuffle, không được xáo trộn luôn bài đang nghe (`currentTrack`).
    *   Frontend tạo ra bản sao `shuffledQueue`, giữ bài hiện tại ở index 0, và dùng thuật toán *Fisher-Yates* để trộn các bài còn lại.
*   **[RULE-PLAY-04] - Tải lậu & Bảo mật Audio (HLS DRM-Lite):**
    *   Tuyệt đối không cấp URL trực tiếp tới file MP3. Link `stream_url` trả về phải là đường dẫn tới file `master.m3u8`. Hacker dùng tools bắt link cũng chỉ tải được các mảnh cắt `.ts` vụn vặt 10 giây thay vì cả bài hát.

## 5. Kiến trúc Công nghệ lõi (HLS Streaming Architecture)
*   **Backend (Laravel):** Khi Nghệ sĩ tải lên file `.mp3` hoặc `.wav`, Backend sử dụng một Background Job gọi tới **FFmpeg** để tiến hành transcode (chuyển đổi) file gốc thành chuẩn HLS (chia nhỏ thành hàng trăm file `.ts` và 1 file `.m3u8`).
*   **Frontend (Vue.js):** Cài đặt package `hls.js`. Khi bấm Play, Frontend cung cấp link `.m3u8` cho lớp Hls, hệ thống sẽ tự động fetch từng mảnh `.ts` theo tiến độ nghe (nghe tới đâu tải tới đó), tiết kiệm cực kỳ nhiều băng thông so với việc tải trước toàn bộ file.

## 6. Tác động Cơ sở dữ liệu (Database Impact)
*   **Ghi (Write):** Mỗi lần Frontend bắn `[API-111]`, Backend Insert 1 dòng vào bảng `[DB-streams]`, đồng thời tăng bộ đếm tổng `streams_count` ở bảng `[DB-songs]`. (Có thể đẩy qua Message Queue / Redis để tránh Lock Table nếu lượng User quá đông).
