# Music Streaming Platform - Backend API

Dự án Backend cho nền tảng âm nhạc trực tuyến (Audio Streaming), cung cấp RESTful API hiệu suất cao và kiến trúc dễ mở rộng.

## 🛠️ Công nghệ sử dụng (Tech Stack)
- **Framework:** Laravel 13 (PHP 8.3+)
- **Kiến trúc:** Modular Monolith (Sử dụng `nwidart/laravel-modules`)
- **Cơ sở dữ liệu:** MySQL 8
- **Cache & Queue:** Redis
- **Lưu trữ (Storage):** MinIO (Object Storage hỗ trợ chunk/stream)
- **Real-time (WebSockets):** Laravel Reverb
- **Xác thực:** Laravel Sanctum (Cookie-based SPA)
- **Phân quyền (RBAC):** Spatie Laravel Permission

---

## 📂 Kiến trúc Module (Modular Monolith)

Dự án từ bỏ cấu trúc MVC truyền thống để phân chia theo các Domain nghiệp vụ độc lập:
- `Authentication`: Xử lý đăng nhập, đăng ký, quên mật khẩu (Sanctum SPA).
- `Users`: Quản lý hồ sơ người dùng (Listener).
- `Music`: Quản lý bài hát, album, thể loại và xử lý file âm thanh (FFmpeg).
- `Playlist`: Quản lý danh sách phát và yêu thích cá nhân.
- `Artist`: Không gian làm việc của nghệ sĩ (Upload nhạc, xem báo cáo).
- `Analytics`: Thu thập dữ liệu lượt nghe, xếp hạng Trending.
- `Notification`: Thông báo đẩy (Real-time qua Reverb).
- `Administration`: CMS quản trị hệ thống và kiểm duyệt nội dung.

### Cấu trúc tiêu chuẩn bên trong mỗi Module:
```text
Modules/{ModuleName}/
    ├── Actions/        # Các Class thực thi một nghiệp vụ duy nhất (Single Responsibility)
    ├── Http/
    │   ├── Controllers/# Nhận Request và điều hướng
    │   ├── Requests/   # Validate dữ liệu đầu vào
    │   └── Resources/  # Định dạng dữ liệu JSON trả về (JSend)
    ├── Models/         # Eloquent Models
    ├── Providers/      # Đăng ký Route, Event, Binding...
    ├── Repositories/   # Tầng tương tác với DB (Database Access)
    ├── Routes/         # Khai báo Endpoint (api.php)
    ├── Rules/          # Các Rule kiểm tra dữ liệu Custom
    └── Tests/          # Unit & Feature Test cho module
```

---

## 🚀 Hướng dẫn Cài đặt

1. **Cài đặt các gói phụ thuộc (Dependencies):**
```bash
composer install
```

2. **Khởi tạo cấu hình môi trường:**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Cấu hình `.env`:**
Cập nhật thông tin kết nối cho MySQL, Redis và MinIO (cần đảm bảo MinIO được cấu hình CORS và Accept-Ranges để stream nhạc).

4. **Khởi tạo Database & Chạy Migrations:**
```bash
php artisan migrate
```

5. **Chạy các dịch vụ (Local Development):**
Sử dụng các lệnh sau trong các tab Terminal khác nhau:
```bash
# Khởi động HTTP API Server
php artisan serve

# Khởi động WebSockets Server (Reverb)
php artisan reverb:start

# Khởi động Queue Worker xử lý tác vụ nền (như gửi Email, Convert Audio)
php artisan queue:listen
```

---

## 🧪 Hệ thống Kiểm thử (Testing)

Hệ thống đã được tinh chỉnh `phpunit.xml` để tự động nhận diện và tính toán Code Coverage cho các file test nằm trong các Module.

- Chạy toàn bộ Test:
```bash
php artisan test
```

## 📜 Tiêu chuẩn Phát triển (Coding Conventions)

- **API Response:** Mọi API (kể cả Exception) đều phải trả về định dạng chuẩn **JSend** `{ "status", "message", "data" }` sử dụng class `ApiResponseTrait`. Không được để lọt các phản hồi HTML gây lỗi cho Frontend.
- **Data Access:** Logic truy vấn DB phức tạp phải nằm trong `Repositories`. Không viết logic nghiệp vụ trong Controller.
- **Tác vụ nặng:** Xử lý file, gửi mail hoặc các tính toán lớn phải đẩy vào `Queue` thay vì chờ phản hồi đồng bộ.
