
> [!IMPORTANT]
> **I18N REQUIREMENT:** Tất cả các đoạn Text, Label, Placeholder, Message hiển thị trong tài liệu Screen Specs này khi triển khai vào code thực tế đều **KHÔNG ĐƯỢC HARDCODE**. Bắt buộc phải sử dụng key đa ngôn ngữ qua hàm `$t()` của `vue-i18n`.

---
Screen ID: SCR-ADM-12
Tên màn hình: Cấu hình Hệ thống (System Settings)
Role: Admin
Trạng thái: Approved
Mô tả chung: Giao diện quản lý các tham số động của hệ thống (Payout rate, Max upload size, Storage Quota...).
---

# 1. UI Layout (Bố cục giao diện)

- **Header**: Tiêu đề "System Settings", Nút "Lưu thay đổi" (disabled nếu chưa có thay đổi).
- **Body**: Chia làm các Tabs (thẻ) theo nhóm cấu hình:
  - **Streaming & Royalty**: Cấu hình trả tiền bản quyền.
  - **Upload & Media**: Cấu hình giới hạn dung lượng file.
  - **Notification**: Cấu hình SMTP & Webhooks (chỉ xem, không sửa trực tiếp).
  - **Security**: Cài đặt JWT TTL, Session limits.

## 1.1 Tabs Detail

### Tab: Streaming & Royalty
- **Payout Rate (USD)**: Số tiền trả cho mỗi lượt stream hợp lệ. Input number (vd: 0.003).
- **Minimum Payout**: Ngưỡng rút tiền tối thiểu cho Artist (vd: 50 USD).

### Tab: Upload & Media
- **Max Audio Size (MB)**: Giới hạn file nhạc (Input number, mặc định 20).
- **Max Cover Size (MB)**: Giới hạn ảnh bìa (Input number, mặc định 2).
- **Allowed Formats**: Checkbox (MP3, WAV, FLAC, OGG).

---

# 2. Logic & Validation

- Chỉ `Super Admin` (RBAC role) mới có quyền truy cập màn hình này.
- Mọi thay đổi đều được ghi log chi tiết vào `[DB-audit_logs]` (VD: "Admin A changed Payout Rate from 0.003 to 0.004").

---

# 3. API Mapping

| API ID | Method | Endpoint | Mô tả |
| :--- | :--- | :--- | :--- |
| `[API-430]` | GET | `/api/v1/admin/settings` | Kéo toàn bộ danh sách cấu hình. |
| `[API-431]` | PUT | `/api/v1/admin/settings/{key}` | Cập nhật giá trị cấu hình đơn lẻ. |

---

# 4. Error Handling

- Trả về `403 Forbidden` nếu Admin không có quyền (Permission: `manage_settings`).
- Trả về `422 Unprocessable Entity` nếu giá trị nhập vào sai định dạng (ví dụ Payout Rate < 0).
