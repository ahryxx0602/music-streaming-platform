# KIẾN TRÚC FRONTEND (FRONTEND ARCHITECTURE)

**Dự án:** Nền tảng Âm nhạc Trực tuyến (Audio Streaming Web App)
**Phiên bản:** 1.0

---

## 1. Công nghệ sử dụng (Tech Stack)

Frontend được xây dựng dưới dạng **Single Page Application (SPA)** để đảm bảo quá trình phát nhạc không bị gián đoạn khi người dùng điều hướng qua lại giữa các trang.

- **Core Framework:** Vue 3 (Composition API với `<script setup>`)
- **Build Tool:** Vite (Tối ưu tốc độ khởi động và HMR cực nhanh)
- **State Management:** Pinia (Thay thế Vuex, hỗ trợ TypeScript tốt hơn)
- **Routing:** Vue Router 4
- **HTTP Client:** Axios (Tích hợp interceptors xử lý Token/Cookie)
- **Styling:** TailwindCSS (Utility-first CSS) + CSS thuần cho các Micro-animations.
- **Audio Engine:** HTML5 `<audio>` API thuần hoặc thư viện `Howler.js` để kiểm soát bộ đệm (buffering) và stream.

---

## 2. Cấu trúc thư mục (Directory Structure)

Thư mục `src/` được tổ chức theo tính năng và mức độ tái sử dụng:

```text
src/
├── assets/            # Hình ảnh tĩnh, CSS toàn cục (index.css), Fonts
├── components/        # UI Components dùng chung (Dumb Components)
│   ├── ui/            # Nút bấm, Modal, Input, Spinner...
│   ├── player/        # Thanh phát nhạc (PlayerBar, Volume, Timeline)
│   └── shared/        # SongCard, ArtistCard, PlaylistCard
├── composables/       # Chứa các Reusable Hooks (VD: useAuth, useDebounce)
├── layouts/           # Các khung giao diện
│   ├── MainLayout.vue       # Dành cho Listener (Có thanh Player ở dưới)
│   ├── ArtistLayout.vue     # Dành cho không gian làm việc Nghệ sĩ (Sidebar riêng)
│   ├── AdminLayout.vue      # Dành cho quản trị viên
│   └── AuthLayout.vue       # Dành cho trang Login/Register
├── router/            # Cấu hình Vue Router & Navigation Guards
├── services/          # Các API calls (gom nhóm theo REST API modules)
│   ├── api.js         # Axios instance (cấu hình BaseURL, Interceptors)
│   ├── musicService.js
│   └── authService.js
├── stores/            # Pinia Global State
│   ├── playerStore.js # Quản lý danh sách phát, trạng thái nhạc
│   └── authStore.js   # Quản lý User info, Roles
├── views/             # Trang độc lập (Smart Components)
│   ├── Explore/
│   ├── Library/
│   ├── Artist/
│   └── Admin/
├── App.vue            # Root Component
└── main.js            # Entry point
```

---

## 3. Kiến trúc Audio Player (Trái tim của hệ thống)

Đây là chức năng quan trọng nhất của một app nghe nhạc. Nó cần phải thỏa mãn: **Nhạc không bị ngắt khi người dùng đổi URL (chuyển trang)**.

### 3.1 Vị trí đặt Component
Component `<GlobalPlayer />` sẽ được đặt bên trong `MainLayout.vue`, bên ngoài `<router-view />`. Điều này đảm bảo khi `<router-view />` thay đổi nội dung (từ trang Home sang trang Profile), Player vẫn không bị re-render.

```html
<!-- MainLayout.vue -->
<template>
  <div class="app-container">
    <Sidebar />
    <main class="content">
      <router-view /> <!-- Các trang thay đổi ở đây -->
    </main>
    <GlobalPlayer /> <!-- Nằm ngoài router-view, luôn cố định -->
  </div>
</template>
```

### 3.2 Quản lý State bằng Pinia (`playerStore`)
Pinia sẽ là bộ não điều khiển Player. 
- **State:** `currentSong`, `queue` (Danh sách phát), `isPlaying`, `progress`, `volume`.
- **Actions:** `play(song)`, `pause()`, `next()`, `prev()`, `addToQueue()`.
- **Persistence:** Lưu trữ `queue` và `currentSong` vào `localStorage` thông qua plugin `pinia-plugin-persistedstate`. Nếu người dùng ấn F5, hệ thống sẽ khôi phục lại bài hát đang nghe dở.
- **Resume Listening Sync:** Để đồng bộ vị trí phát nhạc (`progress`) lên Server mà không gây tải, Frontend sử dụng cơ chế **Debounce** (10 giây/lần) kết hợp hook vào sự kiện `pause` / `beforeunload` của trình duyệt để gọi API `PUT /listener/library/history` nhằm cập nhật `last_position`.

