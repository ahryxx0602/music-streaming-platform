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

## 2. Liên kết dữ liệu & Logic (State & APIs)
Màn hình này tương tác với các thành phần dữ liệu sau:

### Tương tác API (Network)
*   **Lấy dữ liệu (Fetch):**
    *   `[API-XXX-01]` - `GET /api/duong-dan` (Lấy danh sách abc...)
*   **Gửi dữ liệu (Mutations):**
    *   `[API-XXX-02]` - `POST /api/duong-dan` (Tạo mới abc...)

### State Management (Pinia)
*   **Store:** `tenStore.js`
*   **Actions:** `actionName()`, `anotherAction()`

## 3. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-XXX-01]:** Liệt kê các quy tắc validate form. Nếu fail trả về mã lỗi nào (VD: `[ERR-XXX-422]`).
*   **[RULE-XXX-02]:** Liệt kê luồng phân quyền và trạng thái. Trả về `[ERR-XXX-403]` nếu vi phạm.
*   Ví dụ: Nếu chưa login, đẩy về `[SCR-AUTH-01]`.

## 4. Tác động Cơ sở dữ liệu (Database Impact)
*(Chỉ mang tính chất tham khảo cho AI khi code Frontend & Backend chung)*
*   Đọc từ: `[DB-table_name]`
*   Ghi vào: `[DB-table_name]`
