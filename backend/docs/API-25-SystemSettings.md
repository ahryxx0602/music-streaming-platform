# Kiến trúc Backend API: [SCR-ADM-12] Cài đặt Hệ thống (System Settings)

## 1. Yêu cầu & Thiết kế Database
Thay vì lưu cài đặt hệ thống vào file `.env` (vì `.env` yêu cầu restart server và không thể cấp quyền cho Admin sửa), chúng ta sẽ lưu cài đặt vào Database theo kiến trúc **Key-Value Store**.

Tạo bảng `system_settings` (Migration):
- `id` (Primary Key)
- `key` (String, Unique) - Ví dụ: `site_name`, `artist_revenue_share`, `maintenance_mode`.
- `value` (Text/JSON, nullable) - Giá trị của cài đặt.
- `type` (String) - Kiểu dữ liệu để Frontend biết cách render (VD: `string`, `boolean`, `integer`, `float`, `file`).
- `group` (String) - Nhóm cài đặt (VD: `general`, `finance`, `system`).
- `created_at`, `updated_at`

## 2. Cache Cơ chế Đọc (Read Optimization)
Vì các cài đặt này được gọi liên tục (ở Middleware hoặc khi tính toán tiền), việc query bảng `system_settings` mỗi lần là một thảm họa hiệu năng.
- **Giải pháp**: Phải sử dụng `Cache::rememberForever('system_settings', ...)`.
- Khi có bất kỳ một Update nào (API PUT), hệ thống tự động gọi `Cache::forget('system_settings')` để xóa bộ nhớ đệm, ép truy vấn lại vào lần tới.

## 3. Thiết kế API Endpoints

### API 1: Lấy danh sách Cài đặt
- **Endpoint:** `GET /api/v1/admin/settings`
- **Controller:** `Modules\Admin\Http\Controllers\SystemSettingController`
- **Middleware:** `auth:sanctum`, `role:SuperAdmin` (Chỉ SuperAdmin mới được truy cập cấu hình hệ thống).
- **Xử lý:** Lấy dữ liệu từ Cache (nếu có) hoặc query DB và đưa vào Cache. Chuyển đổi format trả về dạng Key-Value hoặc mảng phân nhóm (Group).

### API 2: Cập nhật Cài đặt (Bulk Update)
- **Endpoint:** `PUT /api/v1/admin/settings`
- **Request Format:**
  ```json
  {
    "settings": {
      "site_name": "Antigravity Music",
      "artist_revenue_share": 75,
      "maintenance_mode": false
    }
  }
  ```
- **Xử lý Logic:**
  - Vòng lặp quét mảng `settings`.
  - Sử dụng `SystemSetting::where('key', $key)->update(['value' => $value])`.
  - Xóa Cache hiện tại.
  - LƯU Ý: Vì bảng `SystemSetting` là một Model cực kỳ quan trọng, hãy chắc chắn rằng nó có gắn `use Auditable;` (Đã làm ở Task SCR-ADM-08) để mọi thao tác sửa cấu hình của SuperAdmin đều bị ghi Log lại!

## 4. Công việc cho Backend Agent
1. **Migration & Seeder:** Tạo bảng `system_settings` và viết `SystemSettingSeeder` để insert sẵn các keys mặc định (VD: site_name = 'Music Platform', artist_revenue_share = 70).
2. **Controller:** Code `SystemSettingController` với cơ chế Cache siêu tốc.
3. **Helper/Service (Tùy chọn):** Tạo một class Helper như `Setting::get('site_name')` để các modules khác (như Billing) có thể dễ dàng gọi lấy cấu hình.
4. **Test Coverage:** Viết Feature Test kiểm tra việc Update có thực sự xóa Cache hay không.
