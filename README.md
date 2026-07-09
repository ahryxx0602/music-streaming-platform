# 🎵 Music Streaming Platform (Nền tảng Âm nhạc Trực tuyến)

Một ứng dụng Web Streaming Âm nhạc hiệu năng cao được xây dựng theo kiến trúc **Modular Monolith**. Nền tảng này đóng vai trò cầu nối giữa **Người nghe (Listeners)** và **Nghệ sĩ (Artists)** thông qua một hệ sinh thái âm nhạc bảo mật, mượt mà và dễ dàng mở rộng.

---

## 🚀 Tính năng cốt lõi

- **Hệ thống phân quyền (Strict Role-Based):** Phân tách độc lập các module dành cho Guest (Khách), Listener (Người nghe), Artist (Nghệ sĩ) và Admin.
- **Không gian Nghệ sĩ (Artist Workspace):** Cho phép nghệ sĩ trực tiếp Upload nhạc. Hệ thống tự động xử lý file âm thanh (cắt preview 30s) thông qua Background Job tích hợp FFmpeg.
- **Chống Cheat Lượt nghe (Anti-Cheat Streaming):** Cơ chế bảo mật tinh vi kết hợp Signed URLs (có thời hạn) và xác thực Session/IP để chặn đứng các hành vi spam/cày view bất hợp pháp.
- **Lưu trữ Media mạnh mẽ:** Toàn bộ file âm thanh và hình ảnh được quản lý qua S3-compatible Object Storage (MinIO).
- **Tối ưu Hiệu năng (High Performance):** Kiến trúc tách biệt Web Node và Media Node. Tận dụng tối đa Redis để Cache, Rate Limit và lưu trữ B-Tree Index chuyên sâu trên MySQL.

---

## 🛠 Công nghệ sử dụng (Tech Stack)

### Backend
- **Framework:** Laravel 13.x (PHP 8.3+)
- **Kiến trúc:** Modular Monolith
- **Cơ sở dữ liệu:** MySQL 8
- **Cache & Queue:** Redis
- **Media Processing:** FFmpeg
- **Storage:** MinIO (S3 API)
- **Authentication:** Laravel Sanctum SPA (HttpOnly Cookie bảo mật)

### Frontend
- **Framework:** Vue 3 (Composition API)
- **State Management:** Pinia
- **Styling:** CSS / TailwindCSS
- **HTTP Client:** Axios

---

## 📂 Cấu trúc dự án

Thay vì gộp chung, dự án được tổ chức theo mô hình **Monorepo** với 2 hệ thống Frontend và Backend tách biệt độc lập. Đặc biệt, Backend sử dụng kiến trúc **Modular Monolith** (thông qua `nwidart/laravel-modules`) để gom nhóm logic theo Domain:

```text
music-streaming-platform/
 ├── backend/                 # 🔥 API Server (Laravel 13)
 │    ├── Modules/            # Kiến trúc Modular Monolith lõi:
 │    │    ├── Authentication/  # Xác thực, Đăng nhập (Sanctum SPA)
 │    │    ├── Users/           # Quản lý hồ sơ, Follow
 │    │    ├── Music/           # Bài hát, Album, Xử lý Stream âm thanh
 │    │    ├── Playlist/        # Quản lý Playlist & Yêu thích
 │    │    ├── Artist/          # Workspace Nghệ sĩ, Upload nhạc
 │    │    ├── Analytics/       # Thống kê lượt nghe, Trending
 │    │    ├── Notification/    # Thông báo thời gian thực (Reverb)
 │    │    └── Administration/  # CMS Quản trị hệ thống, Kiểm duyệt
 │    └── ...
 │
 ├── frontend/                # 🎨 Web Application (Vue 3 SPA)
 │    ├── src/
 │    │    ├── components/      # UI Components dùng chung
 │    │    ├── pages/           # Màn hình (Views)
 │    │    ├── stores/          # State Management (Pinia)
 │    │    └── services/        # Gọi API qua Axios
 │    └── ...
 │
 └── docs/                    # 📚 Tài liệu kỹ thuật, API và UI Specs
```

---

## 📚 Hệ thống Tài liệu (Documentation)

Dự án được đặc tả kỹ thuật toàn diện ở mức Production-ready. Toàn bộ tài liệu kiến trúc, Database, API và UI/UX Specs được lưu trữ trong thư mục `docs/`:

### Kiến trúc & Nghiệp vụ (System & Business)
1. `01-functional-specification.md` - Đặc tả Yêu cầu & Business Rules.
2. `02-database-design.md` - Thiết kế CSDL (3NF) & Ràng buộc toàn vẹn.
3. `03-system-architecture.md` - Sơ đồ Hệ thống, Event-driven & Caching.
4. `04-api-documentation/` - Thư mục chứa tài liệu Giao tiếp RESTful API phân theo Domain.
5. `05-development-guidelines.md` - Quy chuẩn Code, Cấu trúc thư mục Module.
6. `06-frontend-architecture.md` - Kiến trúc Frontend Vue 3 & Global Audio Player.
7. `07-api-registry.md` - **Single Source of Truth** về mã định danh API `[API-xxx]`.
8. `08-developer-commands.md` - Danh sách các lệnh CLI để phát triển Backend và Frontend.

### UI/UX Screen Specs (Thiết kế giao diện)
Thư mục `docs/screens/` chứa 27+ tài liệu đặc tả chi tiết cho từng màn hình (Screen Specs), được phân rã theo User Journey:
- **`inventory.md`** - Bảng tổng hợp toàn bộ mã định danh màn hình (`[SCR-xxx]`).
- `Auth/` & `Public/` - Các màn hình tĩnh, Đăng nhập, Đăng ký, Cài đặt bảo mật, Notification.
- `Listener/` - Trải nghiệm nghe nhạc, Khám phá, Quản lý Thư viện.
- `Artist/` - Bảng điều khiển của Nghệ sĩ, Upload file multipart.
- `System/` - Các màn hình quản trị của Admin (Kiểm duyệt, Phân quyền RBAC, Thống kê).

---

## 🔒 Security Checklist

Dự án tuân thủ nghiêm ngặt các nguyên tắc bảo mật:
- Chống **XSS** thông qua SPA Cookie thay vì Local Storage.
- Chống **SQL Injection** hoàn toàn qua Eloquent ORM.
- **Mass Assignment** được kiểm soát bằng `$fillable`.
- Chống **DDoS & Spam** bằng Rate Limiting (Redis) tại các route Upload và Track Stream.
- Giao dịch DB an toàn với **Database Transactions**.

---

*Được kiến trúc và phát triển với tiêu chuẩn System Design nghiêm ngặt của Senior Engineer.*
