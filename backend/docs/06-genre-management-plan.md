# Kế hoạch Triển khai Backend API - Genre Management

Tài liệu này đặc tả chi tiết kế hoạch triển khai API cho màn hình **[SCR-ADM-07] Quản lý Thể loại**.
Chi tiết các Endpoints vui lòng xem tại tài liệu tham chiếu chéo: **[`API-06-GenreManagement.md`](./API-06-GenreManagement.md)**.

## 1. Mục tiêu & Phạm vi (Objectives)
*   Xây dựng hệ thống API phục vụ cho màn hình Admin Quản lý Thể loại (Master Data).
*   Đảm bảo cấu trúc dữ liệu Cha - Con (Parent - Child) thông qua cơ chế Đệ quy (Nested Set Model hoặc Parent_ID).
*   Chống vòng lặp vô hạn (Infinite Loop) khi gán Danh mục Cha.

## 2. Thiết kế Database (Migration)
*   **Table:** `genres`
*   **Columns:**
    *   `id` (PK, uuid/bigint)
    *   `parent_id` (FK trỏ về `genres.id`, nullable)
    *   `name` (string, required, đa ngôn ngữ hoặc lưu JSON nếu cần)
    *   `slug` (string, unique, auto-generated)
    *   `cover_image` (string, nullable)
    *   `is_active` (boolean, default: true) - Phục vụ Soft Toggle thay vì xóa cứng.
    *   `timestamps`

## 3. Cấu trúc API (Endpoints)
*   **[API-370] Lấy danh sách cây Thể loại:**
    *   `GET /api/v1/admin/genres`
    *   **Response:** Trả về cấu trúc cây (tree) đệ quy (Mỗi genre có mảng `children`).
*   **[API-371] Tạo mới Thể loại:**
    *   `POST /api/v1/admin/genres`
    *   **Payload:** `name`, `parent_id` (nullable), `cover_image` (file/url).
    *   **Logic:** Tự động sinh `slug` từ `name`. Đảm bảo `slug` là duy nhất (nếu trùng tự động nối số `-1`, `-2`).
*   **[API-372] Cập nhật Thể loại:**
    *   `PUT /api/v1/admin/genres/{id}`
    *   **Payload:** `name`, `parent_id`, `is_active`, `cover_image`.
    *   **Validate:** Khóa không cho phép cập nhật `parent_id` bằng chính `{id}` hoặc bất kỳ ID nào thuộc cây con (descendants) của `{id}` để **chống lặp vô hạn**.

## 4. Xử lý File & Cache
*   **Upload:** Nếu có upload `cover_image`, lưu file qua disk S3 (hoặc local).
*   **Cache:** Xóa/Clear Redis cache `explore_genres` (nếu có) khi thêm/sửa trạng thái `is_active` để Client được cập nhật ngay lập tức.
