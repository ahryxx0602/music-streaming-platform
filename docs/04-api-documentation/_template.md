# Nhóm Tính năng: [Tên Nhóm Tính Năng]

**Mô tả:** [Mô tả ngắn gọn về nhóm tính năng này, phục vụ cho luồng công việc nào, do Role nào sử dụng]

**Liên kết giao diện:** 
- [SCR-XXX-01 Tên giao diện](../../screens/Thư_Mục/SCR-XXX-01-ten-file.md)

---

## 🔄 Tóm tắt luồng gọi API (Workflow)
[Mô tả sơ đồ luồng dữ liệu (Data flow) hoặc thứ tự gọi các API để hoàn thành tính năng]
1. Bước 1: Gọi `[API-XXX]` để...
2. Bước 2: Gọi `[API-YYY]` để...

---

## 💻 Chi tiết các APIs

### 1. [API-XXX] Tên chức năng của API
**Tính năng:** [Mô tả chức năng cụ thể của API này, lưu ý logic nghiệp vụ nếu có]
* **Endpoint:** `METHOD /api/v1/...`
* **Auth:** [Không yêu cầu / Cần CSRF Token / Bearer Token (Tên Role)]
* **Content-Type:** [Mặc định là application/json, nếu upload file thì ghi multipart/form-data]

**Query Params:** *(Chỉ dùng cho GET / DELETE, xóa phần này nếu không có)*
| Field | Type | Rules | Description |
|---|---|---|---|
| page | integer | nullable, min:1 | Trang hiện tại |

**Request Body:** *(Dùng cho POST / PUT / PATCH, xóa phần này nếu không có)*
| Field | Type | Rules | Description |
|---|---|---|---|
| field_name | string | required, max:255 | Mô tả ý nghĩa của trường này |

**Success Response (200 OK / 201 Created / 204 No Content):**
```json
{
  "status": "success",
  "message": "Thông báo thành công (nếu có)",
  "data": {
    "key": "value"
  }
}
```

**Error Response (4xx / 5xx):** *(Chỉ ghi các lỗi đặc thù của API này)*
*   **403 Forbidden:** [Lý do, ví dụ: Không có quyền sửa bài hát của người khác]
*   **404 Not Found:** [Lý do, ví dụ: Bài hát không tồn tại hoặc đã bị xóa]
*   **422 Unprocessable Entity:** Lỗi Validate dữ liệu đầu vào.
```json
{
  "status": "error",
  "message": "The given data was invalid.",
  "errors": {
    "field_name": ["Lỗi chi tiết..."]
  }
}
```

---

### 2. [API-YYY] Tên chức năng tiếp theo...
*(Lặp lại cấu trúc trên cho các API tiếp theo trong luồng)*
