# Hướng dẫn Refine UI/UX Module Authentication (Concept: Minimal & Ambient)

Tài liệu này dùng để hướng dẫn team Frontend (hoặc phiên chat Frontend) thực hiện nâng cấp UI/UX cho màn hình Đăng nhập & Đăng ký, dựa trên bản đánh giá thiết kế chuyên sâu từ UI/UX Designer.

---

## 1. Bối cảnh & Vấn đề hiện tại
- **Thiếu bản sắc âm nhạc:** Giao diện tối giản nhưng đang bị nhầm lẫn với các app SaaS/Tài chính. Chưa toát lên "vibe" của một nền tảng âm nhạc.
- **Không gian trống trải:** Xung quanh form có quá nhiều khoảng trống (White space) chưa được xử lý, khiến form như đang lơ lửng trong không gian rỗng.
- **Thiếu chiều sâu:** Các thành phần đang quá "phẳng", tương phản của Input với nền chưa đủ sắc nét để dẫn dắt ánh mắt người dùng.

---

## 2. Giải pháp: Concept "Minimal & Ambient"
Chuyển đổi giao diện thành một "Sân khấu điện ảnh" với ánh sáng gradient và không gian mờ ảo, rất phù hợp với nhạc R&B/City Pop. Cụ thể cần triển khai 3 thành phần sau:

### 2.1. Yếu tố đồ họa: "Vinyl Halo" (Vòng ánh sáng đĩa than)
- **Mô tả:** Đặt một khối hình tròn lớn, chứa màu gradient mờ ảo ở ngay phía sau Form đăng nhập/đăng ký.
- **Màu sắc:** Phối giữa màu Tím (Purple) và Hồng (Pink) đặc trưng của dự án, nhưng độ Opacity thấp, các viền được làm nhòe (Blur) cực mạnh để tạo thành một "quầng sáng" (Halo).
- **Hiệu ứng (Animation):** Cho quầng sáng này **xoay cực chậm** (ví dụ: `animation: spin 120s linear infinite`). Cảm giác mang lại sẽ giống như một chiếc đĩa Vinyl đang quay chậm rãi hoặc một làn sóng âm thanh đang lan tỏa trong không gian.

### 2.2. Yếu tố chiều sâu: Hạt sáng lơ lửng (Ambient Particles)
- **Mô tả:** Bổ sung các hạt sáng (particles) hoặc các vệt sáng nhỏ, mờ ảo lơ lửng trôi dạt ngẫu nhiên xung quanh màn hình.
- **Triển khai:** Có thể code thuần bằng CSS (tạo vài thẻ div nhỏ trôi dạt) hoặc dùng thư viện nhẹ (như `tsparticles`). **Lưu ý:** Chỉ cần cực kỳ tinh tế, số lượng ít, di chuyển thật chậm. Không làm quá lố gây rối mắt.

### 2.3. Nâng cấp Khối Form (Glassmorphism Container)
Để Form không bị chìm vào nền và nổi bật lên trên quầng sáng "Vinyl Halo", khối bọc ngoài Form (Form Container) cần được ép chất liệu Kính mờ (Glassmorphism) cao cấp:
- **Nền (Background):** `rgba(255, 255, 255, 0.03)`
- **Độ mờ (Backdrop Filter):** `blur(24px)`
- **Viền (Border):** `1px solid rgba(255, 255, 255, 0.08)` (chỉ tạo một đường nét sắc mảnh ở rìa kính).
- **Bo góc (Border Radius):** `24px` (Góc bo lớn, tạo sự liền mạch, thân thiện).
- **Bóng đổ (Shadow):** `0 25px 50px -12px rgba(0, 0, 0, 0.5)` (Giúp khối kính tách biệt hẳn với nền phía sau).

---

## 3. Gợi ý cấu trúc Code (Vue.js)

```vue
<template>
  <div class="relative min-h-screen flex items-center justify-center bg-[#0a0a0f] overflow-hidden">
    
    <!-- 1. Vinyl Halo Animation Background -->
    <div class="absolute w-[800px] h-[800px] rounded-full vinyl-halo-gradient opacity-40 blur-[80px] animate-[spin_120s_linear_infinite]"></div>

    <!-- 2. Floating Particles (Có thể dùng thư viện hoặc CSS thuần) -->
    <div class="absolute inset-0 pointer-events-none">
        <!-- Các hạt sáng trôi dạt -->
    </div>

    <!-- 3. Glassmorphism Form Container -->
    <div class="relative z-10 w-full max-w-md p-8 rounded-[24px] border border-white/10 bg-white/[0.03] backdrop-blur-[24px] shadow-2xl">
        <!-- Nội dung Form: Tiêu đề, Các BaseInput, BaseButton... -->
        <router-view />
    </div>

  </div>
</template>

<style scoped>
.vinyl-halo-gradient {
  background: conic-gradient(from 0deg, #8B5CF6, #EC4899, #8B5CF6);
  /* Hoặc phối các màu brand của nền tảng */
}
</style>
```

---

## 4. Checklist dành cho Frontend Developer
- [ ] Bố cục lại màn hình Auth (`LoginView`, `RegisterView`) để đưa form vào bên trong khối `Glassmorphism Container`.
- [ ] Xây dựng background `Vinyl Halo` có hiệu ứng xoay (spin) chậm phía sau khối kính.
- [ ] Thêm hiệu ứng hạt sáng lơ lửng xung quanh bằng CSS tĩnh hoặc thư viện tối giản.
- [ ] Kiểm tra lại độ tương phản (Contrast): Đảm bảo chữ, input và nút bấm hiển thị sắc nét, không bị chói khi đi qua các vùng sáng của Vinyl Halo.
