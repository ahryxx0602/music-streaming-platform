# PROMPT KẾ HOẠCH TRIỂN KHAI MODULE AUTHENTICATION (PHẦN MỞ RỘNG)

**Bối cảnh:** Bạn đóng vai trò là Backend Engineer (Laravel). Nhiệm vụ của bạn là triển khai Phần Mở Rộng của Module `Authentication` (Xác minh Email & Quên mật khẩu). Tuân thủ chặt chẽ nguyên tắc SOLID và Action-Domain-Responder theo tài liệu `05-development-guidelines.md`.

---

## 🎯 TASK 1: Xác thực Email (Email Verification)

**Tài liệu tham chiếu:** `[API-006]`, `[API-103]`, `[SCR-SHR-03]`

**Hành động yêu cầu:**
1. Đảm bảo Model `User` đang implements interface `MustVerifyEmail`.
2. Khởi tạo class `VerifyEmailAction` (Trong thư mục `Modules/Authentication/Actions/`):
   - Nhận đối tượng User và thực thi `$user->markEmailAsVerified()`.
   - Bắn Event `Verified` của Laravel.
3. Khởi tạo class `ResendVerificationEmailAction`:
   - Kiểm tra user đã verify chưa. Nếu chưa thì `$user->sendEmailVerificationNotification()`.
4. **[Bảo mật cấu trúc SPA] - Override URL Link Frontend:**
   - Trong ứng dụng SPA, mặc định thư của Laravel sẽ sinh link trỏ về Backend (`localhost:8000`). BẮT BUỘC phải viết 1 Custom Notification (`CustomVerifyEmail`) để override lại phương thức `verificationUrl()`, nhằm tạo ra URL trỏ về Frontend (VD: `http://localhost:5173/verify-email?id=...&hash=...`).
5. Tạo `EmailVerificationController` (Tách biệt khỏi `AuthController` để tuân thủ SRP):
   - `verify(EmailVerificationRequest $request)`: Xử lý `[API-006]`, dùng URL signature để cấp quyền. Trả về JSend response.
   - `resend(Request $request)`: Xử lý `[API-103]`.

---

## 🎯 TASK 2: Khôi phục Mật khẩu (Password Recovery)

**Tài liệu tham chiếu:** `[API-004]`, `[API-005]`, `[SCR-SHR-02]`

**Hành động yêu cầu:**
1. Đảm bảo migration tạo bảng `password_reset_tokens` mặc định của Laravel đã sẵn sàng.
2. Tạo các Form Requests Validation: 
   - `ForgotPasswordRequest`: Bắt buộc nhập `email` và phải tồn tại trong bảng `users`.
   - `ResetPasswordRequest`: Nhận `token`, `email`, và `password`. Đảm bảo `password` tuân thủ chuẩn ngặt nghèo `[RULE-REG-01]` (chữ hoa, chữ thường, số, ký tự đặc biệt).
3. Khởi tạo `ForgotPasswordAction`:
   - Gọi logic `Password::broker()->sendResetLink()`.
4. Khởi tạo `ResetPasswordAction`:
   - Gọi logic `Password::broker()->reset()`.
5. **[Bảo mật cấu trúc SPA] - Override Reset URL:**
   - Trong `AppServiceProvider` (hoặc `AuthServiceProvider`), BẮT BUỘC gọi `ResetPassword::createUrlUsing(function ($user, string $token) { return config('app.frontend_url') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email); });` để đảm bảo link trong Email trỏ về Frontend SPA.
6. Tạo `PasswordResetController` (Tách biệt khỏi `AuthController`):
   - `forgotPassword()`: Hứng request `[API-004]` và gọi Action.
   - `resetPassword()`: Hứng request `[API-005]` và gọi Action.

---

## 🎯 TASK 3: Tích hợp Email Notification & Custom Templates

**Hành động yêu cầu:**
1. Override (Tùy chỉnh) giao diện email của Laravel Notifications (`VerifyEmail` và `ResetPassword`) thông qua lệnh `php artisan vendor:publish --tag=laravel-mail`.
2. Tinh chỉnh CSS nội tuyến (Inline CSS) của file Markdown Blade Template theo hơi hướng "Aurora Blue" (sử dụng mã màu `#3B82F6` cho các nút bấm Call-to-Action) để đồng bộ trải nghiệm với Frontend.

---
*(Hết Prompt. Vui lòng phản hồi "Đã hiểu và sẵn sàng bắt đầu Task 1" để chúng ta tiến hành).*
