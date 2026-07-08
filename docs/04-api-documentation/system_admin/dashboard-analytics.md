# Nhóm Tính năng: Admin Dashboard & Analytics

**Mô tả:** Tài liệu định nghĩa các API trả về dữ liệu thống kê toàn cục của hệ thống cho Admin. Các số liệu này cực lớn nên sẽ được xử lý ngầm và lưu vào Redis Cache (Cache 15-30 phút).

**Liên kết giao diện:** 
- [SCR-ADM-09 Dashboard & Analytics](../../screens/Admin/SCR-ADM-09-dashboard.md)

---

## 💻 Chi tiết các APIs

### 1. [API-310] Thống kê Tổng quan (Widgets)
**Tính năng:** Trả về các con số lớn hiển thị ở đầu trang (Tổng Users, Tổng Streams, Revenue).
* **Endpoint:** `GET /api/v1/admin/dashboard`
* **Auth:** Bearer Token (Admin/Moderator)

### 2. [API-311] Biểu đồ Streams theo ngày
**Tính năng:** Dữ liệu mảng để vẽ biểu đồ Line Chart tổng lượt stream trong 30 ngày qua.
* **Endpoint:** `GET /api/v1/admin/analytics/streams`
* **Auth:** Bearer Token (Admin)

### 3. [API-312] Top Bài hát
**Tính năng:** Bảng xếp hạng top 10 bài hát có lượt nghe cao nhất toàn hệ thống.
* **Endpoint:** `GET /api/v1/admin/analytics/top-songs`
* **Auth:** Bearer Token (Admin)

### 4. [API-313] Biểu đồ Tăng trưởng User
**Tính năng:** Dữ liệu vẽ biểu đồ số lượng đăng ký mới theo tháng.
* **Endpoint:** `GET /api/v1/admin/analytics/user-growth`
* **Auth:** Bearer Token (Admin)

### 5. [API-314] Phân tích Chi tiết một Nghệ sĩ
**Tính năng:** Xem trộm Dashboard riêng của một Nghệ sĩ bất kỳ.
* **Endpoint:** `GET /api/v1/admin/analytics/artists/{artist_id}`
* **Auth:** Bearer Token (Admin)
