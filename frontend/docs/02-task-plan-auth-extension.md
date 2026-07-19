# PROMPT KẾ HOẠCH TRIỂN KHAI FRONTEND - AUTHENTICATION (PHẦN MỞ RỘNG)

**Bối cảnh:** Bạn đóng vai trò là Frontend Engineer (Vue 3 / Pinia). Triển khai màn hình Quên mật khẩu và Xác thực Email theo kiến trúc `06-frontend-architecture.md` và Design System `09-design-system.md` (Theme: Aurora Blue).

---

## 🎯 TASK 1: Xây dựng màn hình Quên / Khôi phục mật khẩu

**Tài liệu tham chiếu:** `[SCR-SHR-02]`, `[API-004]`, `[API-005]`

**Hành động yêu cầu:**
1. Khởi tạo Component `views/Auth/ForgotPassword.vue`:
   - Xây dựng Form nhập Email theo chuẩn UI (nền `#131B2F`, viền `#2A3B57`, focus viền `#60A5FA`).
   - Action Pinia: Gọi `api.post('/guest/auth/forgot-password')`.
   - Hiển thị thông báo Success/Error (JSend) để báo người dùng kiểm tra hòm thư.
2. Khởi tạo Component `views/Auth/ResetPassword.vue`:
   - Đọc query params `?token=` và `?email=` từ URL.
   - Form nhập Mật khẩu mới và Xác nhận mật khẩu.
   - Bắt lỗi validate ngay tại client: Tối thiểu 8 ký tự, có chữ hoa, thường, số, ký tự đặc biệt.
   - Gọi API `[API-005]`. Nếu thành công, hiển thị Alert/Toast và đẩy người dùng về trang Login.

---

## 🎯 TASK 2: Xây dựng màn hình Xác thực Email

**Tài liệu tham chiếu:** `[SCR-SHR-03]`, `[API-006]`, `[API-103]`

**Hành động yêu cầu:**
1. Khởi tạo Component `views/Auth/VerifyEmail.vue`:
   - Đây là một trang Loading/Trung gian xử lý khi user click vào link từ email do Laravel gửi.
   - Đọc URL do Backend cấp và dùng Axios để gọi API Verify `[API-006]`.
   - Báo thành công (Success State) và hướng dẫn người dùng chuyển hướng vào Trang chủ.
2. Xử lý logic "Gửi lại email xác thực" (Resend Email):
   - Nếu User vừa đăng ký xong nhưng lỡ tắt tab email, cần có nút/banner "Chưa nhận được email? Gửi lại".
   - Gọi `api.post('/listener/auth/email/resend')` (Hoặc dùng template chuỗi `${role}` tương tự như Login) để kích hoạt `[API-103]`.

---
*(Hết Prompt. Vui lòng phản hồi "Đã hiểu và sẵn sàng bắt đầu Task 1" để chúng ta tiến hành).*
