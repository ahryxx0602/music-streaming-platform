# PROMPT KẾ HOẠCH TRIỂN KHAI FRONTEND - MODULE AUTHENTICATION

**Bối cảnh:** Bạn đóng vai trò là Frontend Engineer (Vue 3 + TypeScript). Nhiệm vụ của bạn là triển khai giao diện Authentication (Login, Register).
Yêu cầu bạn hãy đọc kỹ kế hoạch dưới đây, phân tích sự liên kết chéo (Cross-Reference) và thực thi tuần tự TỪNG TASK.

---

## 🎯 TASK 1: Xây dựng Layout gốc và Component

**Tài liệu tham chiếu:** 
- N/A (Thuộc kiến trúc Frontend cơ sở)

**Hành động yêu cầu:**
1. Tạo file `src/layouts/AuthLayout.vue`: Thiết kế một layout khung bọc ngoài, hiển thị Form ở giữa màn hình (Ưu tiên thiết kế Dark Mode, CSS Glassmorphism thẩm mỹ cao cho ứng dụng Music).
2. Dùng Plop (`npm run generate`) tạo các UI Component dùng chung nếu cần (như `BaseInput.vue`, `BaseButton.vue`) lưu vào `src/components/base/`.

---

## 🎯 TASK 2: Tích hợp Pinia Store cho Quản lý trạng thái (Auth Store)

**Tài liệu tham chiếu:** 
- APIs: `[API-001]` (CSRF), `[API-002]` (Login), `[API-050]` (Me), `[API-051]` (Logout)

**Hành động yêu cầu:**
1. Tạo store `src/stores/authStore.ts`.
2. Khai báo State: `user` (nullable), `isAuthenticated` (boolean), `role` (string).
3. Viết Action `login(email, password)`: 
   - Bắt buộc phải gọi khởi tạo `api.get('/sanctum/csrf-cookie')` trước.
   - Gửi request tới `[API-002]`.
   - Lấy thông tin user bằng cách gọi `[API-050]` (`/auth/me`) để lưu vào State (chỉ cần gọi 1 API chung, Backend tự xử lý load Profile theo Role).
4. Viết Action `logout()`: Gọi `[API-051]` (`/auth/logout`), xóa State và đá người dùng ra trang Login.

---

## 🎯 TASK 3: Xây dựng các Màn hình (Views)

**Tài liệu tham chiếu:** 
- Screens: `[SCR-SHR-01]` (Login), `[SCR-LST-01]` (Register Listener), `[SCR-ART-01]` (Register Artist)
- APIs: `[API-003]`, `[API-008]`

**Hành động yêu cầu:**
1. Tạo `src/views/auth/LoginView.vue` (Tham chiếu `[SCR-SHR-01]`):
   - Chứa form nhập `email`, `password`. Validate không để trống.
   - Nút Submit gọi Action `login()` trong Pinia Store. 
   - **Xử lý Redirect sau Login:** Đọc Rule `[RULE-AUTH-06]`, nếu role là Admin thì đẩy sang URL Admin, nếu là Artist thì đẩy sang /artist.
2. Tạo `src/views/auth/RegisterListenerView.vue` (Tham chiếu `[SCR-LST-01]`):
   - Form đăng ký thông thường, submit API `[API-003]`.
3. Tạo `src/views/auth/RegisterArtistView.vue` (Tham chiếu `[SCR-ART-01]`):
   - Form có thêm Input `stage_name` (Nghệ danh) và tự động nhận dạng `token` mã mời từ URL Parameter. Gọi API `[API-008]`.

---

## 🎯 TASK 4: Thiết lập Vue Router (Navigation Guards)

**Tài liệu tham chiếu:** 
- Kiến trúc bảo mật Frontend Sanctum.

**Hành động yêu cầu:**
1. Chỉnh sửa `src/router/index.ts`.
2. Khai báo 3 Route Path: `/login`, `/register`, `/artist-register`. Tất cả đều có `meta: { layout: 'AuthLayout', requiresGuest: true }`.
3. Viết `router.beforeEach` Guards:
   - Route nào chứa `requiresGuest: true` mà Store đang báo `isAuthenticated === true` ➔ Lập tức Redirect về trang Home/Dashboard (Ngăn chặn user đã đăng nhập vào lại form Login).

---
*(Hết Prompt. Vui lòng phản hồi "Đã hiểu và sẵn sàng bắt đầu Task 1" để chúng ta tiến hành).*
