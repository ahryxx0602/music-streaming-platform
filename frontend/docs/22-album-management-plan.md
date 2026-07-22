# Kế hoạch Frontend: [SCR-ART-04] Quản lý Album & EP

## 1. Tổng quan & Triết lý Thiết kế (Design Philosophy)
Tính năng này nằm trong phân hệ **Artist Dashboard**. Mục tiêu là mang lại cho nghệ sĩ một không gian quản lý danh sách đĩa nhạc (Discography) chuyên nghiệp.
Vẫn bám sát chuẩn **Design System**: 
- **OLED Dark Mode** (True Black `#000000`).
- **Glassmorphism Components**: Kính mờ (`backdrop-blur-lg`) kết hợp viền sáng phản quang (`border-white/10`).
- **Typography**: Cặp font `Righteous` (Tiêu đề nổi bật) và `Inter` / `Poppins` (Nội dung dữ liệu, Form) để đảm bảo độ sắc nét.

## 2. Các Thành phần Giao diện (UI Components)

### 2.1. Màn hình Danh sách Album/EP (`AlbumListView.vue`)
- **Layout**: Dạng Lưới (Grid View). Trên desktop có thể là 4-5 cột, mobile là 2 cột.
- **Card Design (Glass Card)**:
  - **Cover Image**: Chiếm trọn nửa trên của Card, tỷ lệ vuông 1:1, góc bo tròn nhẹ (`rounded-t-xl`). Hiệu ứng Hover: Ảnh zoom nhẹ (scale 1.05) kèm Overlay Gradient đen.
  - **Thông tin (Card Body)**:
    - Tiêu đề Album/EP (Text trắng, in đậm).
    - Phân loại (Type Badge): Thẻ tag ghi rõ "Album", "EP", hoặc "Single" (Màu viền Neon Green/Blue).
    - Metadata phụ: Số lượng bài hát (Tracks count) & Ngày phát hành (Release Date).
- **Trạng thái Trống (Empty State)**: Nếu nghệ sĩ chưa có Album nào, hiển thị một khung Dashed Border khổng lồ giữa màn hình với thông điệp "Chưa có Album nào. Hãy tạo một kiệt tác mới!" kèm nút bấm Call-to-Action.

### 2.2. Màn hình Tạo mới Album/EP (`CreateAlbumModal.vue` / `CreateAlbumView.vue`)
- **Hình thức**: Sử dụng Modal (Glass Overlay) hoặc trang riêng biệt (Tùy độ phức tạp). Đề xuất dùng Trang riêng (Page) để có không gian thao tác rộng rãi.
- **Cấu trúc Form**:
  - **Cột Trái (Artwork)**:
    - Khu vực kéo thả (Drag & Drop) ảnh bìa (Cover Image).
    - Client-side validation: Bắt buộc là `.jpg`, `.png`, tối thiểu 1000x1000px, dung lượng < 5MB.
    - Chức năng tự động Preview ảnh ngay sau khi chọn.
  - **Cột Phải (Thông tin chi tiết)**:
    - Tiêu đề Album (Input Text).
    - Phân loại (Select: Album, EP, Single).
    - Ngày phát hành dự kiến (Date Picker).
    - Mô tả / Ý nghĩa Album (Textarea).
    - Trạng thái hiển thị (Switch: Public / Draft).
- **Hành động**: Nút "Tạo Album" có kèm Animation Loading (Spinner).

### 2.3. Màn hình Chi tiết Album & Sắp xếp Nhạc (`AlbumDetailView.vue`)
Đây là màn hình lõi (Core Screen) mang lại trải nghiệm tương tác cao nhất. Layout chia làm 2 Panel (Split Screen):

- **Panel Trái: Thư viện Nhạc Tự do (Unassigned Tracks)**
  - Hiển thị danh sách các bài hát nghệ sĩ đã tải lên (qua chức năng SCR-ART-03) nhưng chưa được gán vào bất kỳ Album nào.
  - Giao diện dạng List (List View) gọn gàng.
  - Có thanh Search nhỏ để tìm kiếm nhanh bài hát nếu danh sách quá dài.

- **Panel Phải: Danh sách Bài hát trong Album (Tracklist)**
  - Thông tin chung ở Header: Cover Mini, Tên Album, Nút "Sửa thông tin", "Phát hành".
  - Bảng danh sách bài hát (Tracklist Table).
  - **Tương tác cốt lõi (Drag & Drop)**:
    - Sử dụng thư viện `vuedraggable` hoặc Native HTML5 Drag & Drop.
    - Kéo một bài hát từ *Panel Trái* thả vào *Panel Phải* để gán bài hát vào Album.
    - Nắm kéo (Drag Handle Icon) các bài hát trong *Panel Phải* lên xuống để sắp xếp thứ tự phát (Track Order).
    - Có biểu tượng Thùng rác để đá một bài hát ra khỏi Album (đưa nó quay lại Panel Trái).
  - **Lưu dữ liệu**: 
    - Auto-save (Lưu tự động) sau mỗi cú kéo thả hoặc hiển thị nút "Lưu thay đổi" (Save Changes) tùy chiến lược. Đề xuất hiển thị nút "Save Changes" đang sáng rực lên để người dùng tự chốt luồng.

## 3. Trải nghiệm Tương tác (UX & Animations)
- **Cảm giác vật lý (Tactile Feedback)**: Khi kéo thả một Track, Item đang giữ sẽ có hiệu ứng `scale(1.02)` và đổ bóng Neon (`shadow-[0_0_15px_#22C55E]`) để báo hiệu nó đang "trôi nổi".
- **Khu vực thả (Dropzone Highlighting)**: Bảng danh sách bên Phải sẽ sáng lên một viền mờ khi có một Item đang bay lơ lửng bên trên nó (Drag Over).
- **Lỗi & Cảnh báo (Toasts)**: 
  - Nếu Album chưa có bài hát nào mà ấn "Phát hành", báo lỗi đỏ.
  - Xử lý các lỗi HTTP bằng Toast Notification góc phải màn hình.

## 4. Tích hợp API (Dự kiến)
- `GET /api/v1/artist/albums`: Lấy danh sách Albums.
- `POST /api/v1/artist/albums`: Tạo Album mới (Kèm Cover multipart/form-data).
- `GET /api/v1/artist/songs?unassigned=true`: Lấy nhạc chưa vào Album.
- `PUT /api/v1/artist/albums/{id}/tracks`: API gửi mảng ID các bài hát kèm thứ tự (`order`) để lưu vào DB. 
