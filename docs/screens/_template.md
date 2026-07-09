# [SCR-MODULE-XX] Tên Màn Hình

> **Mô tả ngắn:** Mô tả chức năng và mục đích của màn hình này.

## 1. Thông tin chung (Meta)
*   **Module:** Tên Module (VD: Authentication, Player)
*   **Route / URL:** `/duong-dan-cua-man-hinh`
*   **Layout sử dụng:** `TênLayout.vue`
*   **Quyền truy cập:** Mã quyền truy cập `[PER-XXX]` (VD: `[PER-GUEST]`, `[PER-ARTIST-DASHBOARD]`)
*   **Component con (Children):**
    *   `Component1.vue` (Mô tả chức năng component)
    *   `Component2.vue` (Mô tả chức năng component)

## 2. Thành phần giao diện (UI Elements)
*Liệt kê các thành phần tương tác chính trên màn hình để Frontend dễ dàng thiết kế UI và kết nối Payload.*
*   **Các ô nhập liệu (Inputs):**
    *   `Input 1`: Loại (text/password/email), mô tả chức năng...
    *   `Input 2`: ...
*   **Các nút bấm (Buttons & Links):**
    *   `Button Primary`: Nút chính (VD: Đăng nhập, Submit). Điều hướng hoặc gọi API nào.
    *   `Text Link`: Chuyển hướng sang màn hình nào.

## 3. Liên kết dữ liệu & Logic (State & APIs)
Màn hình này tương tác với các thành phần dữ liệu sau:

### Tương tác API (Network)
*   **Lấy dữ liệu (Fetch):**
    *   `[API-XXX-01]` - `GET /api/v1/duong-dan` (Lấy danh sách abc...)
*   **Gửi dữ liệu (Mutations):**
    *   `[API-XXX-02]` - `POST /api/v1/duong-dan` (Tạo mới abc...)

### State Management (Pinia)
*   **Store:** `tenStore.ts`
*   **Actions:** `actionName()`, `anotherAction()`

## 4. Quy tắc nghiệp vụ (Business Rules)
*   Liệt kê các quy tắc validate form.
*   Liệt kê luồng phân quyền (Ai được xem màn hình này).
*   Ví dụ: Nếu chưa login, đẩy về `[SCR-AUTH-01]`.

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*(Chỉ mang tính chất tham khảo cho AI khi code Frontend & Backend chung)*
*   Đọc từ: `[DB-table_name]`
*   Ghi vào: `[DB-table_name]`
