# [SCR-AUTH-02] Màn hình Đăng ký (Register)

> **Mô tả ngắn:** Màn hình cho phép người dùng đăng ký tài khoản mới. Mặc định tài khoản mới tạo sẽ mang Role là `Listener`. (Artist cần quy trình xác thực riêng).

## 1. Thông tin chung (Meta)
*   **Module:** Authentication
*   **Route / URL:** `/register`
*   **Layout sử dụng:** `AuthLayout.vue`
*   **Quyền truy cập:** `[PER-GUEST]` (Chỉ khách vãng lai mới được vào).
*   **Component con (Children):**
    *   `RegisterForm.vue` (Form nhập tên, email, mật khẩu)
    *   `SocialLogin.vue` (Dùng chung component với trang Login để đăng ký qua Google/Facebook)

## 2. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
1.  **Đăng ký qua Form truyền thống:**
    *   `[API-AUTH-04]` - `POST /api/auth/register`
    *   Payload gửi đi: `name`, `email`, `password`, `password_confirmation`.
2.  **Đăng ký qua Mạng xã hội (OAuth2):**
    *   Sử dụng lại `[API-AUTH-02]` và `[API-AUTH-03]` (Backend tự động xử lý logic Create User nếu Email chưa tồn tại).

### State Management (Pinia)
*   **Store:** `authStore.js`
*   **Actions:** 
    *   `register(payload)`: Gọi `[API-AUTH-04]`. Nếu thành công, Backend Laravel Sanctum thường sẽ tự động đăng nhập luôn (cấp Cookie) và trả về thông tin User. Store nạp dữ liệu này vào State `user`.

## 3. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-REG-01] - Mật khẩu mạnh:** Mật khẩu phải dài tối thiểu 8 ký tự, có ít nhất một chữ hoa, một chữ thường, một số và một ký tự đặc biệt. Trả lỗi `[ERR-AUTH-422]` nếu vi phạm.
*   **[RULE-REG-02] - Unique Email:** Email không được trùng với tài khoản đã có trong hệ thống. Báo lỗi ngay dưới ô nhập liệu: *"Email này đã được sử dụng. Vui lòng đăng nhập."*
*   **[RULE-REG-03] - Rate Limiting:** Tránh bot spam đăng ký tài khoản rác. Giới hạn 3 request đăng ký / 10 phút trên cùng 1 IP. Vi phạm trả mã `[ERR-AUTH-429]`.
*   **[RULE-REG-04] - Email Verification (Optional):** Sau khi đăng ký thành công, cột `email_verified_at` trong DB mặc định là `null`. 
    *   Backend tự động bắn Job gửi Email kích hoạt (Queue: `[JOB-SEND-VERIFY-EMAIL]`).
*   **Điều hướng (Redirect):**
    *   Ngay sau khi đăng ký thành công ➔ Đăng nhập tự động ➔ Redirect thẳng về trang Khám phá `[SCR-EXPLORE-01]` (Có thể hiện thêm một Toast thông báo nhắc check email).

## 4. Tác động Cơ sở dữ liệu (Database Impact)
*   **Ghi dữ liệu (Write):** 
    *   Thêm mới 1 dòng vào bảng `[DB-users]` với `role` = `Listener`.
    *   Thêm mới 1 dòng vào bảng `[DB-sessions]`.
