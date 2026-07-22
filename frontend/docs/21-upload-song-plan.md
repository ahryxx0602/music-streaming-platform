# Kế hoạch Frontend: [SCR-ART-03] Tải nhạc lên (Upload Song)

## 1. Mục tiêu Giao diện
Xây dựng một trang Upload Nhạc (Upload Song) mang phong cách **Glassmorphism & OLED Dark Mode** đồng nhất với Artist Dashboard. Trải nghiệm người dùng (UX) phải mượt mà, chuyên nghiệp và có cảm giác như một studio thu âm hiện đại.

## 2. Các Thành phần (Components)
### 2.1. Upload View (`ArtistUploadView.vue`)
- **Header**: Tiêu đề trang "Tải bài hát mới" (Upload New Track) kèm lời chào mừng.
- **Form Layout**: Chia làm 2 phần chính:
  - **Trái (Khu vực Upload & Ảnh bìa)**: 
    - Dropzone kéo thả Audio File (`.mp3`, `.wav`) với viền nét đứt (dashed border) glowing Neon Green.
    - Chỗ tải lên Ảnh bìa (Cover Image) tỷ lệ 1:1, hình vuông với hiệu ứng hover mờ.
  - **Phải (Thông tin bài hát - Metadata)**:
    - `Input` Tên bài hát (Title)
    - `Select` Thể loại (Genre) - Lấy từ API `/api/v1/genres`.
    - `Select` Album (Optional) - Lấy từ API các album của nghệ sĩ.
    - `Textarea` Lời bài hát (Lyrics).
    - `Toggle/Switch` Quyền riêng tư (Public / Private).
- **Thanh trạng thái Upload (Upload Progress Bar)**: 
  - Khi đang tải lên, hiển thị thanh tiến trình (progress bar) màu Neon Green với hiệu ứng stripe di chuyển.
- **Actions**: Nút "Xác nhận & Tải lên" (Submit) có hiệu ứng loading spinner.

## 3. Quản lý Trạng thái (State Management)
Sử dụng `ref` và `reactive` trong Vue 3:
- `formData`: Lưu trữ metadata (title, genre_id, lyrics, v.v.).
- `audioFile`: Lưu trữ file audio người dùng đã chọn.
- `coverFile`: Lưu trữ file ảnh bìa.
- `uploadProgress`: Phần trăm upload (từ 0 đến 100).
- `isUploading`: Boolean trạng thái khóa form khi đang upload.

## 4. Tích hợp API (Services)
- `getGenres()`: Lấy danh sách thể loại để điền vào Select Box.
- `uploadSong(formData)`: Gửi multipart/form-data lên API `POST /api/v1/artist/songs`. Bắt sự kiện `onUploadProgress` của Axios để cập nhật thanh tiến trình.

## 5. Tiêu chuẩn Thiết kế (Design Tokens)
- Sử dụng các token từ `.theme-artist` (`artist-theme.css`):
  - Viền Dropzone: `--color-border` (`#312E81`), khi drag over chuyển thành `--color-accent` (`#22C55E`).
  - Màu nền Form: `--color-surface/40` kèm `backdrop-blur-md` (hiệu ứng Kính mờ).
  - Text: Nhấn mạnh bằng font `Righteous` cho tiêu đề và `Poppins` cho metadata.

## 6. Đánh giá Chuyên sâu & Bổ sung (UI/UX Expert Review)

### 6.1. Trải nghiệm UX (Client-side Validation)
- **Vấn đề thiếu sót:** Tài liệu chưa đề cập đến việc xác thực file ngay tại Browser (Client-side). Nếu người dùng kéo thả file `.mp4` hoặc file `.wav` nặng 500MB, form sẽ vẫn submit lên Backend và chờ phản hồi rất mất thời gian, gây trải nghiệm tệ.
- **Đề xuất bổ sung:** 
  - Bắt sự kiện `onDrop` và `onChange` của thẻ Input File, kiểm tra ngay thuộc tính `file.type` (chỉ cho phép `audio/mpeg`, `audio/wav`) và `file.size` (Ví dụ: MAX_SIZE = 50MB cho MP3, 100MB cho WAV).
  - Hiện Toast thông báo lỗi hoặc cảnh báo inline màu đỏ (Error Message) ngay tức thì nếu file vi phạm, không cho phép nhấn nút "Upload".

### 6.2. Xử lý Audio Metadata (Web Audio API)
- **Vấn đề thiếu sót:** Thiếu bước đọc thời lượng bài hát (Duration) trước khi đẩy lên server. Nếu phụ thuộc vào Backend phân tích file Audio sẽ rất tốn CPU của máy chủ.
- **Đề xuất bổ sung:** 
  - Sử dụng thẻ `HTMLAudioElement` (`new Audio()`) kết hợp `URL.createObjectURL(file)`. Bắt sự kiện `onloadedmetadata` để lấy `audio.duration` (tính bằng giây).
  - Bổ sung trường `duration` vào `formData` gửi lên server. Đồng thời, hiển thị trực quan thời lượng (Ví dụ: `03:45`) ngay trên màn hình Upload để người dùng xác nhận đúng file.

### 6.3. Quản lý trạng thái Mạng (Network & State)
- **Vấn đề thiếu sót:** Chưa có cơ chế xử lý đứt mạng, báo lỗi chi tiết, hoặc cho phép người dùng Hủy (Cancel) tiến trình tải lên. Trong bối cảnh upload file nặng, điều này cực kỳ quan trọng.
- **Đề xuất bổ sung:**
  - Bổ sung state `uploadStatus`: `idle`, `uploading`, `success`, `error`, `canceled`.
  - Sử dụng `AbortController` của Fetch API hoặc `CancelToken` của Axios. Cung cấp một nút "Hủy tải lên" (Cancel Upload) kế bên thanh Progress Bar.
  - Xử lý cơ chế Retry: Hiện nút "Thử lại" nếu Axios quăng lỗi mạng (Network Error).

### 6.4. Đồng bộ Design System (Glassmorphism & OLED Dark Mode)
- **Vấn đề thiếu sót:** 
  - Giao diện OLED Dark Mode (thường dùng nền Đen tuyệt đối `#000000`) sẽ khiến Glassmorphism (`backdrop-blur`) bị chìm nghỉm nếu không có Box Shadow hoặc Border tạo chiều sâu.
  - Font `Righteous` là font Display mang phong cách đường phố/retro, phù hợp làm Logo hoặc Heading thật to, nhưng KHÔNG nên lạm dụng ở Label Form hay UI Component vì gây rối mắt.
- **Đề xuất bổ sung:**
  - Nền form Glassmorphism cần bọc thêm viền sáng mờ: `border border-white/10 shadow-[0_4px_30px_rgba(0,0,0,0.1)]`.
  - Giữ font `Righteous` cho Tiêu đề trang (H1), nhưng hãy dùng `Inter` hoặc `Poppins` cho toàn bộ Meta Text, Input, Button để đạt độ sắc nét và "SaaS-look" sang trọng nhất.

> **Trạng thái:** Tài liệu đã được rà soát và cập nhật bởi UI/UX Expert. Sẵn sàng chuyển qua giai đoạn Implementation (Coding).
