# Kiến trúc Backend API: [SCR-ADM-08] Nhật ký Hệ thống (Audit Logs)

## 1. Yêu cầu & Luồng xử lý
Nhật ký hệ thống (Audit Logs) là "hộp đen" của ứng dụng, lưu lại các dấu vết thay đổi dữ liệu và truy cập.
Mục tiêu là tự động bắt (Hook) vào quá trình sửa đổi dữ liệu (Eloquent Events) mà không cần phải viết code ghi log rải rác ở khắp các Controllers.

## 2. Thiết kế Cơ sở dữ liệu (Database)
Tạo bảng `audit_logs` (thông qua Migration):
- `id` (BigInt, PK)
- `user_id` (BigInt, nullable, Foreign Key) - Người thực hiện hành động.
- `action` (String) - Ví dụ: `created`, `updated`, `deleted`, `login`, `failed_login`.
- `auditable_type` (String) - Tên Model (VD: `App\Models\User`, `Modules\Music\Models\Song`).
- `auditable_id` (BigInt) - ID của bản ghi bị tác động.
- `old_values` (JSON, nullable) - Dữ liệu trước khi sửa.
- `new_values` (JSON, nullable) - Dữ liệu sau khi sửa hoặc mới tạo.
- `ip_address` (String, nullable).
- `user_agent` (String, nullable).
- `created_at` (Timestamp).

## 3. Kiến trúc Ghi Log Tự Động (Auto-Logging)
Để tránh "code smell" (viết `AuditLog::create()` ở mọi Controller), chúng ta sử dụng kiến trúc **Trait + Observer**.
- Tạo một Trait tên là `Auditable` (nằm ở `App\Traits\Auditable.php`).
- Bất kỳ Model nào `use Auditable;` sẽ tự động kích hoạt boot method, lắng nghe các sự kiện `created`, `updated`, `deleted` của Eloquent và dispatch ra log tương ứng.
- Đối với các sự kiện đăng nhập, sử dụng **Laravel Event Listeners** (lắng nghe event `Illuminate\Auth\Events\Login` và `Failed`).

## 4. Thiết kế API Endpoints

### API 1: Lấy danh sách Logs (Có Phân trang & Lọc)
- **Endpoint:** `GET /api/v1/admin/audit-logs`
- **Controller:** `Modules\Admin\Http\Controllers\AdminAuditLogController`
- **Middleware:** `auth:sanctum`, `role:Admin|SuperAdmin`
- **Query Params:**
  - `search` (Tìm theo IP, ID).
  - `action` (VD: `updated`).
  - `model` (VD: `Song`).
  - `date_from`, `date_to`.
- **Response:** Paginated JsonResponse.

### API 2: Chi tiết Log
- **Endpoint:** `GET /api/v1/admin/audit-logs/{id}`
- Trả về toàn bộ JSON chi tiết để Frontend so sánh dữ liệu Before/After.

## 5. Công việc cho Backend Agent
1. **Migration & Model:** Tạo bảng `audit_logs` và Model `AuditLog`.
2. **Core Logic:** Code file `App\Traits\Auditable.php` để bắt sự kiện tự động lưu log.
3. **Event Listener:** Code listener bắt sự kiện Login/FailedLogin để lưu IP.
4. **API:** Code `AdminAuditLogController` lấy dữ liệu ra cho Frontend.
5. **Testing:** Viết Feature Test tạo thử 1 bài hát, kiểm tra xem bảng `audit_logs` có sinh ra dòng log `created` không.
