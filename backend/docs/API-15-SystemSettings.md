# Kế hoạch Triển khai Backend: [SCR-ADM-12] Cài đặt Hệ thống (System Settings)

## 1. Hiện trạng Database
- Đã có sẵn Migration `create_settings_table` và Model `Setting.php`.
- Cấu trúc: `key` (unique), `value` (text), `description` (nullable).
- Phương pháp tiếp cận: Key-Value store.

## 2. API Endpoints cần xây dựng
Module: `Administration`

### 2.1. Lấy toàn bộ cấu hình
- **Endpoint**: `GET /api/v1/admin/settings`
- **Logic**:
  - Truy vấn toàn bộ bảng `settings`.
  - Format response trả về dạng Object Key-Value cho dễ dùng ở FE:
    ```json
    {
      "site_name": "MusicStreaming",
      "artist_revenue_share": "70",
      "payout_threshold": "50",
      "maintenance_mode": "false"
    }
    ```
  - **Performance**: Phải bọc vào `Cache::rememberForever('system_settings')` để tối ưu truy vấn vì settings sẽ được gọi rất thường xuyên.

### 2.2. Cập nhật cấu hình (Bulk Update)
- **Endpoint**: `PUT /api/v1/admin/settings`
- **Request Body**:
  ```json
  {
    "settings": {
      "site_name": "New Name",
      "artist_revenue_share": "75"
    }
  }
  ```
- **Logic**:
  - Nhận array `settings`. Lặp qua từng key để `updateOrCreate`.
  - Phải xoá cache: `Cache::forget('system_settings')`.
  - Bắn một record vào bảng `audit_logs` thông qua `AuditLog::create(...)` để lưu vết người dùng vừa đổi setting hệ thống.

## 3. Database Seeder
- Tạo `SettingDatabaseSeeder` chứa các default keys:
  - `site_name`: Tên nền tảng (Mặc định: "Harmonia").
  - `support_email`: Email CSKH.
  - `artist_revenue_share`: Phần trăm doanh thu chia cho nghệ sĩ (Mặc định: 70).
  - `payout_threshold`: Ngưỡng rút tiền tối thiểu (Mặc định: 50 USD).
  - `maintenance_mode`: Chế độ bảo trì (Mặc định: false).

## 4. Nhiệm vụ của Backend Agent
1. Cập nhật Model `Setting.php` nếu cần.
2. Xây dựng `AdminSettingController`.
3. Khai báo routes trong `api.php`.
4. Tạo `SettingDatabaseSeeder` và chạy seed để có dữ liệu khởi tạo.
5. Code cẩn thận phần Caching và Audit Logging thủ công khi cập nhật hàng loạt.
