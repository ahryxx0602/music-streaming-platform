# HỆ THỐNG THIẾT KẾ & BỘ MÀU (DESIGN SYSTEM)

**Dự án:** Nền tảng Âm nhạc Trực tuyến
**Chủ đề Theme:** Aurora Blue (Đại diện cho Công nghệ + Streaming + Hiện đại + Sang trọng)

---

## 🎯 Phong cách Tổng thể (Aurora Blue Concept)
- **Cảm giác:** Bầu trời đêm tĩnh lặng (Nền tối) kết hợp với dải cực quang (Aurora Blue) nổi bật.
- **Tính chất:** Phù hợp hoàn hảo với một nền tảng âm nhạc cao cấp. Ít gây mỏi mắt trong chế độ Dark Mode, nhưng vẫn tạo được điểm nhấn công nghệ và sự sang trọng.
- **Khả năng mở rộng:** Dễ dàng áp dụng cho Home, Playlist, Artist Dashboard và Admin Dashboard mà vẫn giữ được sự nhất quán tuyệt đối.

---

## 🌌 1. Primary Colors (Màu Chủ Đạo)
Màu xanh Aurora được sử dụng để điều hướng sự chú ý của người dùng vào các nút hành động (Call-to-Action) và các thanh tiến trình (Progress Bar).

| Vai trò | Màu sắc | Mã HEX |
| :--- | :--- | :--- |
| **Primary** | Aurora Blue | `#3B82F6` |
| **Primary Hover** | Bright Blue | `#60A5FA` |
| **Primary Active** | Deep Blue | `#2563EB` |
| **Secondary** | Cyan | `#06B6D4` |
| **Accent** | Sky Blue | `#38BDF8` |

---

## 🌑 2. Backgrounds (Nền & Không gian)
Giao diện phân tầng chiều sâu dựa trên mức độ sáng của nền. Nền càng sáng, tầng không gian càng gần người dùng.

| Thành phần | Mã HEX |
| :--- | :--- |
| **Main Background** | `#0A0F1F` |
| **Sidebar** | `#101827` |
| **Surface** | `#131B2F` |
| **Card** | `#1A2740` |
| **Modal / Dialog** | `#16233A` |
| **Dropdown Menu** | `#182842` |

---

## 📝 3. Text Colors (Màu Chữ)
Đảm bảo độ tương phản (Contrast Ratio) chuẩn WCAG để tối ưu hóa khả năng đọc (Accessibility).

| Thành phần | Mã HEX |
| :--- | :--- |
| **Primary Text** | `#FFFFFF` |
| **Secondary Text** | `#CBD5E1` |
| **Muted Text** | `#94A3B8` |
| **Disabled** | `#64748B` |
| **Placeholder** | `#7C8CA5` |

---

## 🔲 4. Border (Đường Viền)
Giúp phân định ranh giới giữa các khối giao diện (Cards, Inputs) mà không gây nhức mắt.

| Thành phần | Mã HEX |
| :--- | :--- |
| **Default Border** | `#2A3B57` |
| **Hover Border** | `#3B82F6` |
| **Focus Border** | `#60A5FA` |
| **Divider (Đường kẻ)**| `#24344F` |

---

## 🔘 5. Components (Nút & Nhập Liệu)

### Buttons
- **Primary Button:** 
  - Nền: `#3B82F6`
  - Text: `#FFFFFF`
  - Hover: `#2563EB`
- **Secondary Button (Outline/Ghost):** 
  - Nền: `transparent`
  - Viền: `#3B82F6`
  - Hover: `rgba(59, 130, 246, 0.12)`

### Input Fields
- **Nền:** `#131B2F`
- **Viền Default:** `#2A3B57`
- **Hover:** `#3B82F6`
- **Focus:** `#60A5FA` với hiệu ứng bóng mờ `0 0 0 4px rgba(59, 130, 246, 0.18)`
- **Placeholder:** `#7C8CA5`
- **Text:** `#FFFFFF`
- **Icon Input:** `#94A3B8`

### Cards
- **Nền:** `#1A2740`
- **Viền:** `#24344F`
- **Hover:** Di chuyển lên trên `translateY(-4px)`
- **Bóng (Shadow):** `0 16px 40px rgba(0, 0, 0, 0.25)`

---

## ✨ 6. Hiệu ứng Gradients & Glassmorphism

### Gradients
- **Primary Gradient:** `linear-gradient(135deg, #3B82F6, #06B6D4)`
- **Hero Gradient:** `linear-gradient(135deg, #2563EB, #3B82F6, #38BDF8)`
- **Background Glow (Mờ ảo nền):** `radial-gradient(circle, rgba(59, 130, 246, 0.25), transparent 70%)`

### Glass Effect (Hiệu ứng kính mờ cho Navbar, Sticky Header)
```css
background: rgba(19, 27, 47, 0.75);
backdrop-filter: blur(16px);
border: 1px solid rgba(255, 255, 255, 0.08);
```

