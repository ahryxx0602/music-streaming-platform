# [SCR-ADM-07] Quản lý Thể loại (Genre Management)

> **Mô tả ngắn:** Màn hình quản lý danh mục / thể loại nhạc của hệ thống (Master Data). Cho phép Admin tạo, sửa, ẩn các thể loại để cung cấp dữ liệu cho Artist chọn khi Upload nhạc, và phục vụ người nghe phân loại nhạc.

## 1. Thông tin chung (Meta)
*   **Module:** System / Content Management
*   **Route / URL:** `/admin/genres`
*   **Layout sử dụng:** `AdminLayout.vue`
*   **Quyền truy cập:** `[PER-CONTENT-MANAGER]` hoặc `[PER-ADMIN]`
*   **Component con (Children):**
    *   `GenreTreeTable.vue` (Bảng dữ liệu hỗ trợ hiển thị cấu trúc Cây Cha - Con).
    *   `GenreFormModal.vue` (Popup thêm/sửa thông tin Thể loại).

## 2. Thành phần giao diện (UI Elements)
*   **Khu vực Danh sách (Tree Table):**
    *   `Cột hiển thị`: Tên thể loại (Thụt lề cho danh mục con), Slug, Số lượng bài hát thuộc thể loại này, Trạng thái (Bật/Tắt).
    *   `Toggle Switch`: Bật tắt hiển thị nhanh (`is_active`).
    *   `Hành động`: Nút `Thêm danh mục con (Add Child)`, Nút `Sửa`.
*   **Khu vực Form Thêm/Sửa (Genre Form):**
    *   `Danh mục cha (Parent)`: Dropdown Select chọn Thể loại cha (Nếu để trống thì đây là Danh mục gốc - Root).
    *   `Tên thể loại`: Input text (VD: "Nhạc Trữ Tình").
    *   `Đường dẫn (Slug)`: Input text (Tự động điền theo Tên, có thể sửa tay. VD: `nhac-tru-tinh`).
    *   `Icon/Cover`: Nút upload ảnh đại diện cho Thể loại.
    *   `Nút Lưu`: Action button.

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **Lấy dữ liệu (Fetch):**
    *   `[API-370]` - `GET /api/v1/admin/genres` (Lấy dữ liệu toàn bộ Cây thư mục).
*   **Ghi dữ liệu (Mutations):**
    *   `[API-371]` - `POST /api/v1/admin/genres` (Tạo mới).
    *   `[API-372]` - `PUT /api/v1/admin/genres/{id}` (Cập nhật).
    *   *(Không có API Xóa vật lý, chỉ dùng cập nhật is_active = false).*

### State Management (Pinia)
*   **Store:** `useGenreStore.ts`
*   **Actions:** `fetchGenresTree()`, `saveGenre(payload)`.

## 4. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-ADM-GNR-01] - Ngăn vòng lặp vô hạn (Infinite Loop Prevention):** Khi Sửa một Thể loại, Dropdown "Danh mục cha" KHÔNG ĐƯỢC phép chọn chính nó, hoặc chọn các danh mục đang là Con/Cháu của nó. Backend phải validate chặt chẽ rule này để tránh lỗi sập cây đệ quy.
*   **[RULE-ADM-GNR-02] - Soft Toggle (Tắt ẩn an toàn):** Không cho phép xóa vật lý (Delete) Thể loại vì nó đã bị gắn (Foreign Key) vào hàng ngàn bài hát. Chỉ cho phép Tắt `is_active = 0`.
    *   Khi tắt, Artist sẽ không thấy để chọn khi upload.
    *   Các bài hát cũ thuộc thể loại này vẫn hoạt động bình thường, nhưng không hiện danh mục này ở trang Khám phá.
*   **[RULE-ADM-GNR-03] - Unique Slug:** Cột `slug` phải là duy nhất. Khi user gõ "Pop", tự sinh `pop`. Nếu đã có `pop`, tự động thêm hậu tố `pop-1`.

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Đọc (Read):** Đệ quy bảng `[DB-genres]`.
*   **Ghi (Write):** 
    *   Update bảng `[DB-genres]`.
    *   Nếu có Upload Icon, ghi file lên MinIO.
    *   *Side Effect:* Xóa Cache Redis `explore_genres` nếu Frontend có Cache danh sách này ở màn hình User.
