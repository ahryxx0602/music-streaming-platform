# PROMPT KẾ HOẠCH TRIỂN KHAI MODULE AUTHENTICATION

**Bối cảnh:** Bạn đóng vai trò là Backend Engineer (Laravel). Triển khai Module `Authentication`. Tuân thủ chặt chẽ nguyên tắc SOLID và mô hình Tách biệt Logic theo `05-development-guidelines.md`.

---

## 🎯 TASK 1: Xác thực & Cập nhật Cơ sở dữ liệu (Migrations & Models)

**Tài liệu tham chiếu:** `[DB-users]`, `[DB-artist_profiles]`, `[DB-artist_invitations]`

**Hành động yêu cầu:**
1. **[Verify]**: Kiểm tra hệ thống Database hiện tại. Tuyệt đối KHÔNG chạy lệnh `module:make-model` đè lên các bảng/Models đã tồn tại.
2. Nếu quyết định quản lý tập trung, hãy di chuyển Model `User` vào `Modules/Authentication/Models/` (và sửa Namespace) hoặc giữ nguyên ở `app/Models` nhưng cập nhật các Trait cần thiết.
3. Đảm bảo Model `User` và `ArtistProfile` có định nghĩa quan hệ:
   - Đảm bảo `User` *hasOne* `ArtistProfile`.
   - Đảm bảo `User` sử dụng trait `HasApiTokens` (Sanctum) và `HasRoles` (Spatie).

---

## 🎯 TASK 2: Thiết lập Middleware & Security (Sanctum SPA)

**Tài liệu tham chiếu:** `[API-001]`, `[SCR-SHR-01]`

**Hành động yêu cầu:**
1. Cấu hình Laravel Sanctum chạy ở chế độ SPA (Cookie-based).
2. **Cập nhật biến môi trường (`.env`)**: Khai báo chuẩn xác `SESSION_DOMAIN=localhost` (hoặc `127.0.0.1`) và `SANCTUM_STATEFUL_DOMAINS=localhost:5173` để trình duyệt chấp nhận cross-origin cookie giữa Backend và Frontend Vite.
3. Đảm bảo `config/cors.php` có `supports_credentials = true`.

---

## 🎯 TASK 3: Tách biệt Business Logic (Tầng Actions)

**Tài liệu tham chiếu:** `05-development-guidelines.md`

**Hành động yêu cầu:**
Tạo các class Action độc lập trong thư mục `Modules/Authentication/Actions/`:
1. `LoginAction`: Xử lý `Auth::attempt()` và xác thực dữ liệu.
2. `RegisterListenerAction`: 
   - Khởi tạo dữ liệu User.
   - BẮT BUỘC gọi `$user->assignRole('Listener')` để đồng bộ hệ thống Spatie RBAC.
3. `RegisterArtistAction`:
   - Kiểm tra mã mời.
   - Bọc trong `DB::beginTransaction()`: Tạo User ➔ `$user->assignRole('Artist')` ➔ Tạo `ArtistProfile`.
4. `ValidateArtistTokenAction`:
   - Xác thực token mời Artist (`[API-007]`), kiểm tra hạn sử dụng và trả về thông tin `email` gốc để khóa form UI.

---

## 🎯 TASK 4: Xây dựng Public APIs (Guest Auth)

**Tài liệu tham chiếu:** `[API-002]`, `[API-003]`, `[API-007]`, `[API-008]`

**Hành động yêu cầu:**
1. Tạo các Form Requests validation: `LoginRequest`, `RegisterListenerRequest`, `RegisterArtistRequest`.
   - **Bảo mật [RULE-REG-01]:** Rule Password phải cực kỳ chặt chẽ (vd: `Password::min(8)->letters()->mixedCase()->numbers()->symbols()`).
2. Xây dựng `AuthController`: 
   - `validateArtistToken()`: Trả về email gốc theo `token` để khóa form Frontend.
   - Controller chỉ làm nhiệm vụ tiếp nhận Request, Dependency Injection (DI) các Action ở Task 3, và trả về JSend Response. Tuyệt đối không nhồi nhét Business Logic vào Controller.

---

## 🎯 TASK 5: Xây dựng Protected APIs (Dùng chung) & Xử lý Logout an toàn

**Tài liệu tham chiếu:** `[API-050]` (Me), `[API-051]` (Logout)

**Hành động yêu cầu:**
1. Khai báo Middleware `auth:sanctum` cho các API cần bảo vệ.
2. Viết API lấy thông tin (`me()`): Trả về thông tin User hiện tại kèm theo Profile tương ứng.
3. Viết API Đăng xuất (`logout()`) với chuẩn bảo mật: 
   - Gọi `Auth::guard('web')->logout()`.
   - Gọi `request()->session()->invalidate()`.
   - **Bắt buộc** gọi thêm `request()->session()->regenerateToken()` để phòng chống lỗ hổng CSRF Session Fixation.

---
*(Hết Prompt. Vui lòng phản hồi "Đã hiểu và sẵn sàng bắt đầu Task 1" để chúng ta tiến hành).*
