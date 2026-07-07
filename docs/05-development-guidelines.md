# HƯỚNG DẪN PHÁT TRIỂN & TIÊU CHUẨN (DEVELOPMENT GUIDELINES)

Tài liệu này định nghĩa các tiêu chuẩn phát triển tinh gọn (Lean) dành cho mô hình **Solo Developer + AI Agent**, bỏ qua các rào cản hành chính (bureaucracy) để tập trung vào tốc độ và tính nhất quán.

## 1. Triết lý làm việc (Core Philosophy)
- **AI-First Documentation**: Tài liệu được viết với mục đích chính là cung cấp Context (Ngữ cảnh) cho AI Agent lập trình thay vì để con người review.
- **Single Source of Truth (SSOT)**: 
  - `02-database-design.md` là nguồn sự thật duy nhất cho DB.
  - `04-api-documentation.md` là nguồn sự thật duy nhất cho API.
  - `screens/*.md` là nguồn sự thật duy nhất cho Frontend.
- **Agility over Bureaucracy**: Code xong, chạy được là Push. Không cần quy trình duyệt (Review) phức tạp.

## 2. Tiêu chuẩn Đặt tên & Coding (Naming Conventions)

### 2.1. Backend (Laravel / PHP)
- **Class / Model / Controller**: PascalCase (VD: `UserController`, `SongRepository`).
- **Method / Function / Biến**: camelCase (VD: `getUserInfo()`, `$isPlaying`).
- **Database Table**: snake_case, số nhiều (VD: `artist_profiles`, `playlist_songs`).
- **Database Column**: snake_case (VD: `cover_image`, `created_at`).
- **API Response**: Bắt buộc tuân thủ JSend format thông qua `ApiResponseTrait`.

### 2.2. Vị trí lưu trữ Kiến trúc (Folder Structure Rules)
Dự án áp dụng mô hình **Modular Monolith** kết hợp với thư mục Global.
*   **Global (Dùng chung toàn hệ thống):**
    *   `app/Enums/`: Lưu các Enum dùng chung (VD: `RoleEnum.php`).
    *   `app/Traits/`: Lưu các Trait dùng chung (VD: `ApiResponseTrait.php`).
    *   `app/Exceptions/`: Custom Exceptions hệ thống.
    *   `app/Models/`: Model mặc định nếu không thuộc module nào (VD: `User.php`).
*   **Modular (Nghiệp vụ chuyên biệt):**
    *   Nằm tại `app/Modules/<Tên_Module>/`.
    *   Mỗi Module có thể chứa: `Controllers`, `Services` (chứa Business Logic), `Repositories` (Truy vấn DB), `Events`, `Listeners`, `Observers` (Bắt sự kiện DB của Model), `Requests`, `Jobs`.

### 2.3. Frontend (Vue 3 / JS)
- **Component Vue**: PascalCase (VD: `GlobalPlayer.vue`, `SongCard.vue`).
- **Biến / Hàm JS**: camelCase (VD: `handlePlay()`, `currentUser`).
- **CSS Class (Tailwind)**: kebab-case (VD: `text-red-500`, `btn-primary`).
- **Store (Pinia)**: Đặt tên theo domain (VD: `useAuthStore`, `usePlayerStore`).

## 3. Hệ thống Mã định danh & Liên kết (ID Traceability)
Để AI có thể hiểu được sự phụ thuộc giữa các file, mọi thành phần cốt lõi đều được gắn ID (Mã định danh).

| Loại Entity | Tiền tố | Ví dụ | Ý nghĩa / Quy tắc sử dụng |
| :--- | :--- | :--- | :--- |
| **Feature** | `[FEAT-XX]` | `[FEAT-01]` | Các tính năng lớn cấp độ Product. Định nghĩa tại file số `01`. |
| **Database** | `[DB-table]` | `[DB-users]` | Bảng trong Database. Định nghĩa tại file số `02`. |
| **API** | `[API-XX]` | `[API-AUTH-01]` | Endpoint kết nối FE và BE. Định nghĩa tại file số `04`. |
| **Screen** | `[SCR-XX]` | `[SCR-AUTH-01]` | Màn hình giao diện Frontend. Nằm trong thư mục `screens/`. |
| **Rule** | `[RULE-XX]` | `[RULE-01]` | Các logic ràng buộc khắt khe (Validation, Permission). |

**Quy tắc tham chiếu:** 
Tuyệt đối không lặp lại nội dung. Nếu Màn hình A cần gọi API B, chỉ cần viết: `"Giao diện gọi [API-AUTH-01]"`. AI sẽ tự động qua file số `04` để đọc hợp đồng API.

## 4. Quy trình Git (Trunk-based Development)
Làm việc một mình, chỉ cần dùng 2 nhánh:
- `main` (hoặc `master`): Môi trường Production, chỉ merge vào khi release.
- `develop`: Nhánh làm việc chính. Commit code hàng ngày trực tiếp vào nhánh này.
- **Commit Message Format**: Theo chuẩn Conventional Commits.
  - `feat: [Mô tả tính năng]`
  - `fix: [Mô tả lỗi]`
  - `docs: [Sửa tài liệu]`
  - `chore: [Cấu hình hệ thống, dọn dẹp]`

## 5. Chỉ thị cho AI Agent (AI Directives)
Khi AI nhận task code, buộc phải làm theo 3 bước:
1. **Tìm kiếm (Search)**: Tìm Mã ID tương ứng của tính năng được giao trong thư mục `docs/`.
2. **Nạp Ngữ cảnh (Context)**: Đọc file Screen tương ứng, dò các ID của API và Database liên quan để nạp vào Context.
3. **Xác minh (Verify)**: Đảm bảo viết code đúng với JSend response và Naming Conventions ở trên. Code xong phải kiểm tra lại (test) trước khi trả kết quả.
