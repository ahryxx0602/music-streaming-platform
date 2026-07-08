# [SCR-ART-01] Màn hình Đăng ký Nghệ sĩ (Artist Registration - Invite Only)

> **Mô tả ngắn:** Màn hình đăng ký đặc biệt chỉ dành cho các đối tác/nghệ sĩ đã nhận được Link mời (có mã Token) từ Admin. Tuyệt đối không cho phép truy cập tự do.

## 1. Thông tin chung (Meta)
*   **Module:** Authentication / Artist
*   **Route / URL:** `/artist/register?token={token_hash}`
*   **Layout sử dụng:** `AuthLayout.vue`
*   **Quyền truy cập:** `[PER-GUEST-WITH-TOKEN]` (Chỉ người chưa đăng nhập VÀ có Token hợp lệ mới được vào).
*   **Component con (Children):**
    *   `TokenValidator.vue` (Component chạy ngầm để verify token ngay khi load trang)
    *   `ArtistRegisterForm.vue` (Form điền tên nghệ sĩ, email, password)
    *   `CopyrightAgreement.vue` (Checkbox dài đọc cam kết bản quyền)

## 2. Thành phần giao diện (UI Elements)
*   **Các ô nhập liệu (Inputs):**
    *   `Email`: Input email (Trạng thái `readonly`, tự điền từ Token).
    *   `Nghệ danh (Stage Name)`: Input text. Hiển thị public.
    *   `Họ tên thật (Legal Name)`: Input text. Dành cho hợp đồng pháp lý.
    *   `Mật khẩu` & `Nhập lại mật khẩu`: Input password.
    *   `Cam kết bản quyền`: Checkbox (Bắt buộc tích).
*   **Các nút bấm (Buttons & Links):**
    *   `Tạo tài khoản Nghệ sĩ`: Nút Submit gọi API hoàn tất.

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
Luồng xử lý bắt buộc đi qua 2 bước bảo mật:
1.  **Xác thực mã mời (Verification):**
    *   `[API-007]` - `GET /api/v1/auth/invitation/verify?token={token}`
    *   *Frontend tự động gọi khi vừa vào trang. Nếu Backend trả lỗi, ẩn Form và báo lỗi ngay.*
2.  **Submit Form Đăng ký (Mutations):**
    *   `[API-008]` - `POST /api/v1/auth/artist-register`
    *   Payload gửi đi: `token`, `artist_name`, `email`, `password`, `password_confirmation`, `agreed_to_terms`.

### State Management (Pinia)
*   **Store:** `authStore.js`
*   **Actions:** 
    *   `verifyArtistToken(token)`
    *   `registerArtist(payload)`: Gọi `[API-008]`. Xử lý thành công sẽ điều hướng.

## 4. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-ARTIST-01] - Chặn truy cập tự do & Catch lỗi ngầm:** 
    *   Lúc load trang: Nếu URL không có `?token=`, hoặc `[API-007]` trả về `[ERR-AUTH-403]`, chuyển hướng sang trang `403 Forbidden`.
    *   Lúc Submit form: Nếu Admin lỡ bấm "Thu hồi" mã khi Artist đang gõ form dở dang, Backend `[API-008]` sẽ trả về lỗi Token Expired. Frontend phải catch lỗi HTTP 403 ở hàm Submit, hiện popup: *"Mã mời này vừa bị vô hiệu hóa"* rồi văng ra ngoài.
*   **[RULE-ARTIST-02] - One-time Use:** Khi gọi thành công `[API-008]`, token đó sẽ bị hủy (đánh dấu đã sử dụng). Không ai có thể dùng lại link đó để tạo acc thứ 2.
*   **[RULE-ARTIST-03] - No OAuth2:** Màn hình này **không** cung cấp nút đăng nhập bằng Google/Facebook để đảm bảo tính chính danh.
*   **[RULE-ARTIST-04] - Ràng buộc định danh (Email Lock):** 
    *   **Frontend:** Khi gọi `[API-007]` thành công, lấy `email` từ response và điền thẳng vào Form, set thuộc tính `readonly` (khóa cứng không cho sửa).
    *   **Backend:** Khi Submit `[API-008]`, Backend bắt buộc đối chiếu Payload `email` với Email gốc của Token trong DB. Nếu lệch nhau (do Hacker cố tình Bypass Frontend), lập tức báo `[ERR-AUTH-422] Email mismatch`.
*   **Điều hướng (Redirect):**
    *   Đăng ký thành công ➔ Backend cấp Session Cookie ➔ Redirect thẳng vào `[SCR-ART-02]` (Artist Dashboard).
    *   *(Tùy chọn: Hoặc chuyển tới trang hiển thị "Chờ Admin duyệt vòng 2" tuỳ theo chính sách).*

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Kiểm tra dữ liệu (Read):** Đọc bảng `[DB-artist_invitations]` để tìm cột `token`. Kiểm tra cột `expires_at` (hạn dùng) và `used_at` (đã xài chưa).
*   **Ghi dữ liệu (Write):** 
    *   Update bảng `[DB-artist_invitations]`: Ghi thời gian hiện tại vào cột `used_at`.
    *   Insert vào bảng `[DB-users]`: Tạo user mới với `role` = `Artist` và trạng thái `status` = `Active`.
    *   Insert vào bảng `[DB-artist_profiles]`: Tạo hồ sơ với `stage_name` tương ứng.
