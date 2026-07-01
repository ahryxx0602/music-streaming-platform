# 🎵 Nền tảng Âm nhạc Trực tuyến (Audio Streaming Platform)

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
- **Framework:** Laravel 13 (PHP 8.4)
- **Kiến trúc:** Modular Monolith
- **Cơ sở dữ liệu:** MySQL 8
- **Cache & Queue:** Redis
- **Media Processing:** FFmpeg
- **Storage:** MinIO (S3 API)
- **Authentication:** Laravel Sanctum SPA (HttpOnly Cookie bảo mật chống XSS)

### Frontend
- **Framework:** Vue 3 (Composition API)
- **State Management:** Pinia
- **Styling:** CSS / TailwindCSS
- **HTTP Client:** Axios (Interceptors configured)

---

## 📂 Cấu trúc dự án (Modular Monolith)

Thay vì thiết kế MVC truyền thống, backend được tổ chức thành các Domain độc lập (`app/Modules/`) để dễ bảo trì và hạn chế rủi ro scope creep:

```text
app/Modules/
 ├── Authentication/    # Xác thực, Quản lý Session & Cookie
 ├── User/              # Hồ sơ, Follow, Danh sách Yêu thích
 ├── Music/             # Quản lý Bài hát, Album, Luồng Streaming
 ├── Artist/            # Workspace, Upload, Phân tích dữ liệu Artist
 ├── Playlist/          # Quản lý Playlist cá nhân
 └── Admin/             # Bảng điều khiển quản trị, Kiểm duyệt nội dung
```

---

## 📚 Hệ thống Tài liệu (Documentation)

Dự án được đặc tả cực kỳ chi tiết từ Database đến API. Toàn bộ tài liệu được lưu trong thư mục `docs/`:

1. `01-functional-specification.md` - Đặc tả Yêu cầu & Business Rules.
2. `02-database-design.md` - Thiết kế CSDL (3NF) & Ràng buộc toàn vẹn.
3. `03-system-architecture.md` - Sơ đồ Hệ thống, Event-driven & Caching.
4. `04-api-documentation.md` - Chuẩn giao tiếp RESTful API & Error handling.
5. `05-development-guidelines.md` - Bộ quy chuẩn Code, PSR-12, Git Flow & Security.
6. `06-frontend-architecture.md` - Kiến trúc Frontend Vue 3 & Cơ chế Audio Player.

---

## 🔒 Security Checklist

Dự án tuân thủ nghiêm ngặt các nguyên tắc bảo mật:
- Chống **XSS** thông qua SPA Cookie thay vì Local Storage.
- Chống **SQL Injection** hoàn toàn qua Eloquent ORM.
- **Mass Assignment** được kiểm soát bằng `$fillable`.
- Chống **DDoS & Spam** bằng Rate Limiting (Redis) tại các route Upload và Track Stream.
- Giao dịch DB an toàn với **Database Transactions**.

---

*Được kiến trúc và phát triển với các tiêu chuẩn Production-Ready.*
