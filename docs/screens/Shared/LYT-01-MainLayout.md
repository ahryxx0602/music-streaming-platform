# [LYT-01] Main Layout (Bố Cục Cốt Lõi)

> **Mô tả ngắn:** Khung giao diện chính (Master Layout) được sử dụng cho hơn 90% các trang trong ứng dụng dành cho Listener và Artist. Nó bao bọc `<router-view>` bằng các thành phần tĩnh không bị re-render khi chuyển trang.

## 1. Cấu trúc Hình học (Geometry)
Màn hình được chia làm 3 khu vực cố định (Fixed) và 1 khu vực cuộn (Scrollable):
- **Left (Trái):** Sidebar Navigation (`width: 280px`). Cố định chiều cao 100vh trừ đi phần Player.
- **Bottom (Dưới):** Bottom Player (`height: 90px`). Cố định ở đáy màn hình `bottom: 0`, đè lên tất cả (`z-index: 50`).
- **Top (Trên):** Header (`height: 64px`). Sticky ở trên cùng của Content Area (`z-index: 40`), nền Glassmorphism.
- **Center (Giữa):** Content Area. Khu vực chứa `<router-view>`, chiếm phần diện tích còn lại, có thanh cuộn riêng (Virtual Scroll).

## 2. Liên kết Component (Children)
- `[UI-CMP-01]` Sidebar.vue
- `[UI-CMP-02]` BottomPlayer.vue
- `[UI-CMP-03]` Header.vue

## 3. Hành vi (Behavior)
- **Cấm cuộn toàn trang (No Body Scroll):** `body` được set `overflow: hidden`. Cuộn chỉ xảy ra bên trong Content Area.
- **Responsive (Mobile/Tablet):**
  - `< 768px`: Sidebar bị ẩn, biến thành Bottom Navigation Bar (nằm ngay trên Bottom Player).
  - Header thu gọn, ô Search chuyển thành icon kính lúp.

## 4. Tác động CSS (Style Hooks)
- Sử dụng CSS Grid hoặc Flexbox:
  ```css
  .main-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    grid-template-rows: 1fr 90px;
    height: 100vh;
  }
  ```
