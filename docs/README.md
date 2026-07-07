# 🎵 Music Streaming Platform - Documentation

Chào mừng đến với hệ thống tài liệu cốt lõi của dự án. Tài liệu được thiết kế theo cấu trúc **Hybrid** (cân bằng giữa tốc độ của Solo Dev và sự chặt chẽ của Enterprise), tối ưu hóa để AI Agent có thể đọc hiểu và hỗ trợ viết code.

## 📁 Cấu trúc Tài liệu (The Core 6 + Screens)

Hệ thống tài liệu xoay quanh **6 File Cốt Lõi** và một thư mục chi tiết cho Frontend.

### The Core
*   [01-functional-specification.md](./01-functional-specification.md) - Đặc tả Tính năng & Yêu cầu nghiệp vụ.
*   [02-database-design.md](./02-database-design.md) - Sơ đồ Cơ sở dữ liệu (Tables, Relationships).
*   [03-system-architecture.md](./03-system-architecture.md) - Kiến trúc Tổng thể (Backend & Frontend).
*   [04-api-documentation.md](./04-api-documentation.md) - Đặc tả API (Endpoints, Request/Response).
*   [05-development-guidelines.md](./05-development-guidelines.md) - Nguyên tắc Code, Naming Conventions.
*   [06-frontend-architecture.md](./06-frontend-architecture.md) - Cấu trúc thư mục Frontend, State Management.

### Screens & UI Matrix
*   [screens/](./screens/) - Thư mục chứa chi tiết từng màn hình. Mỗi màn hình là 1 file riêng biệt để dễ theo dõi State, Layout và API.

---

## 🏷 Hệ thống Mã định danh (ID System)

Để AI có thể liên kết các thành phần với nhau, dự án áp dụng quy tắc Đánh Mã Định Danh (ID Cross-referencing). Mọi thành phần khi được nhắc đến trong tài liệu **bắt buộc** phải kèm theo mã ID của nó.

| Loại (Type) | Tiền tố (Prefix) | Ví dụ (Example) | Nơi định nghĩa chính |
| :--- | :--- | :--- | :--- |
| **Feature** | `FEAT-` | `[FEAT-001]` (Phát nhạc) | `01-functional-specification.md` |
| **Screen** | `SCR-` | `[SCR-AUTH-01]` (Màn hình Đăng nhập) | `screens/SCR-AUTH-01.md` |
| **API** | `API-` | `[API-AUTH-01]` (Login) | `04-api-documentation.md` |
| **Database** | `DB-` | `[DB-users]` (Bảng Users) | `02-database-design.md` |

> **Ví dụ về cách liên kết:**
> Tại màn hình `[SCR-AUTH-01]`, khi người dùng nhấn nút, hệ thống sẽ gọi `[API-AUTH-01]` để lưu thông tin vào bảng `[DB-users]`.
