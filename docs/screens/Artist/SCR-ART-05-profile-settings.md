# [SCR-ART-05] Cài đặt Hồ sơ Nghệ sĩ (Artist Profile Settings)

> **Mô tả ngắn:** Cho phép Nghệ sĩ tùy chỉnh không gian hiển thị của mình trên ứng dụng (Phía Khán giả). Họ có thể cập nhật Nghệ danh, đổi ảnh đại diện (Avatar), ảnh bìa lớn (Cover), viết tiểu sử và chèn link mạng xã hội để tăng độ nhận diện.

## 1. Thông tin chung (Meta)
*   **Module:** Artist Workspace / Profile
*   **Route / URL:** `/artist/profile`
*   **Layout sử dụng:** `ArtistLayout.vue`
*   **Quyền truy cập:** `[PER-ARTIST]`
*   **Component con (Children):**
    *   `ProfileForm.vue` (Form chỉnh sửa thông tin).
    *   `ProfilePreview.vue` (Một khung nhỏ mô phỏng giao diện của trang nghệ sĩ bên phía người nghe để Artist xem trước).

## 2. Thành phần giao diện (UI Elements)
*   **Khu vực Hình ảnh (Visuals):**
    *   `Avatar`: Khung hình tròn. Có nút Upload đè lên. Khuyên dùng ảnh vuông 500x500px.
    *   `Ảnh bìa (Cover Image)`: Khung hình chữ nhật dài (Nằm ngang). Khuyên dùng 1920x600px.
*   **Khu vực Thông tin (Information):**
    *   `Nghệ danh (Stage Name)`: Input text (Hiển thị thay cho Tên thật).
    *   `Tiểu sử (Biography)`: Textarea hỗ trợ nhập nhiều dòng.
    *   *(Lưu ý: Không cho phép tự đổi Email hay Mật khẩu ở đây, việc đó nằm ở màn hình `[SCR-SHR-04]` Security Settings chung của User).*
*   **Khu vực Mạng xã hội (Social Links):**
    *   `Facebook, Instagram, Youtube, Website`: 4 ô Input text để nhập Link.
    *   `Nút Lưu thay đổi`.

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **Lấy dữ liệu (Fetch):**
    *   `[API-210]` - `GET /api/v1/artist/profile/me` (Lấy dữ liệu profile hiện tại của mình).
*   **Ghi dữ liệu (Mutations):**
    *   `[API-211]` - `PUT /api/v1/artist/profile/me` (Cập nhật thông tin text và links).
    *   `[API-212]` - `POST /api/v1/artist/profile/upload-avatar` (Tách riêng API up ảnh do dung lượng lớn).
    *   `[API-213]` - `POST /api/v1/artist/profile/upload-cover` (Tách riêng API up ảnh bìa).

### State Management (Pinia)
*   **Store:** `useAuthStore.js` (Vì Avatar và Stage name liên quan trực tiếp đến cục User Auth trên Header, nên update thẳng vào Global Auth Store).

## 4. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-ART-PRF-01] - Đồng bộ Cache Trang Chủ:** Tên và Avatar của Nghệ sĩ thường xuyên xuất hiện trên các màn hình Khám phá hoặc Chi tiết bài hát. Nếu Artist đổi Nghệ danh, Backend phải lập tức dọn dẹp các Cache liên quan đến họ (Ví dụ: `Redis::tags(['artist_'.$id])->flush()`) để Khán giả thấy ngay tên mới.
*   **[RULE-ART-PRF-02] - Giới hạn Kích thước Ảnh:** Để tiết kiệm dung lượng Storage và băng thông, Frontend phải ép dung lượng ảnh Avatar < 2MB và ảnh Cover < 5MB trước khi gọi hàm Upload. Nếu lớn hơn, báo lỗi ngay ở Frontend (Client-side Validation).

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Ghi (Write):** 
    *   Update bảng `[DB-artist_profiles]` (cho Bio, Cover, Social Links, Stage Name).
    *   Update cột `avatar` trong bảng `[DB-users]`.
    *   Lưu file vật lý lên MinIO.
