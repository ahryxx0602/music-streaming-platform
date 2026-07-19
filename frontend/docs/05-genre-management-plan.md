# Kế hoạch Triển khai Frontend - Genre Management

Tài liệu này đặc tả chi tiết kế hoạch triển khai giao diện cho màn hình **[SCR-ADM-07] Quản lý Thể loại**.
Nó liên kết trực tiếp với các luồng API được định nghĩa tại `API-06-GenreManagement.md`.

## 1. Mục tiêu & Phạm vi (Objectives)
*   Phát triển giao diện Admin cho chức năng Quản lý Thể loại nhạc (Master Data).
*   Tích hợp UI dạng Cây (Tree Table) và Cửa sổ trượt ngang (Drawer) để đảm bảo trải nghiệm thống nhất với quản lý Users.
*   Xử lý logic tự tạo Slug và chống lặp Danh mục Cha ở phía Client trước khi gửi API.

## 2. Khởi tạo Cấu trúc & Routing
*   **Menu:** Thêm mục "Thể loại" vào sidebar (`AdminSidebar.vue`) dưới nhóm Music.
*   **Route:** Khởi tạo route `/admin/genres` trong `router/index.ts`.
*   **Views & Components:**
    *   `src/views/admin/genres/GenresView.vue` (Màn hình chính chứa Toolbar và Bảng).
    *   `src/views/admin/genres/components/GenreTreeTable.vue` (Bảng hiển thị đệ quy Cha - Con, thụt lề bằng CSS).
    *   `src/views/admin/genres/components/GenreDrawer.vue` (Form Thêm/Sửa trượt từ phải sang).

## 3. State Management (Pinia)
*   **Store:** Tạo `useGenreStore.ts`.
*   **State:** `genresTree` (mảng đệ quy), `isLoading`.
*   **Actions:** 
    *   `fetchGenres()` (gọi API lấy list).
    *   `saveGenre()` (tạo mới hoặc cập nhật).

## 4. Giao diện Chi tiết (UI/UX)
*   **Bảng dữ liệu (GenreTreeTable):**
    *   Cột Tên Thể loại sẽ có icon Expand/Collapse (Mở/Đóng) nếu có danh mục con.
    *   Nút "Toggle Switch" cho tính năng Ẩn/Hiện Thể loại (`is_active`).
    *   Nút hành động: Sửa, Thêm danh mục con (Add Child).
*   **Form Thêm/Sửa (GenreDrawer):**
    *   **Auto-slug:** Khi nhập `name`, watch và debounce tự động generate chuỗi sang dạng kebab-case cho ô `slug`.
    *   **Dropdown Danh mục Cha:** Disable chính nó và các con của nó khi đang ở chế độ Sửa, hiển thị tên theo dạng thụt lề (`-- Nhạc Trẻ`).
    *   Tích hợp UI Upload Icon đơn giản cho `cover_image`.

## 5. Đa ngôn ngữ (i18n)
*   Đảm bảo toàn bộ label, placeholder (VD: "Tên thể loại", "Danh mục cha", "Đường dẫn") đều sử dụng `$t('admin.genres.xxx')`.
