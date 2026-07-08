# [SCR-SHR-02] Luồng Khôi phục Mật khẩu (Password Recovery)

> **Mô tả ngắn:** Luồng xử lý cho phép người dùng (cả 3 Role: Listener, Artist, Admin) lấy lại quyền truy cập tài khoản khi quên mật khẩu thông qua Email xác thực. Gồm 2 màn hình nối tiếp nhau.

## 1. Thông tin chung (Meta)
*   **Module:** Authentication
*   **Route / URL:** 
    *   Bước 1: `/forgot-password`
    *   Bước 2: `/reset-password?token={hash}&email={email}`
*   **Layout sử dụng:** `AuthLayout.vue`
*   **Quyền truy cập:** `[PER-GUEST]`
*   **Component con (Children):**
    *   `ForgotPasswordForm.vue` (Form nhập Email gửi yêu cầu)
    *   `ResetPasswordForm.vue` (Form nhập mật khẩu mới)

## 2. Thành phần giao diện (UI Elements)
*   **Màn hình Quên mật khẩu (Forgot):**
    *   `Email`: Input email để nhận link.
    *   `Gửi Link Khôi Phục`: Nút Submit gọi API gửi mail.
    *   `Quay lại`: Link về màn hình Đăng nhập.
*   **Màn hình Đặt lại mật khẩu (Reset):**
    *   `Mật khẩu mới` & `Xác nhận mật khẩu`: Input password.
    *   `Cập nhật mật khẩu`: Nút Submit hoàn tất thay đổi.

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
Luồng xử lý đi qua 2 Endpoint tách biệt:
1.  **Gửi Link Khôi Phục (Giai đoạn 1):**
    *   `[API-004]` - `POST /api/v1/auth/forgot-password`
    *   Payload: `email`.
2.  **Đổi Mật Khẩu Mới (Giai đoạn 2):**
    *   `[API-005]` - `POST /api/v1/auth/reset-password`
    *   Payload: `token`, `email`, `password`, `password_confirmation`.

### State Management (Pinia)
*   Không yêu cầu lưu State phức tạp ở luồng này. Chỉ hiển thị thông báo (Toast) thành công/thất bại.

## 4. Quy tắc nghiệp vụ (Business Rules)

### Giai đoạn 1: Quên Mật Khẩu (Forgot Password)
*   **[RULE-PW-01] - Anti-Enumeration (Chống dò quét):** Khi gọi `[API-004]`, bất kể `email` có tồn tại trong hệ thống hay không, Backend luôn trả về HTTP 200 OK với một thông báo chung: *"Nếu email này có trong hệ thống, chúng tôi đã gửi link khôi phục cho bạn."*. Tuyệt đối không được báo *"Email không tồn tại"*.
*   **[RULE-PW-02] - Rate Limiting:** Để tránh Spam Email Server, giới hạn 1 email chỉ được gửi tối đa 3 yêu cầu khôi phục trong vòng 60 phút.

### Giai đoạn 2: Đặt Lại Mật Khẩu (Reset Password)
*   **[RULE-PW-03] - Hiệu lực Token:** Link khôi phục chỉ có tác dụng trong vòng **60 phút** kể từ lúc tạo. Nếu Backend check `[API-005]` thấy hết hạn, trả lỗi `[ERR-AUTH-400] Token Expired`.
*   **[RULE-PW-04] - Ràng buộc an toàn:** Mật khẩu mới không được trùng với mật khẩu hiện tại (Optional), phải tuân thủ chuẩn an toàn (8 ký tự, có số, chữ hoa).

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Đọc (Read):** Kiểm tra `email` ở bảng `[DB-users]`. Đối chiếu `email` và `token` ở bảng `[DB-password_reset_tokens]`.
*   **Ghi (Write):** 
    *   Sinh Token: Tạo mới/Cập nhật 1 record trong `[DB-password_reset_tokens]`.
    *   *Side Effect:* Đẩy Queue Job `[JOB-SEND-RESET-LINK]` để gửi thư.
    *   Đổi Pass: Update cột `password` (đã Hash) vào bảng `[DB-users]`.
    *   Xóa Token: Sau khi đổi pass thành công, xóa record tương ứng trong `[DB-password_reset_tokens]` để tránh xài lại (One-time use).
    *   Hủy Session: *(Tùy chọn bảo mật)* Reset lại toàn bộ các phiên bản Cookie/Session cũ của tài khoản đó.
