# Kiến trúc Backend API: [SCR-ART-05] Thiết lập Profile Nghệ sĩ

## 1. Yêu cầu & Luồng xử lý
Tính năng này cho phép Nghệ sĩ cập nhật thông tin cá nhân của mình trên hệ thống. Khác với thông tin User (Tên, Email đăng nhập, Password), các thông tin này sẽ hiển thị public ra cho người nghe (Listeners) xem.
Do đó, hệ thống cần thao tác với bảng `artist_profiles` (hoặc cấu trúc bảng tương đương quản lý profile nghệ sĩ).

## 2. Thiết kế API Endpoints

### API 1: Lấy thông tin Profile hiện tại
- **Endpoint:** `GET /api/v1/artist/profile`
- **Controller:** `Modules\Artist\Http\Controllers\ArtistProfileController`
- **Middleware:** `auth:sanctum`, `role:Artist|artist`
- **Xử lý Logic:** 
  - Lấy `artistProfile` dựa trên `user_id` của user đang đăng nhập.
  - Load kèm các thông tin phụ (như social links).
- **Response Format:**
  ```json
  {
      "success": true,
      "data": {
          "id": 1,
          "stage_name": "Sơn Tùng M-TP",
          "bio": "Nghệ sĩ trẻ đa tài...",
          "contact_email": "booking@mtpent.com",
          "avatar_url": "https://s3.../avatar.jpg",
          "banner_url": "https://s3.../banner.jpg",
          "social_links": {
              "instagram": "https://instagram.com/sontungmtp",
              "youtube": "https://youtube.com/c/sontungmtp"
          }
      }
  }
  ```

### API 2: Cập nhật thông tin Profile
- **Endpoint:** `PUT /api/v1/artist/profile` (hoặc `POST` nếu dùng `multipart/form-data` để gửi file qua PHP).
- **Lưu ý Kiến trúc Upload Ảnh:** Rút kinh nghiệm từ chức năng Upload Nhạc, mặc dù Avatar/Banner có dung lượng nhẹ (dưới 5MB), chúng ta vẫn có thể dùng **S3 Presigned URL** hoặc cho upload trực tiếp (vì ảnh thường được nén và xử lý resize tại backend bằng thư viện Intervention Image). Để đơn giản hoá ở Phase MVP cho Image, ta có thể chọn upload trực tiếp qua API (nhẹ) hoặc dùng chung luồng Presigned URL. *Đề xuất: Chấp nhận upload trực tiếp đối với ảnh (dưới 5MB) để resize ra các cỡ (thumbnail, medium) trước khi đưa lên S3.*
- **Request Format:** `multipart/form-data`
  - `stage_name` (string, max 255)
  - `bio` (text, max 1000)
  - `contact_email` (email, nullable)
  - `avatar` (file, mimes:jpg,jpeg,png, max:5120)
  - `banner` (file, mimes:jpg,jpeg,png, max:10240)
  - `social_links` (json hoặc array)
- **Xử lý Logic:**
  - Validate dữ liệu đầu vào.
  - Lấy `artistProfile` hiện tại.
  - Xử lý ảnh: Nén ảnh, resize và lưu vào Storage. Xóa ảnh cũ (nếu có).
  - Cập nhật thông tin DB.

## 3. Cấu trúc DB
Bảng `artist_profiles` cần đảm bảo có các trường:
- `user_id` (Khóa ngoại)
- `stage_name`
- `bio`
- `contact_email`
- `avatar_url` (Hoặc có thể lưu chung với User Avatar)
- `banner_url`
- `social_links` (JSON column)

## 4. Công việc cho Backend Agent
1. Kiểm tra bảng `artist_profiles` xem đã đủ cột chưa. (Nếu thiếu cột `social_links` JSON thì tạo Migration update).
2. Viết `ArtistProfileController`.
3. Viết FormRequest validation.
4. Cập nhật `UploadService` để hỗ trợ nén & xoá ảnh cũ.
5. Viết Test Feature đầy đủ.
