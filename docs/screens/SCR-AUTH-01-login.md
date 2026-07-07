# [SCR-AUTH-01] Màn hình Đăng nhập (Login)

> **Mô tả ngắn:** Màn hình cho phép người dùng (Listeners, Artists, Admins) nhập thông tin tài khoản để truy cập vào hệ thống. Hỗ trợ xác thực qua hệ thống Sanctum Cookie-based.

## 1. Thông tin chung (Meta)
*   **Module:** Authentication
*   **Route / URL:** `/login`
*   **Layout sử dụng:** `AuthLayout.vue`
*   **Quyền truy cập:** `Guest` (Chỉ người chưa đăng nhập mới vào được).

## 2. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
Quá trình đăng nhập trải qua 2 bước bắt buộc của Laravel Sanctum SPA:
1.  **Lấy CSRF Token:**
    *   `[API-AUTH-00]` - `GET /sanctum/csrf-cookie` (Được Axios tự động gọi).
2.  **Gửi thông tin xác thực (Mutations):**
    *   `[API-AUTH-01]` - `POST /api/auth/login`
    *   Payload gửi đi: `email`, `password`, `remember_me`.

### State Management (Pinia)
*   **Store:** `authStore.js`
*   **State:** `user`, `isAuthenticated`, `role`
*   **Actions:** 
    *   `login(credentials)`: Gọi `[API-AUTH-01]`, lưu thông tin trả về vào State, và điều hướng người dùng.

## 3. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-AUTH-01]:** Email phải đúng định dạng, bắt buộc điền. Password tối thiểu 8 ký tự.
*   **[RULE-AUTH-02]:** Nếu gọi `[API-AUTH-01]` trả về lỗi `401 Unauthorized` hoặc `422 Validation Error`, phải hiển thị dòng text thông báo màu đỏ ngay dưới ô input tương ứng.
*   **Điều hướng (Redirect) sau khi login thành công:**
    *   Nếu `user.role === 'Admin'` ➔ Chuyển hướng tới `[SCR-ADMIN-01]` (Admin Dashboard).
    *   Nếu `user.role === 'Artist'` ➔ Chuyển hướng tới `[SCR-ARTIST-01]` (Artist Dashboard).
    *   Nếu `user.role === 'Listener'` ➔ Chuyển hướng tới `[SCR-EXPLORE-01]` (Trang chủ/Khám phá).

## 4. Tác động Cơ sở dữ liệu (Database Impact)
*   **Kiểm tra dữ liệu (Read):** Bảng `[DB-users]` để đối chiếu `email` và Hash của `password`. Kiểm tra cột `status` xem tài khoản có bị khóa không.
*   **Ghi dữ liệu (Write):** Không trực tiếp ghi, nhưng Laravel Sanctum có thể cập nhật thời gian đăng nhập hoặc tạo Session trong bảng `[DB-sessions]`.
