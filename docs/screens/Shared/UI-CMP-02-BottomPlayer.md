# [UI-CMP-02] Bottom Player (Trình Phát Nhạc)

> [!IMPORTANT]
> **I18N REQUIREMENT:** Tất cả các đoạn Text, Label, Placeholder, Message hiển thị trong tài liệu Screen Specs này khi triển khai vào code thực tế đều **KHÔNG ĐƯỢC HARDCODE**. Bắt buộc phải sử dụng key đa ngôn ngữ qua hàm `$t()` của `vue-i18n`.


> **Mô tả ngắn:** Thanh phát nhạc nằm ngang cố định ở mép dưới cùng của trình duyệt. Đây là trái tim của ứng dụng, cho phép nhạc tiếp tục phát khi user chuyển trang.

## 1. Thông tin chung
- **Vị trí:** Bottom của `[LYT-01]` MainLayout.
- **Kích thước:** `height: 90px`. `width: 100%`.
- **Z-Index:** Cực cao (`z-index: 50`) để đè lên mọi nội dung khác, ngoại trừ Modal.

## 2. Thành phần giao diện (UI Elements)
Chia làm 3 khu vực chính (Left, Center, Right):

### Khu vực Trái (Now Playing Info)
- Ảnh bìa bài hát (Cover Art). Khi click mở to toàn màn hình.
- Tên bài hát (Song Title) + Tên nghệ sĩ (Artist Name).
- Nút "Thêm vào bài hát đã thích" (Heart icon).

### Khu vực Giữa (Playback Controls & Progress)
- **Controls:** Shuffle (Trộn bài), Previous (Bài trước), Play/Pause (Icon to nhất, ở giữa), Next (Bài sau), Repeat (Lặp lại).
- **Progress Bar (Thanh tiến trình):** 
  - Hiển thị thời gian hiện tại (VD: `1:24`) bên trái.
  - Thanh trượt (Slider) để tua (Scrubbing).
  - Thời gian tổng (VD: `3:45`) bên phải.

### Khu vực Phải (Extra Controls)
- Nút Hiện/Ẩn Hàng đợi (Queue - View Danh sách phát).
- Điều khiển Âm lượng (Volume Slider + Mute icon).
- (Tùy chọn) Nút Lyrics (Xem lời bài hát).

## 3. Liên kết dữ liệu & Logic (Audio Engine)
- **Store (Pinia):** Gắn kết chặt chẽ với `playerStore.ts`.
- **Audio Engine:** Sử dụng thư viện `Howler.js` (hoặc Audio API thuần qua VueUse `useMediaControls`) để xử lý phát nhạc thực tế. Player UI chỉ là lớp vỏ hiển thị State từ `playerStore`.
- **Anti-Cheat Logic:** Gọi API cộng lượt nghe (Stream Count) chỉ khi User nghe được `>= 30 giây` mà không tua.
- **Presist Queue:** Hàng đợi (Queue) phải được lưu vào LocalStorage để khi người dùng F5 trình duyệt không bị mất bài đang nghe.

## 4. Tác động CSS (Aesthetics)
- Nền: `var(--color-surface)` hoặc Glassmorphism đậm màu hơn.
- Progress Bar: Gradient `var(--color-primary)` sang `var(--color-secondary)`. Khi kéo (scrubbing) hiện tooltip hiển thị giây.
- Hiệu ứng Sóng nhạc (Tùy chọn): Sử dụng `GSAP` để vẽ sóng âm thanh cực nhỏ bên cạnh ảnh bìa khi nhạc đang play.
