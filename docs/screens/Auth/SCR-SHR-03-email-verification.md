# [SCR-SHR-03] Luồng Xác minh Định danh (Email Verification)

> **Mô tả ngắn:** Màn hình và luồng xử lý yêu cầu người dùng xác thực địa chỉ Email thông qua một đường link gửi vào hòm thư.

## 1. Thông tin chung (Meta)
*   **Module:** Authentication
*   **Route / URL:** 
    *   Trang thông báo: `/verify-email`
    *   Trang xử lý link: `/email/verify/{id}/{hash}`
*   **Layout sử dụng:** `AuthLayout.vue`
*   **Quyền truy cập:** `[PER-AUTH-UNVERIFIED]` (Chỉ người đã đăng nhập nhưng cột `email_verified_at` đang là `null` mới thấy).
*   **Component con (Children):**
    *   `VerifyEmailPrompt.vue` (Hiển thị câu thông báo "Vui lòng kiểm tra hộp thư")
    *   `ResendVerifyBtn.vue` (Nút bấm gửi lại email nếu bị thất lạc)

## 2. Thành phần giao diện (UI Elements)
*   **Thành phần hiển thị (Display):**
    *   `Graphic Icon`: Hình ảnh hộp thư hoặc lá thư cảnh báo.
    *   `Tiêu đề`: "Xác minh tài khoản của bạn".
*   **Các nút bấm (Buttons & Links):**
    *   `Gửi lại Email xác minh`: Nút bấm gọi API resend. Khi bấm sẽ chuyển sang trạng thái disabled và đếm ngược 60s.
    *   `Trở về trang chủ`: Link để bỏ qua (Soft block).

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
1.  **Xác thực Link (Verification):**
    *   `[API-006]` - `GET /api/v1/guest/auth/email/verify/{id}/{hash}`
    *   *(API sử dụng cơ chế URL Signed Route của Laravel để chống giả mạo).*
2.  **Gửi lại Link (Resend):**
    *   `[API-103]` - `POST /api/v1/listener/auth/email/resend`

## 4. Quy tắc nghiệp vụ (Business Rules)

### Phân loại mức độ xác thực (Verification Tiers)
*   **[RULE-VERIFY-01] - Artist (Nghệ sĩ):** Nghệ sĩ đăng ký thông qua mã Invite (Gửi vào Email). Do đó, ngay khi đăng ký thành công tại `[SCR-ART-01]`, Backend **tự động** gán `email_verified_at = now()`. Không cần bắt Artist đi verify lần nữa.
*   **[RULE-VERIFY-02] - OAuth2 (Google/Facebook):** Các Listener đăng nhập bằng Mạng xã hội đã được phía Google xác thực. Backend tự động gán `email_verified_at = now()`.
*   **[RULE-VERIFY-03] - Form truyền thống:** Chỉ các Listener đăng ký tài khoản bằng Email/Password truyền thống mới bị đưa vào luồng phải đi xác minh này.

### Trải nghiệm người dùng (UX Flow)
*   **[RULE-VERIFY-04] - Soft Block:** Nếu Listener chưa xác thực Email, họ vẫn có thể vào trang Chủ `[SCR-LST-02]` để nghe nhạc bình thường. Tuy nhiên, khi họ thực hiện các hành động "Write" (Tạo Playlist, Bình luận, Đổi Avatar...), Frontend sẽ chặn lại bằng Popup và điều hướng họ về màn hình `/verify-email`.
*   **[RULE-VERIFY-05] - Resend Limit:** Nút "Gửi lại Email" có cơ chế Throttle (Mỗi phút chỉ được gửi 1 lần) để chống việc SPAM tốn tiền hệ thống Mail (SES/SMTP).
*   **[RULE-VERIFY-06] - Hết hạn Link:** Link xác thực mặc định sống trong 60 phút. Nếu nhấn vào link quá hạn, Frontend hiện thông báo *"Đường link đã hết hạn"* và hiển thị lại nút Resend.

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Kiểm tra (Read):** Đọc cột `email_verified_at` trong bảng `[DB-users]`.
*   **Ghi (Write):** Cập nhật thời gian thực vào cột `email_verified_at` khi gọi API thành công.
