# [SCR-ADM-08] Nhật ký Hệ thống (Audit Logs)

> **Mô tả ngắn:** Màn hình giám sát (Read-only) dành riêng cho Giám đốc / Super Admin. Giúp truy vết toàn bộ các thao tác quan trọng (Duyệt bài, Xóa User, Phân quyền) mà nhân viên Admin đã thực hiện trên hệ thống.

## 1. Thông tin chung (Meta)
*   **Module:** System / Security
*   **Route / URL:** `/admin/audit-logs`
*   **Layout sử dụng:** `AdminLayout.vue`
*   **Quyền truy cập:** `[PER-SUPER-ADMIN]`
*   **Component con (Children):**
    *   `LogDataTable.vue` (Bảng dữ liệu logs có bộ lọc).
    *   `LogDiffModal.vue` (Popup so sánh chi tiết dữ liệu Cũ - Mới).

## 2. Thành phần giao diện (UI Elements)
*   **Khu vực Bộ lọc (Filters):**
    *   `Filter Ngày tháng`: Chọn khoảng thời gian (Từ ngày - Đến ngày).
    *   `Filter Hành động`: Dropdown chọn loại hành động (Ví dụ: `approve_song`, `ban_user`).
    *   `Filter Admin`: Dropdown chọn tên nhân viên cần tra cứu.
*   **Khu vực Danh sách Log (Data Table):**
    *   `Cột hiển thị`: Thời gian (Timestamp), Người thực hiện (Tên + Email), Tên thao tác, Đối tượng bị tác động (Ví dụ: Song ID #123), IP truy cập.
    *   `Hành động`: Nút **"Xem chi tiết (View Data)"**.
*   **Khu vực Chi tiết (Diff Modal):**
    *   Hiển thị 2 cột `Old Values` (Dữ liệu trước khi sửa) và `New Values` (Dữ liệu sau khi sửa) dưới định dạng JSON rõ ràng (Pretty-print).

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **Lấy dữ liệu (Fetch):**
    *   `[API-410]` - `GET /api/v1/admin/audit-logs?date_from=...&action=...` (Lấy dữ liệu phân trang).
*   **Ghi dữ liệu (Mutations):**
    *   *Không có tương tác Ghi. Màn hình này là Read-only tuyệt đối.*

### State Management (Pinia)
*   **Store:** `useAuditLogStore.js`
*   **Actions:** `fetchLogs(filters)`

## 4. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-ADM-LOG-01] - Bất khả xâm phạm (Immutability):** Dữ liệu trên màn hình này là bất biến. Hoàn toàn không có nút Xóa, Sửa. Ngay cả Backend cũng không cung cấp API Delete cho bảng này. Điều này nhằm đảm bảo tính minh bạch tuyệt đối (Transparency).
*   **[RULE-ADM-LOG-02] - Xử lý Big Data:** Vì bảng Audit Logs sẽ phình to rất nhanh (hàng triệu dòng), Frontend không được phép tải toàn bộ dữ liệu. Mặc định khi vào trang, Backend chỉ trả về Logs của **7 ngày gần nhất**. Người dùng phải dùng bộ lọc để tìm dữ liệu cũ hơn.
*   **[RULE-ADM-LOG-03] - Ẩn dữ liệu nhạy cảm (Data Masking):** Nếu thao tác là Đổi mật khẩu hoặc các thông tin chứa Secret Key, Backend khi ghi log (hoặc lúc trả API `[API-410]`) phải Mask (che lại dạng `***`) dữ liệu trước khi hiển thị ra Modal JSON để tránh lộ dữ liệu người dùng.

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Đọc (Read):** Truy vấn bảng `[DB-audit_logs]` kết hợp bảng `[DB-users]`.
*   **Ghi (Write):** Màn hình này không có chức năng Ghi. Tuy nhiên, Backend được khuyến nghị sử dụng package như `spatie/laravel-activitylog` để tự động tracking mọi hành vi (Create/Update/Soft-Delete) trên toàn hệ thống đổ vào bảng `audit_logs` một cách tự động và đồng nhất mà không cần code tay ở từng Controller.