---

## 🌟 7. Shadows (Đổ Bóng)
- **Small (Items nhỏ):** `0 2px 8px rgba(0,0,0,0.15)`
- **Medium (Dropdowns, Modals):** `0 8px 24px rgba(0,0,0,0.25)`
- **Large (Hero elements):** `0 20px 50px rgba(0,0,0,0.35)`
- **Primary Glow (Nổi bật cho các nút Active):** `0 0 30px rgba(59,130,246,0.22)`

---

## 📊 8. Status Colors (Thông báo Trạng Thái)
| Trạng thái | Mã HEX |
| :--- | :--- |
| **Success** | `#22C55E` |
| **Warning** | `#F59E0B` |
| **Error** | `#EF4444` |
| **Info** | `#0EA5E9` |

---

## 🎵 9. Accent cho Music Streaming (Chuyên Biệt)
Màu nhấn dành riêng cho trải nghiệm nghe nhạc:
- **Album đang phát:** `#3B82F6` (Xanh nhạt)
- **▶️ Nút Play:** `#3B82F6`
- **⏸ Nút Pause:** `#2563EB` (Xanh đậm đà hơn)
- **❤️ Nút Like (Trái tim):** `#EF4444`
- **⭐ Nút Favorite (Playlist):** `#FACC15`
- **🎤 Artist Verified (Dấu tích):** `#06B6D4`
- **🎼 Progress Bar (Thanh phát nhạc):** Gradient từ `#3B82F6` ➔ `#06B6D4`
- **🔊 Volume Slider:** `#38BDF8`

---

## 🌊 10. Animations (Chuyển Động)
Sử dụng hiệu ứng chuyển cảnh mềm mại tạo cảm giác mượt mà nhưng phản hồi tức thời.
- **Hover Button:** `200ms ease`
- **Input Focus:** `150ms ease`
- **Card Hover:** `250ms ease`
- **Sidebar (Đóng/Mở):** `300ms ease`
- **Dialog / Modal:** `250ms ease`
- **Progress Bar:** `linear` (chạy mượt theo tiến trình)
- **Đĩa than (Vinyl Record):** Quay `8–12s` mỗi vòng, kiểu `linear infinite`
- **Audio Wave (Sóng nhạc):** Dao động nhẹ theo nhịp với biên độ nhỏ

---

## 🌓 11. Cấu hình Theme Sáng/Tối (Light/Dark Mode Mapping)
Dự án sử dụng chiến lược **CSS Variables Mapping** để hỗ trợ mượt mà cả 2 giao diện. Toàn bộ mã HEX ở các phần trên mặc định mô tả cho **Dark Mode**. Đối với **Light Mode**, chúng ta cung cấp một bộ màu tương phản (Contrast) sạch sẽ, trắng/xám hiện đại.

**Tuyệt đối không** sử dụng class `dark:bg-black` rải rác trên giao diện (làm dơ file `.vue`). Chỉ khai báo 2 bộ màu duy nhất tại `src/assets/main.css`:

```css
/* ----------------------------------- */
/* 🌞 LIGHT MODE (Giao diện Sáng)       */
/* ----------------------------------- */
:root {
  /* Background */
  --bg-main: #F8FAFC;     /* Trắng xám nhạt */
  --bg-sidebar: #FFFFFF;
  --bg-surface: #F1F5F9;
  --bg-card: #FFFFFF;
  
  /* Text */
  --text-primary: #0F172A;
  --text-secondary: #475569;
  --text-muted: #94A3B8;

  /* Border */
  --border-default: #E2E8F0;
  
  /* Primary - Cần đậm hơn xíu để nổi trên nền trắng */
  --color-primary: #2563EB; 
}

/* ----------------------------------- */
/* 🌚 DARK MODE (Giao diện Tối - Aurora) */
/* ----------------------------------- */
.dark {
  /* Background */
  --bg-main: #0A0F1F;
  --bg-sidebar: #101827;
  --bg-surface: #131B2F;
  --bg-card: #1A2740;

  /* Text */
  --text-primary: #FFFFFF;
  --text-secondary: #CBD5E1;
  --text-muted: #94A3B8;

  /* Border */
  --border-default: #2A3B57;
  
  /* Primary */
  --color-primary: #3B82F6;
}
```

**Cách code UI ở Vue Components:**
Trong file config `tailwind.config.js`, chúng ta sẽ khai báo:
```javascript
theme: {
  extend: {
    colors: {
      primary: 'var(--color-primary)',
      background: 'var(--bg-main)',
      card: 'var(--bg-card)',
      // ...
    }
  }
}
```
Khi Code template `.vue`, dev chỉ cần viết ngắn gọn: `<div class="bg-background text-primary">...</div>`. Hệ thống sẽ tự động chuyển màu 100% khi người dùng Switch Theme (chuyển đổi bằng `@vueuse/core` thay đổi thẻ HTML class).
