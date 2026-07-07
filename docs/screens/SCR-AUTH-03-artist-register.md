# [SCR-AUTH-03] Màn hình Đăng ký Nghệ sĩ (Artist Registration - Invite Only)

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

## 2. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
Luồng xử lý bắt buộc đi qua 2 bước bảo mật:
1.  **Xác thực mã mời (Verification):**
    *   `[API-AUTH-05]` - `GET /api/auth/invitation/verify?token={token}`
    *   *Frontend tự động gọi khi vừa vào trang. Nếu Backend trả lỗi, ẩn Form và báo lỗi ngay.*
2.  **Submit Form Đăng ký (Mutations):**
    *   `[API-AUTH-06]` - `POST /api/auth/artist-register`
    *   Payload gửi đi: `token`, `artist_name`, `email`, `password`, `password_confirmation`, `agreed_to_terms`.

### State Management (Pinia)
*   **Store:** `authStore.js`
*   **Actions:** 
    *   `verifyArtistToken(token)`
    *   `registerArtist(payload)`: Gọi `[API-AUTH-06]`. Xử lý thành công sẽ điều hướng.

## 3. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-ARTIST-01] - Chặn truy cập tự do:** Nếu truy cập URL không có tham số `?token=`, hoặc gọi `[API-AUTH-05]` trả về `[ERR-AUTH-403]` (Token hết hạn/đã sử dụng/sai), Frontend hiển thị trang `403 Forbidden` ngay lập tức.
*   **[RULE-ARTIST-02] - One-time Use:** Khi gọi thành công `[API-AUTH-06]`, token đó sẽ bị hủy (đánh dấu đã sử dụng). Không ai có thể dùng lại link đó để tạo acc thứ 2.
*   **[RULE-ARTIST-03] - No OAuth2:** Màn hình này **không** cung cấp nút đăng nhập bằng Google/Facebook để đảm bảo tính chính danh của email và tài khoản.
*   **Điều hướng (Redirect):**
    *   Đăng ký thành công ➔ Backend cấp Session Cookie ➔ Redirect thẳng vào `[SCR-ARTIST-01]` (Artist Dashboard).
    *   *(Tùy chọn: Hoặc chuyển tới trang hiển thị "Chờ Admin duyệt vòng 2" tuỳ theo chính sách).*

## 4. Tác động Cơ sở dữ liệu (Database Impact)
*   **Kiểm tra dữ liệu (Read):** Đọc bảng `[DB-artist_invitations]` để tìm cột `token`. Kiểm tra cột `expires_at` (hạn dùng) và `used_at` (đã xài chưa).
*   **Ghi dữ liệu (Write):** 
    *   Update bảng `[DB-artist_invitations]`: Ghi thời gian hiện tại vào cột `used_at`.
    *   Insert vào bảng `[DB-users]`: Tạo user mới với `role` = `Artist` và trạng thái `status` = `Active`.
    *   Insert vào bảng `[DB-artist_profiles]`: Tạo hồ sơ với `stage_name` tương ứng.
