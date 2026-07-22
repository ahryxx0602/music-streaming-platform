---
name: backend-auto-test
description: Kỹ năng tự động chạy PHPUnit Test (Backend Laravel), đọc kết quả lỗi và tự động vá lỗi source code (Auto-fix) theo chuẩn TDD.
---
# BACKEND AUTO TEST & FIX PROTOCOL

Kỹ năng này biến Agent thành một kỹ sư TDD (Test-Driven Development). Khi kích hoạt, bạn BẮT BUỘC thực hiện quy trình vòng lặp sau một cách tự chủ:

### Bước 1: Khởi động Test
1. Làm việc tại thư mục `backend/`.
2. Sử dụng công cụ chạy terminal (run_command) để chạy lệnh: `php artisan test` (Hoặc có thể chạy riêng một module cụ thể: `php artisan test Modules/Authentication`).
3. Chờ lệnh trả về kết quả (Stdout/Stderr).

### Bước 2: Phân tích & Tự động vá lỗi (Auto-Fix)
- **Nếu PASS 100%:** Dừng vòng lặp, xuất thông báo chúc mừng User.
- **Nếu FAILED:** 
  1. Phân tích kĩ Stack Trace. Xác định chính xác file và dòng code gây lỗi.
  2. Truy xuất file mã nguồn (`.php`) bằng lệnh xem file.
  3. Suy luận nguyên nhân (Do logic sai, Validation bị strict, Mock Object sai cấu trúc, thiếu Route, hoặc chưa migrate DB).
  4. Sử dụng công cụ sửa file (VD: `multi_replace_file_content`) để **FIX TRỰC TIẾP** mã nguồn. (Lưu ý: Luôn ưu tiên sửa file Logic gốc thay vì đi sửa file Test để ăn gian, trừ khi file Test viết sai kịch bản).

### Bước 3: Xác minh lại (Re-test)
- Lập tức QUAY LẠI BƯỚC 1.
- Liên tục lặp lại Vòng lặp Code -> Test -> Fix này cho đến khi Terminal báo Pass 100%.
- **Safety Net (Chốt an toàn):** Nếu mắc kẹt chung 1 lỗi quá 3 lần liên tiếp, Agent phải tạm dừng vòng lặp, in ra phân tích chuyên sâu (Deep Dive Analysis) và xin lời khuyên từ User.
