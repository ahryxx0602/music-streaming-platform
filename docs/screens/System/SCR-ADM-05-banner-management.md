# [SCR-ADM-05] Quản lý Banner & Khám phá (Banner & Explore Management)

> **Mô tả ngắn:** Màn hình cấu hình giao diện Home Page cho người dùng. Cho phép Admin (hoặc Content Manager) tải lên các Banner sự kiện, chèn link điều hướng và sắp xếp thứ tự hiển thị của chúng trên trang chủ `[SCR-LST-02]`.

## 1. Thông tin chung (Meta)
*   **Module:** System / Operation (Vận hành)
*   **Route / URL:** `/admin/banners`
*   **Layout sử dụng:** `AdminLayout.vue`
*   **Quyền truy cập:** `[PER-ADMIN]` hoặc `[PER-CONTENT-MANAGER]`
*   **Component con (Children):**
    *   `BannerList.vue` (Danh sách banner hỗ trợ kéo thả - Drag & Drop để đổi vị trí).
    *   `BannerFormModal.vue` (Popup thêm/sửa Banner, upload hình ảnh).

## 2. Thành phần giao diện (UI Elements)
*   **Khu vực Danh sách Banner (Draggable List):**
    *   `Grid/Table hiển thị`: Hình ảnh thu nhỏ của Banner, Tiêu đề, Link điều hướng, Trạng thái (Bật/Tắt).
    *   `Icon Drag (Kéo thả)`: Nằm ở đầu mỗi dòng, dùng chuột giữ và kéo lên/xuống để thay đổi cột `order`.
    *   `Toggle Switch`: Nút gạt bật/tắt nhanh một Banner mà không cần xóa vật lý.
*   **Khu vực Thêm/Sửa (Form):**
    *   `Tiêu đề nội bộ`: Input text (Ví dụ: "Banner tết 2026").
    *   `Vùng chọn ảnh`: Nút Upload, chấp nhận `.jpg`, `.png`, `.webp`, yêu cầu kích thước ngang (Ví dụ: 1920x600).
    *   `Link điều hướng (Target URL)`: Input text. Khi User bấm vào Banner ở Home Page sẽ nhảy tới đâu (Ví dụ: `/album/123`).
    *   `Nút Lưu / Hủy`: Action buttons.

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **Lấy danh sách:**
    *   `[API-380]` - `GET /api/v1/admin/banners` (Lấy toàn bộ banner, sort theo cột `order`).
*   **Thao tác (Mutations):**
    *   `[API-381]` - `POST /api/v1/admin/banners` (Tạo mới, Multipart/Form-data cho ảnh).
    *   `[API-382]` - `PUT /api/v1/admin/banners/{id}` (Cập nhật thông tin / Tắt bật).
    *   `[API-383]` - `PUT /api/v1/admin/banners/reorder` (Lưu lại vị trí sau khi kéo thả. Payload là mảng ID theo thứ tự mới `[3, 1, 2]`).

### State Management (Pinia)
*   **Store:** `useBannerAdminStore.ts`
*   **Actions:** `fetchBanners()`, `createBanner()`, `updateBanner()`, `reorderBanners(newArray)`.

## 4. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-ADM-BNR-01] - Validate Kích thước Ảnh:** Frontend phải check kích thước ảnh trước khi Upload (Tối thiểu 1080px chiều ngang) để tránh vỡ hình trên màn hình máy tính của Khán giả.
*   **[RULE-ADM-BNR-02] - Xóa Cache Trang Chủ (Redis Invalidation):** Trang chủ `[SCR-LST-02]` hiện đang lấy danh sách Banner từ Redis Cache. Bất kỳ khi nào Admin thực hiện hành động Thêm/Sửa/Xóa/Kéo thả/Tắt bật thông qua các API từ `[API-381]` đến `[API-383]`, Backend **BẮT BUỘC** phải chạy lệnh `Redis::del('explore_banners')` để xóa Cache cũ. (Nếu không, Khán giả sẽ không thấy Banner mới).
*   **[RULE-ADM-BNR-03] - Giới hạn hiển thị:** Dù Admin có tạo 100 cái Banner, truy vấn từ Client (Trang chủ) chỉ lấy tối đa **5 Banner** đang ở trạng thái `is_active = 1`, sắp xếp theo `order ASC` để làm Slider.

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Ghi (Write):** Cập nhật bảng `[DB-banners]`. 
*   **Lưu trữ File:** Upload file ảnh lên MinIO. (Lưu ý: Nếu Xóa Banner, phải gọi lệnh gỡ file trên MinIO để tiết kiệm dung lượng).
*   **Side Effect:** Tác động trực tiếp vào bộ nhớ Cache Redis.
