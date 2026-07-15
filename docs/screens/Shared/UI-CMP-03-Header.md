# [UI-CMP-03] Sticky Header (Thanh Công Cụ Trên)

> **Mô tả ngắn:** Thanh điều hướng phụ nằm ở trên cùng của Content Area, tự động thu nhỏ/mờ đi khi người dùng cuộn trang. Chứa thanh tìm kiếm toàn cầu và thông tin hồ sơ (User Menu).

## 1. Thông tin chung
- **Vị trí:** Top của Content Area trong `[LYT-01]` MainLayout.
- **Kích thước:** `height: 64px`.
- **Trạng thái:** Bám dính (Sticky) khi cuộn nội dung ở dưới. Nền tự động áp dụng hiệu ứng Glassmorphism khi trang bị cuộn xuống.

## 2. Thành phần giao diện (UI Elements)
- **Nút Back/Forward (Điều hướng Lịch sử):** 2 nút tròn cho phép quay lại trang trước hoặc tiến tới trang sau (giống hệt hành vi của Browser).
- **Thanh Tìm kiếm Toàn cầu (Global Search):**
  - Ô input bo tròn, có icon kính lúp.
  - Hiển thị kết quả tìm kiếm nhanh (Popup Dropdown) bằng `Floating UI`.
- **Khu vực User (Right side):**
  - **Nếu là Guest:** Hiển thị nút "Đăng ký" và nút "Đăng nhập" nổi bật (`var(--color-primary)`).
  - **Nếu đã Đăng nhập:**
    - Icon Chuông thông báo (Notification) kèm số chấm đỏ đếm thông báo chưa đọc.
    - User Avatar (Ảnh đại diện tròn). Click vào sẽ mở Dropdown Menu.

## 3. Liên kết dữ liệu & Logic (State)
- **Search Logic:** Nhập liệu vào thanh search phải được `Debounce` (chờ 300ms sau khi ngừng gõ mới gọi API) thông qua hàm `useDebounceFn` của `VueUse` để chống spam API.
- **User Dropdown:** 
  - Render theo `authStore.role`. 
  - Nút "Đăng xuất" sẽ gọi hàm `authStore.logout()`.
- **Scroll Hook:** Dùng `useWindowScroll` của VueUse để theo dõi vị trí cuộn y. Nếu `y > 0` thì thêm class CSS `.scrolled` vào Header để đổi nền thành Glass.

## 4. Tác động CSS (Aesthetics)
- Nền mặc định: `transparent`.
- Nền khi cuộn (Scrolled): `rgba(10, 15, 31, 0.75)` kết hợp `backdrop-filter: blur(16px)`.
- Chuyển động (Transitions): Áp dụng `transition: background-color 0.3s ease` để quá trình biến đổi nền mượt mà.
