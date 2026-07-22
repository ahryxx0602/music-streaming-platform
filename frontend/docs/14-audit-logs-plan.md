# Kế hoạch Triển khai Frontend: [SCR-ADM-08] Nhật ký Hệ thống (Audit Logs)

## 1. Mục tiêu
Xây dựng giao diện hiển thị danh sách lịch sử thao tác của các nhân viên/quản trị viên (Audit Logs). Kết hợp dữ liệu này vào màn hình Dashboard (bảng Hoạt động gần đây). Tuân thủ chặt chẽ Design System 3-Layer Token hiện tại của Admin.

## 2. Giao diện chính (Audit Logs View)
- **Đường dẫn (Route)**: `/admin/audit-logs`
- **File**: `frontend/src/views/admin/audit/AuditLogsView.vue`
- **Menu Sidebar**: Cần bổ sung một mục "Nhật ký Hệ thống" (Icon History/List) trong `AdminSidebar.vue`.

### Bố cục Màn hình:
1. **Header & Filters**:
   - Ô tìm kiếm (Search theo tên User, IP).
   - Dropdown lọc theo Hành động (Tạo mới, Cập nhật, Xóa, Xét duyệt...).
   - Dropdown lọc theo Module (Nhạc, Người dùng, Thể loại...).
   - Date picker để lọc thời gian.
2. **Data Table (`AdminDataTable.vue`)**:
   - Cột 1: Người thực hiện (Avatar + Tên + Email).
   - Cột 2: Hành động (Hiển thị StatusBadge với các màu Semantic tương ứng: success cho tạo, warning cho cập nhật, danger cho xoá).
   - Cột 3: Đối tượng bị tác động (Module name + ID).
   - Cột 4: Thời gian, IP Address.
   - Cột 5: Nút "Chi tiết" để xem sự thay đổi (Old/New values).

## 3. Component Xem Chi Tiết Sự Thay Đổi (Diff Modal)
- **File**: `frontend/src/components/admin/features/audit/AuditDiffModal.vue`
- Khi người dùng click "Chi tiết" ở những action `updated`, mở Modal hiển thị một bảng so sánh dạng Before/After (Sử dụng JSON formatter nếu cần) để theo dõi xem dữ liệu cụ thể nào đã bị thay đổi (VD: Sửa tên Genre từ "Pop" thành "Pop/Dance").

## 4. Tích hợp Dashboard (Dashboard Integration)
- Cập nhật file `AdminDashboardView.vue` vừa tạo.
- Ở khung "Hoạt động gần đây" (Recent Activity), thay thế nội dung tĩnh hiện tại bằng một component `RecentActivitiesList.vue`.
- Gọi API `GET /api/v1/admin/dashboard/recent-activities`.
- Render dưới dạng Timeline (hoặc list gọn) các sự kiện mới nhất.

## 5. Quản lý Trạng thái (Store)
- File: `frontend/src/stores/auditLogStore.ts` (Sử dụng Pinia).
- Cung cấp các hàm: `fetchLogs(params)`, `fetchRecentActivities()`.

## 6. Các bước thực hiện
1. **Store Setup**: Khởi tạo `auditLogStore.ts` và tích hợp các endpoint API.
2. **Dashboard Tweak**: Gắn dữ liệu thật vào ô "Hoạt động gần đây" của Dashboard.
3. **AuditLogsView**: Tạo file View, gắn Router và tích hợp vào `AdminSidebar.vue`.
4. **Table & Filters**: Xây dựng UI bộ lọc và đưa `AdminDataTable` vào hiển thị logs.
5. **Diff Modal**: Dựng BaseModal chuyên hiển thị sự khác biệt dữ liệu (`old_values` vs `new_values`).
6. **Polish**: Check lại Dark Mode và các Class Semantic CSS (`bg-theme-surface`, `text-theme-text`...).
