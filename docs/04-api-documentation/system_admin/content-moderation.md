# Nhóm Tính năng: Kiểm duyệt Nội dung (Content Moderation & Audit)

**Mô tả:** Là lá chắn bảo vệ hệ thống khỏi nội dung độc hại. Moderator sử dụng cụm API này để nghe thử nhạc Pending, ra quyết định Duyệt/Từ chối. Mọi hành động của Admin đều bị ghi lại ở Audit Logs.

**Liên kết giao diện:** 
- [SCR-ADM-04 Duyệt bài hát](../../screens/Admin/SCR-ADM-04-moderation.md)
- [SCR-ADM-08 Nhật ký hệ thống](../../screens/Admin/SCR-ADM-08-audit-logs.md)

---

## 💻 Chi tiết các APIs - Duyệt Nhạc (Song Moderation)

### 1. [API-350] Danh sách Bài hát chờ duyệt
**Tính năng:** Lấy các bài hát có `status = Pending`. Sắp xếp cũ nhất lên đầu (First in, First out).
* **Endpoint:** `GET /api/v1/admin/moderation/songs`
* **Auth:** Bearer Token (Moderator)

### 2. [API-351] Chi tiết bài hát cần duyệt
**Tính năng:** Lấy thông tin metadata và đường dẫn nhạc gốc để Admin nghe thử trước khi duyệt.
* **Endpoint:** `GET /api/v1/admin/moderation/songs/{id}`
* **Auth:** Bearer Token (Moderator)

### 3. [API-352] Đổi Trạng thái Phê duyệt Bài hát
**Tính năng:** Phê duyệt hoặc từ chối bài hát từ Artist.
* **Endpoint:** `PATCH /api/v1/admin/moderation/songs/{id}`
* **Auth:** Bearer Token (Moderator)

**Request Body:**
| Field | Type | Rules | Description |
|---|---|---|---|
| status | string | required, in:Approved,Rejected | Trạng thái mới |
| rejected_reason | string | required_if:status,Rejected, max:500 | Bắt buộc có nếu từ chối |

---

## 💻 Chi tiết các APIs - Kiểm soát Bình luận & Audit

### 5. [API-420] Xóa Bình luận vi phạm
**Tính năng:** Admin có quyền xóa vĩnh viễn/xóa mềm bất kỳ bình luận độc hại nào của Listener.
* **Endpoint:** `DELETE /api/v1/admin/comments/{id}`
* **Auth:** Bearer Token (Moderator)

### 6. [API-410] Xem Nhật ký Hệ thống (Audit Logs)
**Tính năng:** Xem lịch sử "Ai đã làm gì, lúc nào". (VD: "Admin John đã Approve bài hát A").
* **Endpoint:** `GET /api/v1/admin/audit-logs`
* **Auth:** Bearer Token (Super Admin)
