# TIÊU CHUẨN PHÁT TRIỂN & CẤU TRÚC CODE (DEVELOPMENT GUIDELINES)

**Dự án:** Nền tảng Âm nhạc Trực tuyến (Audio Streaming Web App)
**Phiên bản:** 1.0

---

## 1. Cấu trúc Source Code (Modular Monolith)

Dự án áp dụng mô hình **Modular Monolith** nhằm chia nhỏ các Domain nghiệp vụ cho dễ bảo trì và thuận tiện cho việc nâng cấp, mở rộng trong tương lai.

**Cấu trúc thư mục Backend (Laravel 13):**
```text
app/
├── Modules/
│   ├── Authentication/    (Login, Register, Sanctum, Password Reset)
│   ├── User/              (Profile, Follow, Favorites, History)
│   ├── Music/             (Song, Album, Streaming, Track Stream, Search)
│   ├── Artist/            (Artist Profile, Analytics, Workspace, Upload)
│   ├── Playlist/          (Playlist, Playlist Songs)
│   ├── Admin/             (Ban/Unban, Verify, Approve/Reject, Dashboard)
│   └── Shared/            (Common Helpers, Base Abstract Classes, Interfaces)
```

**Nguyên tắc giao tiếp:**
- Một Module (VD: `Artist`) **không được phép** trực tiếp `use App\Modules\Music\Models\Song` để query dữ liệu.
- Thay vào đó, giao tiếp phải thông qua **Service Contracts** (Interface) hoặc qua **Events/Listeners** (VD: Dispatch Event `ArtistDeleted` -> `Music` Module tự lắng nghe và ẩn bài hát).
- Đảm bảo **Loose Coupling** (Khớp nối lỏng lẻo) giữa các Module.

---

## 2. Coding Standard (Quy chuẩn Code)

### 2.1 Backend (PHP & Laravel)
- **Chuẩn mã hóa:** Tuân thủ tuyệt đối **PSR-12**.
- **Naming Conventions:**
  - `Model`: PascalCase, số ít (VD: `Song`, `ArtistProfile`).
  - `Table`: snake_case, số nhiều (VD: `songs`, `artist_profiles`).
  - `Controller`: PascalCase, hậu tố Controller (VD: `SongController`).
  - `Method / Function`: camelCase (VD: `getTrendingSongs()`).
  - `Variable`: camelCase (cho logic), hoặc snake_case (nếu lấy từ DB để map chuẩn). Ưu tiên camelCase.
- **Fat Service, Skinny Controller:**
  - **TUYỆT ĐỐI KHÔNG** viết logic nghiệp vụ (Business Logic) hoặc query Database trực tiếp trong Controller.
  - Controller chỉ làm nhiệm vụ: `Nhận Request` -> `Validate (FormRequest)` -> `Gọi Service` -> `Trả về Response (API Resource)`.
- **Dependency Injection:** Dùng Constructor Injection tại Controller/Service, hạn chế tối đa sử dụng Facades và Helper `app()` trực tiếp để dễ viết Unit Test.

### 2.2 Frontend (Vue 3)
- Bắt buộc sử dụng **Composition API** (`<script setup>`).
- Quản lý Global State bằng **Pinia** (Không dùng Vuex).
- Gọi API qua thư viện Axios (Được config `withCredentials: true` để đính kèm HttpOnly Cookie tự động, không dùng Bearer Token ở Local Storage để chống XSS).
- **Naming:**
  - `Component`: PascalCase (VD: `SongPlayer.vue`, `ArtistCard.vue`).
  - `CSS Class`: Tuân thủ BEM (Block Element Modifier) nếu viết thuần, hoặc dùng class TailwindCSS.

---

## 3. Quản lý Git & Branching Strategy

