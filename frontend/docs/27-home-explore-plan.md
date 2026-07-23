# Kế hoạch Frontend: [SCR-LST-02] Trang chủ Khám phá (Home Explore)

## 1. Mục tiêu
Xây dựng trang chủ rực rỡ và thu hút (Landing/Explore) cho người dùng cuối (Listener). Đây là trang đầu tiên người dùng nhìn thấy, quyết định việc họ có muốn ở lại nghe nhạc hay không.

## 2. Cấu trúc UI (`HomeExploreView.vue`)

### 2.1. Hero Carousel (Banners)
- **Vị trí:** Trên cùng, chiếm một nửa màn hình (Hero section).
- **Thiết kế:** Dạng Slider/Carousel tự động chuyển động. Hiển thị các Banner quảng bá Album mới, Sự kiện, hoặc Nghệ sĩ nổi bật.
- Hiệu ứng: Chuyển slide mượt mà, có các chấm (dots) để chọn slide. Hình ảnh có gradient đen ở viền dưới để hòa vào nền.

### 2.2. Nhóm Nội dung: "Mới phát hành" (New Releases)
- **Vị trí:** Ngay dưới Hero Banner.
- **Thiết kế:** Grid 4-5 cột hoặc dạng trượt ngang (Horizontal Scroll).
- **Thành phần:** 
  - Hiển thị danh sách Bài hát (Song Card).
  - Có ảnh cover, tên bài hát (font to), tên nghệ sĩ (font nhỏ, xám).
  - Hover vào Cover sẽ hiện mờ nền đen và một **Nút Play lớn**. Bấm Play sẽ nạp trực tiếp bài hát đó vào `GlobalPlayer` (thông qua Pinia `playerStore.fetchAndPlaySong(id)`).

### 2.3. Nhóm Nội dung: "Nghệ sĩ Thịnh hành" (Trending Artists)
- **Thiết kế:** Thay vì dùng ảnh Cover hình vuông, phần Nghệ sĩ sẽ dùng Avatar bo tròn (Circle) hoàn toàn. Tên nghệ sĩ nằm dưới ảnh.
- Click vào Avatar sẽ nhảy tới trang Profile của Nghệ sĩ đó.

### 2.4. Nhóm Nội dung: "Playlist Khám phá" (System Playlists)
- **Thiết kế:** Các hình chữ nhật lớn (Landscape Cards) hiển thị Playlist do hệ thống tạo (VD: "Top 50 Nhạc EDM", "Chill Cuối Tuần").

## 3. Quản lý State & Routing
- Route: `/` hoặc `/explore` (dùng chung `ListenerLayout.vue` chứa `GlobalPlayer` bên dưới và `Sidebar` bên trái).
- Kết nối API bằng `onMounted`. Nên thiết kế bộ Skeleton Loading (nhấp nháy xám) hiển thị trong lúc chờ gọi API để tạo cảm giác app chạy nhanh (App-like feel).

## 4. UI/UX Style
- Tiếp tục tuân thủ ngôn ngữ thiết kế **OLED Dark Mode**.
- Font chữ `Righteous` cho Tiêu đề các mục, `Poppins` cho nội dung.
- Khoảng cách (Spacing) giữa các Section phải rộng rãi để tạo cảm giác "sang chảnh".
