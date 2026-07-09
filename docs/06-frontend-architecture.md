# KIẾN TRÚC FRONTEND (FRONTEND ARCHITECTURE)

**Dự án:** Nền tảng Âm nhạc Trực tuyến (Audio Streaming Web App)
**Phiên bản:** 1.1

---

## 1. Công nghệ sử dụng (Tech Stack)

Frontend được xây dựng dưới dạng **Single Page Application (SPA)** để đảm bảo quá trình phát nhạc không bị gián đoạn khi người dùng điều hướng qua lại giữa các trang.

- **Core Framework:** Vue 3 (Composition API với `<script setup>`)
- **Build Tool:** Vite (Tối ưu tốc độ khởi động và HMR cực nhanh)
- **State Management:** Pinia (Thay thế Vuex, hỗ trợ TypeScript tốt hơn)
- **Routing:** Vue Router 4
- **HTTP Client:** Axios (Tích hợp interceptors xử lý Token/Cookie)
- **Styling:** TailwindCSS v4 + CSS Variables (Vanilla CSS)
- **Audio Engine:** HTML5 `<audio>` API thuần hoặc thư viện `Howler.js`

---

## 2. Cấu trúc thư mục (Folder Structure)

Thư mục `src/` được tổ chức cực kỳ chặt chẽ theo tính năng và mức độ tái sử dụng (Dumb vs Smart components).

```text
src/
├── assets/            # Hình ảnh tĩnh, CSS toàn cục (index.css), Fonts
├── components/        # Tầng Component (Chỉ nhận Props, xả Emits)
│   ├── base/          # [Atoms] Chứa các hạt nhân thuần túy, KHÔNG dính líu đến nghiệp vụ. VD: BaseButton.vue, BaseInput.vue, BaseModal.vue, Spinner.vue.
│   ├── domain/        # [Molecules/Organisms] Chứa các khối mang logic nghiệp vụ nhưng tái sử dụng nhiều nơi. VD: SongCard.vue, TrackList.vue, ArtistAvatar.vue, PlayerBar.vue.
├── composables/       # Chứa các Reusable Hooks (VD: useAuth, useDebounce, VueUse)
├── layouts/           # Khung giao diện (Layout)
│   ├── MainLayout.vue       # Dành cho Listener (Có thanh Player cố định)
│   ├── ArtistLayout.vue     # Dành cho không gian làm việc Nghệ sĩ
│   └── AuthLayout.vue       # Dành cho trang Login/Register
├── router/            # Vue Router & Navigation Guards
├── services/          # HTTP & API calls (Axios)
├── stores/            # Pinia Global State (authStore, playerStore)
├── views/             # Trang độc lập (Smart Components)
│   ├── Explore/
│   ├── Library/
│   └── Admin/
├── App.vue            # Root Component
└── main.ts            # Entry point
```

**Sự khác biệt cốt lõi giữa `layouts` và `views`:**
- **Layouts (`src/layouts`):** Là những cái khung (frame) bên ngoài chứa thanh điều hướng (Sidebar, Navbar) và trình phát nhạc cố định. Layout có thẻ `<router-view>` ở giữa để "bơm" nội dung vào.
- **Views (`src/views`):** Là những trang thực tế (Page) mang logic cao nhất, có nhiệm vụ gọi API, lấy dữ liệu và đẩy dữ liệu xuống cho các `components` hiển thị.

---

## 3. Chiến lược Quản lý Trạng thái (State Management)

Dự án áp dụng quy tắc phân tách State rõ ràng để tránh làm phình to bộ nhớ (Memory Leak) và dễ debug:

- **Local State (`ref()`, `reactive()`):**
  - **Sử dụng khi:** Dữ liệu chỉ dùng nội bộ trong 1 component hoặc cha truyền con 1 cấp.
  - **Ví dụ:** Trạng thái mở/đóng của một Modal (`isOpen = ref(false)`), dữ liệu Form đăng nhập đang gõ (`const form = reactive({email, password})`), trạng thái đang loading của 1 nút bấm (`isSubmitting`).

- **Global State (Pinia):**
  - **Sử dụng khi:** Dữ liệu mang tính "Toàn cục", được nhiều View/Layout chia sẻ và truy cập liên tục.
  - **Ví dụ:**
    - `authStore`: Quản lý thông tin User đăng nhập, Token (nếu có), Vai trò (Role).
    - `playerStore`: Trạng thái của Audio Player (Current Playing Track, Playlist Queue, Volume, IsPlaying). Trạng thái này phải sống độc lập khỏi Views để nhạc không bị tắt khi chuyển trang.

---

## 4. Kiến trúc CSS (Styling Strategy)

