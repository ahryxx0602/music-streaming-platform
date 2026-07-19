# API Reference: Genre Management (Content Management)

Đặc tả các Endpoints CRUD dành cho Admin để quản lý dữ liệu Thể loại nhạc (Master Data) dạng cây (Tree).

## 1. Lấy danh sách cây Thể loại
- **Mã API:** `[API-370]`
- **Method:** `GET`
- **Endpoint:** `/api/v1/admin/genres`
- **Middleware:** `auth:sanctum`, `role:admin|content-manager`

### Response Thành công (200 OK)
```json
{
  "success": true,
  "message": "Lấy danh sách thể loại thành công.",
  "data": [
    {
      "id": 1,
      "name": "Nhạc Trẻ",
      "slug": "nhac-tre",
      "parent_id": null,
      "is_active": true,
      "children": [
        {
          "id": 2,
          "name": "Pop",
          "slug": "pop",
          "parent_id": 1,
          "is_active": true,
          "children": []
        }
      ]
    }
  ]
}
```

---

## 2. Tạo Thể loại Mới
- **Mã API:** `[API-371]`
- **Method:** `POST`
- **Endpoint:** `/api/v1/admin/genres`
- **Middleware:** `auth:sanctum`, `role:admin|content-manager`

### Request Body (JSON / FormData)
```json
{
  "name": "Nhạc Trữ Tình",
  "parent_id": null,
  "cover_image": null
}
```
*(Ghi chú: `slug` sẽ được Backend tự động sinh ra từ `name`)*

### Response Thành công (201 Created)
```json
{
  "success": true,
  "message": "Tạo thể loại thành công.",
  "data": {
    "genre": {
      "id": 3,
      "name": "Nhạc Trữ Tình",
      "slug": "nhac-tru-tinh",
      "parent_id": null,
      "is_active": true
    }
  }
}
```

---

## 3. Cập nhật Thể loại
- **Mã API:** `[API-372]`
- **Method:** `PUT` (hoặc `POST` kèm `_method=PUT` nếu có file upload)
- **Endpoint:** `/api/v1/admin/genres/{id}`
- **Middleware:** `auth:sanctum`, `role:admin|content-manager`

### Yêu cầu đặc biệt (Business Rule)
- `[RULE-ADM-GNR-01]`: Trả về `422 Unprocessable Entity` nếu `parent_id` được truyền lên bằng chính `{id}` hoặc bất kỳ ID nào thuộc danh sách các nút con của nó (chống vòng lặp).

### Request Body (JSON)
```json
{
  "name": "Bolero",
  "parent_id": 3,
  "is_active": true
}
```

### Response Thành công (200 OK)
```json
{
  "success": true,
  "message": "Cập nhật thể loại thành công.",
  "data": {
    "genre": {
      "id": 4,
      "name": "Bolero",
      "slug": "bolero",
      "parent_id": 3,
      "is_active": true
    }
  }
}
```
