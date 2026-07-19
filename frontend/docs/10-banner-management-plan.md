# Kế hoạch Triển khai Frontend - Quản lý Banner (Admin)

Tài liệu này đặc tả kiến trúc giao diện cho module Quản lý Banner trang chủ (`SCR-ADM-05`).

---

## 1. Cấu trúc Component Dự kiến
Tạo folder mới: `src/views/admin/banners/`
*   `src/views/admin/banners/BannerView.vue`: Màn hình chứa Bảng danh sách Banner.
*   `src/components/admin/features/banners/BannerList.vue`: Thành phần hiển thị danh sách dạng List/Card, **hỗ trợ Kéo-thả (Drag & Drop)**.
*   `src/components/admin/features/banners/BannerFormModal.vue`: Popup nhập liệu tạo mới / sửa Banner. Phải có khu vực kéo thả để Upload ảnh bìa.

---

## 2. Store Management (`bannerStore.ts`)
Tạo store `src/stores/bannerStore.ts`:
*   **State:** 
    *   `banners`: Mảng chứa các Banner của hệ thống.
*   **Actions:**
    *   `fetchBanners()`: Gọi `GET /api/v1/admin/banners`. (Backend đã tự sort theo order).
    *   `saveBanner(id, formData)`: Gửi Request `POST` (Nếu có id thì đính kèm `_method=PUT` vào FormData).
    *   `reorderBanners(orderedIds)`: Gửi Request `PUT /api/v1/admin/banners/reorder` với payload `banner_ids`.
    *   `toggleStatus(id, isActive)`: Bật tắt nhanh, gọi hàm `saveBanner` đính kèm data `is_active` mà không cần upload ảnh.
    *   `deleteBanner(id)`: Gọi `DELETE /api/v1/admin/banners/{id}`.

---

## 3. UI/UX Rules & Ràng buộc
1. **Kéo thả native (Draggable List):** Giống hệt module Playlist, Admin có thể giữ icon Grip dọc (⋮⋮) để kéo dòng Banner lên hoặc xuống. Khi Drop xong, Store sẽ map mảng `banners` thành mảng `banner_ids` và bắn API `reorderBanners` ngầm ngay lập tức.
2. **Kích thước ảnh:** Gợi ý user tải ảnh kích thước (1920x600). Preview ảnh ra màn hình Form trước khi submit.
3. **Toggle Switch (Bật/Tắt):** Mỗi Banner ở ngoài danh sách có 1 cái công tắc bật tắt. Cứ bấm là gọi API update `is_active`. Trạng thái bật thì hiện Label "Đang hiển thị", tắt thì "Đang ẩn".

---

## 4. Routing
Cần bổ sung Route `/admin/banners` vào Router (`router/index.ts`) và gắn Link trên thanh Sidebar Menu (Dưới nhóm "Quản lý Giao diện").
