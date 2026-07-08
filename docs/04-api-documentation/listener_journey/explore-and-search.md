# Nhóm Tính năng: Khám phá & Tìm kiếm (Explore & Search)

**Mô tả:** Tài liệu cung cấp các APIs phục vụ cho màn hình Trang chủ (Khám phá) và chức năng Tìm kiếm (từ gợi ý Live Search đến kết quả chi tiết). Đa phần các API ở đây sử dụng Cache (Redis) để chịu tải lớn từ người dùng Guest và Listener.

**Liên kết giao diện:** 
- [SCR-LST-02 Khám phá (Home / Explore)](../../screens/Listener/SCR-LST-02-home-explore.md)
- [SCR-LST-06 Tìm kiếm (Search)](../../screens/Listener/SCR-LST-06-search.md)

---

## 🔄 Tóm tắt luồng gọi API (Workflow)

1. **Khởi tạo Trang chủ:** Gọi đồng loạt các API để load layout:
   - Cần Banners -> Gọi `[API-011]`.
   - Cần Nhạc Thịnh Hành -> Gọi `[API-012]`.
   - Cần Nhạc Mới -> Gọi `[API-013]`.
   - Cần Playlists Hệ thống -> Gọi `[API-023]`.
   - (Chỉ Listener) Cần Gợi ý cá nhân hóa -> Gọi `[API-108]`.
2. **Luồng Tìm kiếm (Live Search Flow):**
   - Vừa Click vào ô Input -> Gọi `[API-170]` lấy lịch sử tìm kiếm gần đây.
   - Bắt đầu gõ phím (Debounce 300ms) -> Gọi `[API-024]` Autocomplete.
   - Bấm Enter -> Gọi `[API-015]` tìm kiếm chuyên sâu toàn bộ DB.
   - Bấm "X" xóa lịch sử -> Gọi `[API-171]` hoặc `[API-172]`.

---

## 💻 Chi tiết các APIs - Khu vực Khám phá

### 1. [API-011] Lấy danh sách Banner
**Tính năng:** Lấy các Banner đang Active để chạy slider ở đầu trang chủ.
* **Endpoint:** `GET /api/v1/guest/explore/banners`
* **Auth:** Không yêu cầu

**Success Response (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "Rap Việt Mùa 4",
      "image_url": "https://minio.../banner1.jpg",
      "target_url": "https://domain.com/playlists/99"
    }
  ]
}
```

### 2. [API-012] Top Nhạc Thịnh Hành (Trending)
**Tính năng:** Trả về Top 10 bài hát có lượt Stream cao nhất trong 7 ngày qua.
* **Endpoint:** `GET /api/v1/guest/explore/trending`
* **Auth:** Không yêu cầu

**Success Response (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 105,
      "title": "Chạy Ngay Đi",
      "artist": { "id": 1, "stage_name": "Sơn Tùng M-TP" },
      "cover_image": "https://minio.../cover.jpg",
      "play_count": 1500000
    }
  ]
}
```

### 3. [API-013] Nhạc Mới Phát Hành (New Releases)
**Tính năng:** Trả về các Album/Single vừa được Public gần đây nhất.
* **Endpoint:** `GET /api/v1/guest/explore/new-releases`
* **Auth:** Không yêu cầu

**Query Params:**
| Field | Type | Rules | Description |
|---|---|---|---|
| limit | integer | nullable, default:10 | Số lượng kết quả |

### 4. [API-023] Danh sách System Playlists
**Tính năng:** Trả về các Playlist do Admin hệ thống tạo (Nhạc Chill, Top 100).
* **Endpoint:** `GET /api/v1/guest/explore/system-playlists`
* **Auth:** Không yêu cầu

### 5. [API-108] Gợi ý Cá nhân hóa (Recommendations)
**Tính năng:** Gợi ý nhạc dựa trên lịch sử nghe của Listener (AI/Algorithm cơ bản).
* **Endpoint:** `GET /api/v1/listener/explore/recommendations`
* **Auth:** Bearer Token (Listener)

---

## 💻 Chi tiết các APIs - Khu vực Tìm kiếm

### 6. [API-014] Lấy danh sách Thể loại (Genres)
**Tính năng:** Load danh sách thể loại nhạc dạng Grid ở trang Tìm kiếm.
* **Endpoint:** `GET /api/v1/guest/genres`
* **Auth:** Không yêu cầu

### 7. [API-024] Gợi ý Tìm kiếm Nhanh (Autocomplete)
**Tính năng:** Gợi ý kết quả ngay khi đang gõ (Live Search). Trả về dữ liệu nhẹ nhất.
* **Endpoint:** `GET /api/v1/guest/search/autocomplete`
* **Auth:** Không yêu cầu

**Query Params:**
| Field | Type | Rules | Description |
|---|---|---|---|
| q | string | required, min:2 | Từ khóa tìm kiếm |

**Success Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "songs": [{ "id": 105, "title": "Chạy Ngay Đi" }],
    "artists": [{ "id": 1, "stage_name": "Sơn Tùng M-TP" }]
  }
}
```

### 8. [API-015] Tìm kiếm Toàn cục (Full Search)
**Tính năng:** Tìm kiếm chuyên sâu, phân trang rõ ràng theo từng Tab (Tất cả, Bài hát, Nghệ sĩ, Album, Playlist).
* **Endpoint:** `GET /api/v1/guest/search`
* **Auth:** Không yêu cầu

**Query Params:**
| Field | Type | Rules | Description |
|---|---|---|---|
| q | string | required | Từ khóa tìm kiếm |
| type | string | nullable | all, song, artist, album, playlist |
| page | integer | nullable | Số trang |

---

## 💻 Chi tiết các APIs - Lịch sử Tìm kiếm

### 9. [API-170] Lấy Lịch sử Tìm kiếm
**Tính năng:** Lấy tối đa 20 từ khóa tìm kiếm gần nhất của Listener.
* **Endpoint:** `GET /api/v1/listener/search/recent`
* **Auth:** Bearer Token (Listener)

**Success Response (200 OK):**
```json
{
  "status": "success",
  "data": [
    { "id": 50, "keyword": "sơn tùng", "created_at": "..." }
  ]
}
```

### 10. [API-171] Xóa một từ khóa lịch sử
**Tính năng:** Xóa một từ khóa cụ thể khỏi lịch sử.
* **Endpoint:** `DELETE /api/v1/listener/search/recent/{id}`
* **Auth:** Bearer Token (Listener)

### 11. [API-172] Xóa toàn bộ lịch sử
**Tính năng:** Xóa sạch toàn bộ từ khóa tìm kiếm của User hiện tại.
* **Endpoint:** `DELETE /api/v1/listener/search/recent`
* **Auth:** Bearer Token (Listener)
