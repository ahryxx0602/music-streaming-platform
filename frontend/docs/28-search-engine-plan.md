# Kế hoạch Frontend: [SCR-LST-06] Tìm kiếm (Search Engine)

## 1. Mục tiêu
Xây dựng trang Tìm Kiếm Trung Tâm. Người dùng có một ô Input siêu to khổng lồ. Gõ đến đâu, kết quả đổ ra đến đó (Debounce Search) được chia thành các tab hoặc các phần: Bài hát, Nghệ sĩ, Album.

## 2. Cấu trúc UI (`SearchView.vue`)

### 2.1. Thanh Tìm Kiếm (Search Bar)
- **Vị trí:** Cố định trên cùng của phần Content (Dưới header/navbar).
- **Thiết kế:** Input `type="text"` lớn, bo góc tròn (rounded-full), nền kính mờ `bg-white/10`, icon kính lúp (Search) bự bên trái, nút "X" (Clear) bên phải khi có text.
- **Tương tác:** Focus vào input sẽ có outline viền sáng Neon (`focus:ring-theme-primary`).
- **Debounce Logic:** Sử dụng Lodash `debounce` hoặc custom timeout (khoảng 500ms) để khi user ngừng gõ mới gọi API, tránh spam Request.

### 2.2. Trạng thái Hiển thị (Display States)
- **State 1: Empty (Chưa gõ gì)**
  - Hiển thị các khối màu (Color Blocks) đại diện cho các "Thể loại" (Browse all genres) giống phong cách Spotify.
- **State 2: Loading**
  - Hiển thị Skeleton Loader (nhấp nháy xám) khi đang gọi API.
- **State 3: Có Kết quả (Results)**
  - Chia làm 3 hàng/cột:
    - **Top Result (Kết quả phù hợp nhất):** Ô to nhất bên trái (Thường là Nghệ sĩ hoặc Bài hát top 1).
    - **Bài Hát (Songs):** Danh sách dạng List dọc bên phải Top Result. Mỗi dòng có nút Play, nhấn Play sẽ gọi `playerStore.fetchAndPlaySong()`.
    - **Nghệ sĩ & Album (Grid):** Hiển thị dạng lưới (Card) bên dưới phần Bài hát.

## 3. Công nghệ & Thư viện
- **Debounce:** Xử lý bằng Javascript thuần hoặc `lodash.debounce`.
- **API Call:** Axios với URL `GET /api/v1/listener/search?q=keyword`.

## 4. UI/UX Style
- Tiếp tục tuân thủ **OLED Dark Mode**.
- Các item kết quả tìm kiếm khi Hover vào sẽ đổi màu nền (Highlight) và hiện icon Play.

## 5. Hướng dẫn cho Frontend Agent
1. Tạo component `frontend/src/views/listener/SearchView.vue`.
2. Dựng UI gồm ô Input to đùng.
3. Code logic Vue `watch` biến `searchQuery` kết hợp `setTimeout` để debounce 500ms trước khi chạy hàm `performSearch()`.
4. Trong hàm `performSearch`, gọi API (tạm thời mock data) và bind ra UI 3 cục: Songs, Artists, Albums.
5. Cắm action `handlePlay` cho các bài hát tìm được để đưa vào Global Player.
6. Báo cáo kết quả, TUYỆT ĐỐI KHÔNG PUSH.
