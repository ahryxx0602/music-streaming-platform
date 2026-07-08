# Nhóm Tính năng: Bảng điều khiển & Phân tích (Dashboard & Analytics)

**Mô tả:** Đây là khu vực "đếm tiền" của Artist. Nơi họ vào xem tổng số lượt nghe (Streams), số người theo dõi (Followers), thu nhập ước tính và các Thông báo hệ thống quan trọng (như Nhạc vừa được Admin duyệt).

**Liên kết giao diện:** 
- [SCR-ART-02 Dashboard Nghệ sĩ](../../screens/Artist/SCR-ART-02-dashboard.md)

---

## 💻 Chi tiết các APIs

### 1. [API-240] Lấy Dữ liệu Thống kê Dashboard
**Tính năng:** Đổ dữ liệu tổng quát cho các biểu đồ trên Dashboard. Dữ liệu này thường được lấy từ Redis Cache (Cập nhật định kỳ) thay vì query SQL Real-time để tránh sập Database.
* **Endpoint:** `GET /api/v1/artist/analytics`
* **Auth:** Bearer Token (Artist)

**Success Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "total_streams": 2500400,
    "total_followers": 15000,
    "monthly_streams": 300000,
    "estimated_revenue": 1500.50,
    "top_performing_songs": [
      {
        "id": 105,
        "title": "Chạy Ngay Đi",
        "streams_this_month": 150000
      }
    ],
    "chart_data": {
      "labels": ["01/07", "02/07", "03/07"],
      "streams": [5000, 6200, 4800]
    }
  }
}
```

---

## 💻 Chi tiết các APIs - Thông báo (Notifications)

*(Hệ thống thông báo của Artist có cơ chế giống với Listener, chỉ khác Endpoint là `/artist/`)*

### 2. [API-250] Lấy danh sách Thông báo
**Tính năng:** Lấy luồng thông báo dành riêng cho Artist.
*Ví dụ: "Bài hát 'Cơn mưa ngang qua' của bạn đã được Admin phê duyệt." hoặc "Hệ thống báo lỗi khi xử lý âm thanh bài hát XYZ."*
* **Endpoint:** `GET /api/v1/artist/notifications`
* **Auth:** Bearer Token (Artist)

### 3. [API-251] Đánh dấu Đã đọc 1 mục
**Tính năng:** Khi Artist click vào 1 thông báo để đọc.
* **Endpoint:** `PUT /api/v1/artist/notifications/{id}/read`
* **Auth:** Bearer Token (Artist)

### 4. [API-252] Đánh dấu Đã đọc Tất cả
**Tính năng:** Bấm nút Mark all as read.
* **Endpoint:** `PUT /api/v1/artist/notifications/read-all`
* **Auth:** Bearer Token (Artist)
