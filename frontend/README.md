# Music Streaming Platform - Frontend (Vue 3 SPA)

Dự án giao diện người dùng (Frontend) của nền tảng nghe nhạc trực tuyến, được phát triển trên kiến trúc Single Page Application (SPA) đem lại trải nghiệm mượt mà, phản hồi nhanh.

## 🛠️ Công nghệ sử dụng (Tech Stack)
- **Framework Chính:** Vue 3 (Composition API với `<script setup>`)
- **Quản lý Trạng thái:** Pinia 3
- **Định tuyến (Routing):** Vue Router 5
- **Giao diện & CSS:** TailwindCSS 4
- **HTTP Client:** Axios (Tích hợp bảo mật tự động kèm X-XSRF-TOKEN với Laravel Sanctum)
- **Công cụ Build:** Vite 8
- **Ngôn ngữ lập trình:** TypeScript
- **Tự động hóa (DX):** `unplugin-auto-import` & `unplugin-vue-components`
- **Code Generator:** Plop

---

## 📂 Cấu trúc thư mục tiêu chuẩn (Directory Structure)
```text
frontend/
    ├── plop-templates/ # Các mẫu code (templates) dùng cho Plop Generator
    ├── public/         # Tài nguyên tĩnh (ảnh, icon)
    ├── src/
    │   ├── assets/     # Styles (Tailwind base) và Fonts
    │   ├── components/ # Các Component độc lập và dùng lại nhiều (Button, Card, AudioPlayer)
    │   ├── composables/# Các logic có thể tái sử dụng (VD: useAuth, useAudio)
    │   ├── layouts/    # Bố cục trang (MainLayout, AuthLayout, AdminLayout)
    │   ├── pages/      # Tương ứng với các Màn hình (Route Views)
    │   ├── router/     # Cấu hình Vue Router và các Route Guards (Bảo vệ tuyến đường)
    │   ├── services/   # Tập trung các hàm gọi API (Axios instance)
    │   ├── stores/     # Quản lý State toàn cục bằng Pinia
    │   ├── types/      # Định nghĩa các TypeScript Interface / Type
    │   ├── App.vue     # Component gốc (Root Component)
    │   └── main.ts     # Điểm khởi chạy của ứng dụng (Entry point)
    ├── eslint.config.ts# Cấu hình ESLint chuẩn hóa mã nguồn
    └── vite.config.ts  # Cấu hình Vite, Alias và Plugins
```

---

## 🚀 Hướng dẫn Cài đặt & Khởi chạy

1. **Cài đặt các gói phụ thuộc (Node Modules):**
```bash
npm install
```

2. **Cấu hình Biến môi trường:**
Tạo file `.env` hoặc copy từ `.env.example` (nếu có), trỏ đường dẫn URL về Backend API:
```env
VITE_API_URL=http://localhost:8000
```

3. **Chạy Môi trường Phát triển (Local Development):**
```bash
npm run dev
```

4. **Biên dịch Môi trường Production:**
```bash
npm run build
```

---

## 🔧 Tiêu chuẩn Phát triển (Coding Conventions)
- **Style Component:** Toàn bộ component sử dụng chuẩn SFC (Single File Component) của Vue 3 với thẻ `<script setup lang="ts">`.
- **Tên Component:** Đặt tên theo PascalCase, dùng từ ghép đa âm tiết để tránh xung đột HTML (VD: `GlobalAudioPlayer.vue`).
- **UI & Styling:** Tuân thủ 100% TailwindCSS. Hạn chế tối đa việc viết tag `<style>` tùy chỉnh trừ phi sử dụng cho Micro-animations phức tạp.
- **Bảo mật (Sanctum):** Hệ thống không dùng Bearer Token lưu ở `localStorage`. Giao tiếp với API qua phương thức Cookie-based, đảm bảo bảo vệ khỏi tấn công XSS. Backend sẽ trả Session Cookie `HttpOnly`.

## 📝 NPM Scripts hữu ích
- `npm run dev`: Mở server dev (Hỗ trợ Hot Module Replacement).
- `npm run build`: Type-check và đóng gói ứng dụng.
- `npm run lint`: Quét và báo lỗi mã nguồn bằng ESLint và Oxlint (nhanh).
- `npm run format`: Format lại code chuẩn chỉ theo Prettier.
- `npm run generate`: Kích hoạt Plop để tự động sinh file/thư mục chuẩn cho một Component, View hoặc Store mới (Tăng tốc độ code đáng kể).
