# Kế hoạch Frontend: [SCR-LST-04] Trình phát nhạc toàn cầu (Global Player)

## 1. Mục tiêu
Xây dựng một thanh nghe nhạc tĩnh (Sticky Player) luôn luôn hiển thị ở dưới cùng màn hình (bottom: 0) trong suốt quá trình người dùng (Listener) điều hướng trên web. Player sẽ không bao giờ bị gián đoạn hay tải lại khi chuyển trang (nhờ kiến trúc Single Page Application của Vue Router).

## 2. Kiến trúc State Management (Pinia)
Vì Player có thể được điều khiển từ bất kỳ đâu (Ví dụ: bấm nút Play ở trang Chủ, trang Tìm kiếm), chúng ta bắt buộc phải sử dụng **Pinia Store** (`usePlayerStore`).
**State cần lưu:**
- `currentSong`: Bài hát đang phát (object chứa URL audio, title, cover_image, artist_name).
- `isPlaying`: Trạng thái (boolean).
- `queue`: Danh sách bài hát chờ phát (mảng).
- `currentTime`, `duration`: Thời gian hiện tại và tổng độ dài bài hát.
- `volume`: Âm lượng (0 - 1).

## 3. Bố cục UI (GlobalPlayer.vue)
Component này sẽ được đặt trực tiếp trong file `App.vue` (hoặc ListenerLayout.vue) ở vị trí dưới cùng.
Giao diện chia làm 3 cột (Flexbox):

### Cột Trái (Now Playing Info)
- Ảnh bìa bài hát (Hình vuông nhỏ, bo góc).
- Tên bài hát (Chữ màu trắng, in đậm).
- Tên nghệ sĩ (Chữ màu xám, font nhỏ, bấm vào sẽ dẫn tới trang Artist).
- Nút "Yêu thích" (Trái tim).

### Cột Giữa (Playback Controls)
- Các nút điều khiển: Nhạc trước (Prev), Play/Pause (Hình tròn to nhất, nổi bật), Nhạc sau (Next).
- Nút Shuffle (Ngẫu nhiên), Repeat (Lặp lại).
- Thanh Tiến trình (Progress Bar): Slider hiển thị `currentTime` và `duration`. 

### Cột Phải (Volume & Extras)
- Icon Loa (Bấm vào để Mute).
- Thanh Slider chỉnh âm lượng.
- Nút mở danh sách chờ (Queue/Up Next).

## 4. Công nghệ & Thư viện
- Sử dụng **HTML5 Audio API** thuần (`new Audio()`) kết hợp với Pinia để thao tác. Không cần thư viện ngoài trừ khi cần hiệu ứng phức tạp.
- Sử dụng thẻ `<input type="range">` tùy chỉnh CSS cho thanh Progress Bar và Volume.
- Icon: Sử dụng thư viện Tabler Icons.

## 5. UI/UX Style
- Thanh Player cao khoảng `90px`, nền đen mờ kính `bg-black/90 backdrop-blur-xl`, viền trên mờ `border-t border-white/10`.
- Nút Play/Pause có hiệu ứng Neon nhẹ (Box shadow) khi Hover.
- Các thanh trượt có màu xanh/tím đặc trưng (Accent color).
