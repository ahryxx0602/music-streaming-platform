---
name: frontend-auto-test
description: Kỹ năng tự động chạy Unit Test (Frontend Vue 3 / Vitest), đọc kết quả lỗi và tự động vá lỗi Component.
---
# FRONTEND AUTO TEST & FIX PROTOCOL

Kỹ năng chuyên biệt dành cho quá trình kiểm thử Frontend (Vitest / Vue Test Utils). Khi kích hoạt, Agent thực hiện vòng lặp sau:

### Bước 1: Khởi động Test
1. Làm việc tại thư mục `frontend/`.
2. Sử dụng lệnh terminal chạy: `npm run test` (hoặc `npx vitest run` để tránh chạy ở chế độ watch mode gây kẹt terminal).
3. Thu thập toàn bộ log.

### Bước 2: Phân tích & Vá lỗi
- **Nếu PASS 100%:** Thông báo thành công cho người dùng.
- **Nếu FAILED:**
  1. Đọc lỗi từ Console. Một số lỗi phổ biến ở Frontend: 
     - Quên mock Pinia (`createTestingPinia`).
     - Quên mock Vue-i18n (`$t` is not a function).
     - Component render thiếu Props bắt buộc.
     - Sai đường dẫn Import (Relative path).
  2. Sửa trực tiếp file Component `.vue` hoặc file test `.spec.ts` tương ứng.

### Bước 3: Xác minh lại (Re-test)
- Lập tức chạy lại bài test.
- Nếu lặp quá 3 lần không qua, tổng hợp báo cáo và xin ý kiến User.
