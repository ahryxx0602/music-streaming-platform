# Kế hoạch Triển khai Backend: [SCR-ADM-08] Nhật ký Hệ thống (Audit Logs)

## 1. Mục tiêu
Theo dõi và ghi nhận toàn bộ các thao tác (Tạo, Sửa, Xóa, Cấp quyền, Kiểm duyệt...) của Admin và Staff trên hệ thống. Cung cấp API để Frontend (Bảng theo dõi & Dashboard) truy xuất dữ liệu một cách hiệu quả, kèm theo bộ lọc (filters) phong phú.

## 2. Kiến trúc & Database
Sẽ tạo bảng `audit_logs` để lưu trữ dữ liệu dưới dạng Polymorphic Relation nhằm dễ dàng map với bất kỳ đối tượng nào bị tác động (User, Song, Genre, Playlist, Banner).

### Cấu trúc bảng `audit_logs`:
- `id`: PK
- `user_id`: FK (Người thực hiện hành động)
- `action`: String (Ví dụ: `created`, `updated`, `deleted`, `approved`, `rejected`, `login`)
- `auditable_type`: String (Tên Model bị tác động, vd: `App\Models\Song`)
- `auditable_id`: BigInt (ID của Model bị tác động)
- `old_values`: JSON (Lưu trạng thái dữ liệu trước khi đổi)
- `new_values`: JSON (Lưu trạng thái dữ liệu sau khi đổi)
- `ip_address`: String (IP của người thực hiện)
- `user_agent`: String (Thiết bị/Trình duyệt)
- `created_at`: Timestamp (Thời gian thực hiện)

## 3. Khâu thu thập dữ liệu (Logging Mechanism)
Sử dụng **Eloquent Observers** (hoặc boot trait) gắn vào các Models quan trọng (User, Song, Genre, v.v.). Khi Model có sự kiện `created`, `updated`, `deleted`, Observer sẽ tự động push một record vào bảng `audit_logs`.
*Ghi chú: Nếu hệ thống đã cài package như `spatie/laravel-activitylog`, chúng ta sẽ tận dụng nó thay vì code chay từ đầu để tiết kiệm thời gian và đảm bảo chuẩn xác.*

## 4. API Endpoints cần xây dựng (Module: Administration)

### 4.1. Lấy danh sách Audit Logs
- **Endpoint**: `GET /api/v1/admin/audit-logs`
- **Quyền**: Admin (hoặc staff có quyền `view_audit_logs`)
- **Query Parameters**:
  - `page`, `per_page`: Phân trang.
  - `action`: Lọc theo loại hành động.
  - `user_id`: Lọc theo người thực hiện.
  - `module`: Lọc theo bảng dữ liệu (`auditable_type`).
  - `date_from`, `date_to`: Lọc theo khoảng thời gian.
- **Response**: Trả về dữ liệu phân trang, kèm theo relationship `user` (người thực hiện).

### 4.2. Lấy hoạt động gần đây (Dành cho Dashboard)
- **Endpoint**: `GET /api/v1/admin/dashboard/recent-activities`
- **Response**: Trả về 5-10 log mới nhất (được format tinh gọn để hiển thị trên Dashboard).

## 5. Các bước thực hiện
1. **Migrations & Models**: Tạo migration cho bảng `audit_logs` (hoặc cài package `spatie/laravel-activitylog`), tạo Model `AuditLog`.
2. **Trait/Observer**: Viết cơ chế hook tự động ghi log vào các Model cần thiết. Thử nghiệm ghi log khi một Admin sửa tên Genre hoặc duyệt Bài hát.
3. **Controller & Routes**: Xây dựng `AdminAuditLogController` và đăng ký routes trong `Modules/Administration/routes/api.php`.
4. **Unit/Feature Test**: Bổ sung `AdminAuditLogControllerTest.php` để đảm bảo API trả về đúng format và hoạt động của bộ lọc.