### 3.3 Cơ chế Tracking (Anti-Cheat) ở Frontend
Khi bài hát bắt đầu phát (`audio.play()`), Frontend sẽ sinh ra một chuỗi `session_id` (UUID).
Đoạn code sẽ theo dõi thời gian phát thực tế liên tục (Continuous playback - không tính thời gian pause và việc tua/scrubbing sẽ làm reset bộ đếm). Khi thời gian nghe thực tế liên tục đạt `>= 30 giây`, Frontend sẽ gọi API:
`POST /api/v1/listener/stream/track/{id}` kèm payload `session_id`.

### 3.4 Quản lý Signed URL hết hạn (Expired Audio Source)
Do file âm thanh được bảo vệ qua MinIO Signed URL có thời hạn (TTL), nếu người dùng Pause bài hát quá lâu rồi Play tiếp, URL có thể đã hết hạn (HTTP 403) khiến thẻ Audio ném lỗi.
- **Giải pháp:** Lắng nghe sự kiện `error` của thẻ `<audio>`. Nếu phát hiện lỗi 403 / Network Error, Frontend sẽ tự động gọi API `GET /listener/stream/url/{id}` để làm mới Signed URL, gán lại vào `audio.src` và dùng `audio.currentTime = progress` để seek về đúng vị trí đang nghe (Resuming State) trước khi tiếp tục phát.

---

## 4. Xử lý Authentication & API Interceptors

Frontend sử dụng **Sanctum SPA (HttpOnly Cookie)**, vì vậy luồng Auth được thiết lập như sau:

### 4.1 Axios Instance (`services/api.js`)

**Khởi tạo CSRF (Đặc thù Sanctum SPA):** 
Trước khi thực hiện các request làm thay đổi dữ liệu (POST, PUT, DELETE) lần đầu tiên, ứng dụng bắt buộc phải gọi `GET /sanctum/csrf-cookie` để khởi tạo CSRF Protection.

```javascript
import axios from 'axios';

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL + '/api/v1',
    withCredentials: true, // BẮT BUỘC để tự động đính kèm HttpOnly Cookie và CSRF Token
});

// Interceptor xử lý lỗi Global
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // Cookie hết hạn hoặc chưa đăng nhập -> Xóa state -> Đẩy về trang Login
            useAuthStore().logout();
            window.location.href = '/login';
        }
        if (error.response?.status === 403) {
            // Lỗi Role (VD Listener chui vào route Artist)
            alert("Bạn không có quyền truy cập khu vực này!");
        }
        return Promise.reject(error);
    }
);
export default api;
```

### 4.2 Vue Router Navigation Guards
Bảo vệ các Router không cho phép khách truy cập.
```javascript
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login' }); // Bắt buộc đăng nhập
  } else if (to.meta.role && authStore.user.role !== to.meta.role) {
    next({ name: 'Home' }); // Sai Role thì đá về Home
  } else {
    next();
  }
});
```

### 4.3 Chuẩn hóa Pagination Payload (Axios GET)
Để đồng bộ chặt chẽ với JSend Format và cơ chế phân trang của Backend, toàn bộ các request lấy danh sách (GET) ở Frontend phải truyền payload thống nhất:
```javascript
// Gọi API lấy danh sách bài hát
api.get('/songs', {
    params: {
        page: 1,       // Trang hiện tại
        per_page: 20   // Số lượng item trên 1 trang
    }
}).then(response => {
    const items = response.data.data.items; // Mảng dữ liệu
    const meta = response.data.data.meta;   // Thông tin phân trang (current_page, last_page...)
});
```

---

## 5. UI/UX & Aesthetics (Tính thẩm mỹ)

Để đạt được chất lượng giao diện **"Wow-effect"** chuẩn Premium:

1. **Dark Mode by default:** Sử dụng nền tối sang trọng (Dark gradients, Glassmorphism) để làm nổi bật các Cover Image (bìa bài hát).
2. **Typography:** Sử dụng các Font chữ hiện đại (như Inter, Outfit) thay cho Font mặc định của trình duyệt.
3. **Skeleton Loading:** Khi tải dữ liệu, hiển thị Skeleton thay vì Spinner để cảm giác ứng dụng mượt mà hơn.
4. **Micro-animations:** 
   - Hover effects trên các Card (Phóng to nhẹ 1.05x).
   - Thanh tiến trình (Progress bar) chạy mượt không bị giật cục.
   - Transition fade mượt mà khi chuyển trang.
