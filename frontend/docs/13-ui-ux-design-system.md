# Hệ thống Thiết kế (Global Design System) - Music Streaming CMS

> Dựa trên phân tích từ `ui-ux-pro-max`, tài liệu này định nghĩa hệ thống thiết kế tổng thể cho chiến dịch "Đập đi xây lại" Frontend của toàn bộ hệ thống Music Streaming CMS. Các luật này được kế thừa từ `design-system/music-streaming-cms/MASTER.md`.

## 1. Phong cách Tổng thể (Global Style)
- **Style:** Chế độ Kép (Dual-Mode: Light/Dark) với phong cách "Modern SaaS".
- **Phong cách tương tác:** Immersive/Interactive Experience - Đề cao hiệu ứng chuyển cảnh, hover mượt mà nhưng tinh tế.
- **Từ khóa:** Tech, Micro SaaS, Indigo primary, Cyan Accent, High readability.

## 2. Bảng màu (Color Palette)
Hệ thống sử dụng các màu semantic có khả năng tự động đảo màu giữa Light/Dark mode.

| Vai trò | Light Mode (Hex) | Dark Mode (Hex) | Tên biến CSS |
| :--- | :--- | :--- | :--- |
| **Primary** | `#6366F1` (Indigo 500) | `#818CF8` (Indigo 400) | `--color-primary` |
| **Accent/CTA** | `#06B6D4` (Cyan 500) | `#22D3EE` (Cyan 400) | `--color-accent` |
| **Background** | `#F8FAFC` (Slate 50) | `#0B0F19` (Tech Dark) | `--color-background` |
| **Surface** | `#FFFFFF` (White) | `#161D2C` (Dark Surface) | `--color-surface` |
| **Text Primary** | `#0F172A` (Slate 900)| `#F8FAFC` (Slate 50) | `--color-text-primary` |
| **Border** | `#E2E8F0` (Slate 200) | `#1F2937` (Gray 800) | `--color-border` |
| **Destructive**| `#EF4444` (Red 500) | `#F87171` (Red 400) | `--color-danger` |

## 3. Kiểu chữ (Typography)
- **Tiêu đề (Headings):** `Plus Jakarta Sans` (Gọn gàng, hiện đại, hiển thị số rất đẹp).
- **Văn bản (Body):** `Inter` (Font SaaS tiêu chuẩn, cực kỳ dễ đọc).
- **Nhập file CSS toàn cục:**
```css
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap');
```

## 4. Các hệ quy chiếu (Tokens)
- **Khoảng cách (Spacing):** 
  - `xs (4px)`, `sm (8px)` cho icon.
  - `md (16px)`, `lg (24px)` cho padding nội dung.
  - `xl (32px)` trở lên cho Layout section.
- **Bóng đổ (Shadows):** 
  - Ở Light Mode: Sử dụng `shadow-sm`, `shadow-md` tự nhiên.
  - Ở Dark Mode: Chuyển bóng đổ thành glow viền: `box-shadow: 0 0 15px rgba(34,211,238,0.3)`.

## 5. Quy tắc Code Component (Rules & Anti-patterns)
- ❌ **Không dùng Emoji làm Icon:** BẮT BUỘC dùng Vector/SVG (Phosphor, Heroicons, Tabler Icons).
- ❌ **Không đổi trạng thái (Hover/Focus) đột ngột:** BẮT BUỘC phải có `transition-all duration-200 ease-in-out`.
- ❌ **Không làm rung/lệch Layout khi Hover:** Thay vì `scale` lớn lên làm xô đẩy khối xung quanh, hãy dùng `transform: translateY(-1px)`.
- ❌ **Hardcode màu sắc (VD: `bg-slate-900`):** BẮT BUỘC dùng biến semantic (`bg-theme-surface`, `text-theme-text`) để hỗ trợ tự động đổi màu Dark/Light Mode.
- ✅ **Vùng chạm (Touch Target):** Các nút bấm phải to tối thiểu `44x44px` trên giao diện Mobile.
- ✅ **Phản hồi Tương tác:** Phải có `cursor-pointer` ở mọi nút bấm/vùng nhấn được.

## 6. Lộ trình Refactor
1. **P1:** Chỉnh sửa `index.css` để nhúng Font và Colors (Dual mode). *(Đã xong)*
2. **P2:** Đập đi xây lại các UI Core Components (BaseButton, BaseInput, BaseModal) theo CSS ở trên. *(Đã xong)*
3. **P3:** Giao việc cho AI Agent làm mới từng trang (VD: Sidebar, Playlist, Banners) tuân thủ 100% tài liệu này.
