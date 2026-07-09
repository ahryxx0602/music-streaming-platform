# [SCR-SHR-01] Màn hình Đăng nhập (Login)

> **Mô tả ngắn:** Màn hình cho phép người dùng (Listeners, Artists, Admins) nhập thông tin tài khoản để truy cập vào hệ thống. Hỗ trợ xác thực qua hệ thống Sanctum Cookie-based.

## 1. Thông tin chung (Meta)
*   **Module:** Authentication
*   **Route / URL:** `/login`
*   **Layout sử dụng:** `AuthLayout.vue`
*   **Quyền truy cập:** `[PER-GUEST]` (Chỉ người chưa đăng nhập mới vào được).
*   **Component con (Children):**
    *   `LoginForm.vue` (Form nhập email/password chính)
    *   `SocialLogin.vue` (Nút đăng nhập qua Google/Facebook - Nếu có)

## 2. Thành phần giao diện (UI Elements)
*   **Các ô nhập liệu (Inputs):**
    *   `Email`: Input (type="email"). Bắt buộc.
    *   `Password`: Input (type="password"). Bắt buộc.
    *   `Remember me`: Checkbox lưu phiên đăng nhập.
*   **Các nút bấm (Buttons & Links):**
    *   `Đăng nhập`: Nút Submit chính gọi `[API-002]`.
    *   `Đăng nhập Google/Facebook`: Nút gọi OAuth2.
    *   `Quên mật khẩu?`: Link điều hướng tới `[SCR-SHR-02]`.
    *   `Đăng ký ngay`: Link điều hướng tới `[SCR-LST-01]`.

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
Quá trình đăng nhập trải qua 2 bước bắt buộc của Laravel Sanctum SPA:
1.  **Lấy CSRF Token:**
    *   `[API-001]` - `GET /sanctum/csrf-cookie` (Được Axios tự động gọi).
2.  **Gửi thông tin xác thực (Mutations):**
    *   `[API-002]` - `POST /api/v1/guest/auth/login` (Login bằng Email/Password truyền thống).
    *   Payload gửi đi: `email`, `password`, `remember_me`.
3.  **Đăng nhập Mạng xã hội (OAuth2 - Laravel Socialite):**
    *   `[API-009]` - `GET /api/v1/guest/auth/redirect/{provider}` (Ví dụ: `google`, `facebook`). API này trả về URL chuyển hướng sang trang OAuth.
    *   `[API-010]` - `GET /api/v1/guest/auth/callback/{provider}` (Backend xử lý Callback, tạo user nếu chưa có, và cấp Session cookie).

### State Management (Pinia)
*   **Store:** `authStore.ts`
*   **State:** `user`, `isAuthenticated`, `role`
*   **Actions:** 
    *   `login(credentials)`: Gọi `[API-002]`, lưu State, và điều hướng.
    *   `loginOAuth(provider)`: Gọi `[API-009]` để mở popup/redirect sang trang cấp quyền. Khi redirect về frontend thành công, gọi API fetch profile để lưu State.

## 4. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-AUTH-01]:** Email phải đúng định dạng, bắt buộc điền. Password tối thiểu 8 ký tự.
*   **[RULE-AUTH-02]:** Bắt lỗi UI.
    *   Lỗi sai thông tin: Trả về `[ERR-AUTH-401]`, hiển thị text đỏ dưới form.
    *   Lỗi validation: Trả về `[ERR-AUTH-422]`, bôi đỏ ô input.
*   **[RULE-AUTH-03] - Brute-force Protection:** Giới hạn request (Throttle). Nếu sai mật khẩu 5 lần liên tiếp trong 1 phút ➔ Khóa tạm thời IP/Email, trả về mã lỗi `[ERR-AUTH-429]`. 
*   **[RULE-AUTH-04] - Account Status:** Nếu tài khoản có `status === 'banned'` hoặc `'suspended'` ➔ Chặn đăng nhập, trả về mã lỗi `[ERR-AUTH-403]` (Forbidden).
*   **[RULE-AUTH-05] - Remember Me TTL:** 
    *   Nếu gửi `remember_me = true`: Laravel cấp session có TTL = 30 ngày.
    *   Nếu `false`: Session-based (Xóa khi đóng trình duyệt).
*   **[RULE-AUTH-06] - Luồng OAuth2 (Social Login):**
    *   Nếu User dùng Google/Facebook để đăng nhập lần đầu tiên: Hệ thống tự động khởi tạo tài khoản mới với vai trò mặc định là `Listener`.
    *   Nếu Email từ Google/Facebook trùng với Email đã đăng ký bằng mật khẩu: Tự động liên kết (Merge) tài khoản mà không báo lỗi.
*   **Điều hướng (Redirect) sau khi login thành công:**
    *   Nếu `user.role === 'Admin'` ➔ Chuyển hướng tới `[SCR-ADM-01]` (Admin Dashboard).
    *   Nếu `user.role === 'Artist'` ➔ Chuyển hướng tới `[SCR-ART-02]` (Artist Dashboard).
    *   Nếu `user.role === 'Listener'` ➔ Chuyển hướng tới `[SCR-LST-02]` (Trang chủ/Khám phá).

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Kiểm tra dữ liệu (Read):** Bảng `[DB-users]` để đối chiếu `email`, `status` (Banned/Active) và Hash của `password`.
*   **Ghi dữ liệu (Write):** Laravel Sanctum có thể tạo/cập nhật Session trong bảng `[DB-sessions]`.