Để có một giao diện "Premium" và dễ bảo trì, dự án kết hợp TailwindCSS v4 và Vanilla CSS theo nguyên tắc sau:

1. **CSS Variables (Biến dùng chung ở Root):**
   - Định nghĩa TẤT CẢ các mã màu chủ đạo (Primary, Background, Surface, Text) thành các biến CSS (`--color-primary`, `--bg-dark`) bên trong file `src/assets/main.css` (hoặc `index.css`).
   - Giúp việc đổi Theme (Light/Dark mode) chỉ cần 1 thao tác đổi giá trị biến ở lớp `:root`.

2. **Global CSS vs Scoped CSS:**
   - **Global CSS (`main.css`):** Chỉ dùng để cấu hình `@tailwind`, khai báo CSS Variables, Typography gốc và các class hoạt ảnh (Micro-animations: `@keyframes spin`, `.glass-effect`) dùng lặp đi lặp lại trên toàn hệ thống.
   - **`<style scoped>`:** Bắt buộc áp dụng cho TẤT CẢ các file `.vue`. Tuyệt đối không viết CSS toàn cục bừa bãi trong Component. Ưu tiên dùng Tailwind utility classes trực tiếp trên template trước khi phải mở thẻ `<style>`.

---

## 5. Xử lý Network & Lỗi (API Interceptors)

Frontend giao tiếp với Backend qua Axios. Quá trình bắt lỗi (Error Handling) được tự động hóa tại Tầng Network (Interceptors) để tái sử dụng logic và tránh lặp code ở các Component.

```javascript
// src/services/api.ts
import axios from 'axios';
import { useAuthStore } from '@/stores/authStore';

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL + '/api/v1',
    withCredentials: true, // BẮT BUỘC để đính kèm HttpOnly Cookie (Sanctum SPA)
});

// Interceptor xử lý lỗi Global
api.interceptors.response.use(
    (response) => response,
    async (error) => {
        const authStore = useAuthStore();
        
        // HTTP 401: Unauthorized (Chưa đăng nhập, hoặc Session hết hạn)
        if (error.response?.status === 401) {
            authStore.logout(); // Xóa sạch dữ liệu User hiện hành
            window.location.href = '/login'; // Cưỡng chế đá ra trang đăng nhập
        }
        
        // HTTP 403: Forbidden (Sai Role, không có quyền. VD Listener truy cập API Artist)
        if (error.response?.status === 403) {
            alert("Bạn không có quyền truy cập chức năng này!");
            window.location.href = '/'; // Đẩy về trang chủ/Dashboard
        }

        // HTTP 419: CSRF Token Mismatch (Đặc thù bảo mật của Laravel Sanctum)
        if (error.response?.status === 419) {
            // Tự động xử lý Refresh CSRF Token (gọi lại /sanctum/csrf-cookie)
            await api.get('/sanctum/csrf-cookie');
            return api.request(error.config); // Re-try request ban đầu
        }

        return Promise.reject(error);
    }
);
export default api;
```

---

## 6. Kiến trúc Audio Player (Trái tim hệ thống)

### 6.1 Vị trí đặt Component
Component `<GlobalPlayer />` được đặt bên trong `MainLayout.vue`, bên ngoài `<router-view />`. Điều này đảm bảo khi `<router-view />` thay đổi nội dung, Player vẫn không bị re-render.

### 6.2 Quản lý State bằng Pinia (`playerStore`)
Pinia điều khiển Player (`currentSong`, `queue`, `isPlaying`). Sử dụng plugin `pinia-plugin-persistedstate` lưu `queue` vào `localStorage` để chống mất bài đang nghe khi F5.

### 6.3 Cơ chế Tracking (Anti-Cheat)
Nghe `>= 30 giây` sẽ kích hoạt API `POST /api/v1/listener/stream/track/{id}` đính kèm `session_id`. Tua (scrubbing) làm reset bộ đếm thời gian liên tục.

### 6.4 Quản lý Signed URL hết hạn
Bắt sự kiện `error` của thẻ `<audio>`. Nếu lỗi 403 (URL MinIO hết hạn), Frontend gọi `GET /listener/stream/url/{id}` lấy URL mới, gán vào `audio.src` và `audio.currentTime = progress` để phát tiếp mượt mà.

---

## 7. UI/UX & Aesthetics (Tính thẩm mỹ)

1. **Dark Mode by default:** Sử dụng nền tối sang trọng (Dark gradients, Glassmorphism).
2. **Typography:** Font chữ hiện đại (Inter, Outfit).
3. **Skeleton Loading:** Hiển thị Skeleton thay vì Spinner giật cục khi load dữ liệu.
4. **Micro-animations:** Hover effects phóng to, thanh tiến trình mượt.
