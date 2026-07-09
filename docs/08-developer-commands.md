# CÁC LỆNH PHÁT TRIỂN DỰ ÁN (DEVELOPER COMMANDS CHEATSHEET)

Tài liệu này liệt kê toàn bộ các lệnh CLI (Command Line Interface) cần thiết để làm việc trên dự án Music Streaming. Dự án được chia thành 2 thư mục riêng biệt: `backend/` và `frontend/`. Bạn cần mở Terminal và di chuyển (`cd`) vào đúng thư mục trước khi chạy lệnh.

---

## 1. Môi trường Backend (Laravel Modular / PHP)
*Tất cả các lệnh dưới đây phải được thực thi bên trong thư mục `/backend`.*

| Lệnh | Ý nghĩa / Mục đích |
| :--- | :--- |
| `php artisan serve` | Khởi chạy máy chủ Backend (mặc định tại `http://localhost:8000`). |
| `php artisan module:make TênModule --api` | **Tạo Module API mới** (Ví dụ: `php artisan module:make Auth --api`). |
| `php artisan module:make-controller TênController TênModule --api`| Tạo một Controller chuẩn API bên trong Module cụ thể. |
| `php artisan module:make-model TênModel TênModule -m` | Tạo Model kèm theo file Migration bên trong Module. |
| `php artisan module:make-request TênRequest TênModule` | Tạo FormRequest để Validate dữ liệu đầu vào. |
| `php artisan migrate` | Cập nhật cấu trúc CSDL theo các file Migrations. |
| `php artisan db:seed` | Bơm dữ liệu mẫu (Fake Data) vào Database. |
| `php artisan tinker` | Mở giao diện dòng lệnh tương tác REPL của Laravel để test code/DB nhanh. |

---

## 2. Môi trường Frontend (Vue 3 / TypeScript)
*Tất cả các lệnh dưới đây phải được thực thi bên trong thư mục `/frontend`.*

| Lệnh | Ý nghĩa / Mục đích |
| :--- | :--- |
| `npm run dev` | Khởi chạy máy chủ Frontend (Vite) ở chế độ phát triển (Hỗ trợ Hot-Reload). |
| `npm run generate` | **Khởi động Trình sinh code (Plop.js).** Hệ thống sẽ hỏi bạn muốn tạo View hay Component, sau đó tự sinh file `.vue` kèm TypeScript vào đúng vị trí. |
| `npm run lint` | Quét toàn bộ lỗi kiểu dữ liệu (như lỗi `any`), cú pháp TypeScript và cảnh báo (ESLint). |
| `npm run format` | Tự động căn lề, định dạng lại toàn bộ mã nguồn theo chuẩn Prettier. |
| `npm run build` | Đóng gói toàn bộ mã nguồn Frontend ra thư mục `dist/` để đẩy lên Server chạy thực tế. |
| `npm install` | Cài đặt toàn bộ các gói phụ thuộc (Dependencies) khi vừa tải dự án về máy. |
