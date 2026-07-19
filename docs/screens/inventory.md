# SCREEN INVENTORY (DANH MỤC MÀN HÌNH)

> [!IMPORTANT]
> **I18N REQUIREMENT:** Tất cả các đoạn Text, Label, Placeholder, Message hiển thị trong tài liệu Screen Specs này khi triển khai vào code thực tế đều **KHÔNG ĐƯỢC HARDCODE**. Bắt buộc phải sử dụng key đa ngôn ngữ qua hàm `$t()` của `vue-i18n`.


Tài liệu này quản lý mã định danh (Screen ID) cho toàn bộ hệ thống Music Streaming Platform. Cấu trúc ID sử dụng chuẩn: `SCR-{ROLE}-{NUMBER}`.

## 📌 Nhóm Public & Shared (Khách & Tính năng chung)

| Screen ID | Tên màn hình / Component | Role Truy Cập | Tệp Spec (Spec File) |
| :--- | :--- | :--- | :--- |
| `SCR-PUB-001` | Landing Page (Trang chủ tĩnh) | Guest | [SCR-PUB-001](./Public/SCR-PUB-001-landing-page.md) |
| `SCR-SHR-01` | Đăng nhập (Login) | Guest | [SCR-SHR-01](./Auth/SCR-SHR-01-login.md) |
| `SCR-SHR-02` | Quên mật khẩu (Password Recovery) | Guest | [SCR-SHR-02](./Auth/SCR-SHR-02-password-recovery.md) |
| `SCR-SHR-03` | Xác thực Email (Email Verification) | Guest/Auth | [SCR-SHR-03](./Auth/SCR-SHR-03-email-verification.md) |
| `SCR-SHR-04` | Cài đặt Tài khoản (Profile & Security) | Listener/Artist | [SCR-SHR-04](./Auth/SCR-SHR-04-security-settings.md) |
| `SCR-SHR-05` | Trung tâm thông báo (Notification Center)| Listener/Artist | [SCR-SHR-05](./Auth/SCR-SHR-05-notification-center.md) |

---

## 📌 Nhóm Listener Journey (Người nghe)

| Screen ID | Tên màn hình | Role Truy Cập | Tệp Spec (Spec File) |
| :--- | :--- | :--- | :--- |
| `SCR-LST-01` | Đăng ký Listener | Guest | [SCR-LST-01](./Auth/SCR-LST-01-register.md) |
| `SCR-LST-02` | Khám phá (Home / Explore) | Listener/Guest | [SCR-LST-02](./Listener/SCR-LST-02-home-explore.md) |
| `SCR-LST-03` | Chi tiết Collection (Album/Playlist) | Listener/Guest | [SCR-LST-03](./Listener/SCR-LST-03-collection-detail.md) |
| `SCR-LST-04` | Global Audio Player | Listener | [SCR-LST-04](./Listener/SCR-LST-04-global-player.md) |
| `SCR-LST-05` | Thư viện cá nhân (My Library) | Listener | [SCR-LST-05](./Listener/SCR-LST-05-my-library.md) |
| `SCR-LST-06` | Tìm kiếm (Search) | Listener/Guest | [SCR-LST-06](./Listener/SCR-LST-06-search.md) |
| `SCR-LST-07` | Hồ sơ Nghệ sĩ (Artist Profile Public)| Listener/Guest | [SCR-LST-07](./Listener/SCR-LST-07-artist-profile.md) |

---

## 📌 Nhóm Artist Journey (Nghệ sĩ)

| Screen ID | Tên màn hình | Role Truy Cập | Tệp Spec (Spec File) |
| :--- | :--- | :--- | :--- |
| `SCR-ART-01` | Đăng ký Artist | Guest | [SCR-ART-01](./Auth/SCR-ART-01-register.md) |
| `SCR-ART-02` | Bảng điều khiển (Dashboard) | Artist | [SCR-ART-02](./Artist/SCR-ART-02-dashboard.md) |
| `SCR-ART-03` | Đăng tải bài hát (Upload Song) | Artist | [SCR-ART-03](./Artist/SCR-ART-03-upload-song.md) |
| `SCR-ART-04` | Quản lý Album (Album Management) | Artist | [SCR-ART-04](./Artist/SCR-ART-04-album-management.md) |
| `SCR-ART-05` | Hồ sơ Artist (Profile Settings) | Artist | [SCR-ART-05](./Artist/SCR-ART-05-profile-settings.md) |

---

## 📌 Nhóm System Admin (Quản trị viên)

| Screen ID | Tên màn hình | Role Truy Cập | Tệp Spec (Spec File) |
| :--- | :--- | :--- | :--- |
| `SCR-ADM-02` | Lời mời Nghệ sĩ (Artist Invites) | Admin | [SCR-ADM-02](./System/SCR-ADM-02-artist-invites.md) |
| `SCR-ADM-03` | Quản lý Người dùng (Users) | Admin | [SCR-ADM-03](./System/SCR-ADM-03-user-management.md) |
| `SCR-ADM-04` | Kiểm duyệt Nội dung (Moderation) | Admin | [SCR-ADM-04](./System/SCR-ADM-04-song-moderation.md) |
| `SCR-ADM-05` | Quản lý Banners | Admin | [SCR-ADM-05](./System/SCR-ADM-05-banner-management.md) |
| `SCR-ADM-06` | Quản lý Phân quyền (RBAC/Roles) | Admin | [SCR-ADM-06](./System/SCR-ADM-06-rbac-roles.md) |
| `SCR-ADM-07` | Quản lý Thể loại (Genres) | Admin | [SCR-ADM-07](./System/SCR-ADM-07-genre-management.md) |
| `SCR-ADM-08` | Nhật ký Hệ thống (Audit Logs) | Admin | [SCR-ADM-08](./System/SCR-ADM-08-audit-logs.md) |
| `SCR-ADM-09` | Thống kê (Dashboard Analytics) | Admin | [SCR-ADM-09](./System/SCR-ADM-09-dashboard-analytics.md) |
| `SCR-ADM-10` | Kho bài hát (Song Inventory) | Admin | [SCR-ADM-10](./System/SCR-ADM-10-song-inventory.md) |
| `SCR-ADM-11` | Playlists Hệ thống (System Playlists)| Admin | [SCR-ADM-11](./System/SCR-ADM-11-system-playlists.md) |
| `SCR-ADM-12` | Cấu hình Hệ thống (System Settings) | Admin | [SCR-ADM-12](./System/SCR-ADM-12-system-settings.md) |