### 3.1 Git Flow Đơn Giản
Hệ thống sử dụng Git Flow cơ bản:
- `main`: Nhánh production, code luôn ở trạng thái chạy ổn định.
- `develop`: Nhánh test và gom code (Staging).
- `feature/<tên-tính-năng>`: Nhánh làm tính năng mới (Tạo từ `develop`, PR merge về lại `develop`).
- `bugfix/<tên-lỗi>`: Nhánh sửa lỗi phát sinh trong lúc dev (Tạo từ `develop`).
- `hotfix/<tên-lỗi>`: Nhánh sửa lỗi khẩn cấp trên Production (Tạo từ `main`, merge ngược về cả `main` và `develop`).

### 3.2 Chuẩn Commit Message (Conventional Commits)
Bắt buộc áp dụng định dạng: `<type>(<scope>): <subject>`

**Types hợp lệ:**
- `feat`: Tính năng mới.
- `fix`: Sửa lỗi.
- `docs`: Cập nhật tài liệu.
- `style`: Format code (khoảng trắng, thiếu chấm phẩy...).
- `refactor`: Viết lại code (không đổi tính năng).
- `perf`: Tối ưu hiệu năng.
- `test`: Thêm/sửa Unit Test.

**Ví dụ:**
- `feat(music): add streaming tracker anti-cheat mechanism`
- `fix(auth): prevent expired token access`

---

## 4. Xử lý Lỗi (Error Handling & Logging)

- Sử dụng `try-catch` ở tầng Service khi thực hiện các tác vụ nguy hiểm: Tương tác DB phức tạp, Gọi external API, Tương tác hệ thống file MinIO, Job FFmpeg.
- Chủ động ném Custom Exceptions thay vì return flag. (VD: `throw new InvalidAudioFormatException();`).
- Ở môi trường Production (`APP_ENV=production`), Laravel Handler phải tự động convert tất cả các Exception thô (Stack trace) thành một JSON format chuẩn theo JSend (`status: error`, `message: Server Error`). Tuyệt đối không lộ SQL Error ra ngoài.
- **Database Transaction:** BẮT BUỘC sử dụng `DB::transaction()` ở Service Layer đối với các luồng Insert/Update phức tạp tác động nhiều bảng (Ví dụ: Upload Song cần lưu bảng `songs`, tạo `job`, cập nhật `genres`...) nhằm đảm bảo tính toàn vẹn dữ liệu (Data Integrity) nếu có lỗi xảy ra giữa chừng.
- Các lỗi nghiệp vụ (Level >= Warning) phải được ghi log (Log::error) vào channel lưu trữ.

---

## 5. Security Checklist (Bảo mật)

1. **SQL Injection:** Dùng Eloquent ORM. Không bao giờ dùng Query Raw có nối chuỗi (String Concatenation).
2. **Mass Assignment:** Tất cả Model phải cấu hình `$fillable` các cột được phép insert/update.
3. **Cross-Site Scripting (XSS):** Nếu hệ thống Comment đi vào hoạt động, backend phải strip tags hoặc frontend sử dụng template interpolation chuẩn (tránh dùng `v-html` không kiểm soát).
4. **Rate Limit:** Tương ứng với Tài liệu 04, bắt buộc gán Middleware `throttle` cho các Router chịu tải.
5. **Authorization / Policy:** Trước khi thực thi thao tác Cập nhật / Xóa, Backend phải gọi Policy kiểm tra xem `user_id` hiện tại có phải là chủ sở hữu không. (VD: Artist A không được phép xóa bài hát của Artist B).

---

## 6. Code Testing (Pest / PHPUnit)

- Back-end ưu tiên sử dụng **Pest** (hiện đại, cú pháp gọn nhẹ) cho việc viết Unit/Feature Test.
- Cần viết Feature Test (Integration Test) cho các luồng quan trọng nhất (Happy Path):
  - Luồng Authentication.
  - Luồng Upload bài hát (Mock MinIO Disk và Queue).
  - Luồng Streaming và Rate Limiting.
  - Logic phân quyền bảo mật (Thử dùng Account Listener để xóa bài hát).
