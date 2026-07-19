# Kế hoạch Triển khai Đa ngôn ngữ (i18n - Vi/En)

Tài liệu này đặc tả quy trình kỹ thuật chuẩn xác nhất để tích hợp hệ thống đa ngôn ngữ (Internationalization) cho dự án Music Streaming Platform, bao gồm cả Frontend (Vue 3) và Backend (Laravel 11).

## 1. Mục tiêu kiến trúc (Architecture Decisions)
- **Frontend:** Sử dụng `vue-i18n` với cấu hình Native Composition API (`legacy: false`). Phân tách rõ ràng file JSON theo module nếu cần để tránh phình dung lượng.
- **Backend Validation:** Không dịch tay. Sử dụng thư viện cộng đồng `laravel-lang/lang` để tự động hóa 100% câu báo lỗi mặc định của Laravel sang Tiếng Việt.
- **Backend API Responses:** Sử dụng Middleware nội bộ chặn header `Accept-Language` để chuyển đổi ngữ cảnh ngôn ngữ (`App::setLocale()`), giúp Backend trả về thông báo lỗi/thành công đúng với ngôn ngữ user đang chọn.

---

## 2. Kế hoạch Phía Frontend (Vue 3 + Vite)

### Bước 2.1: Cài đặt và Thiết lập Cấu trúc
- **Package:** `npm install vue-i18n@9`
- **Cấu trúc thư mục:**
  ```text
  src/
   ├─ locales/
   │   ├─ vi.json  (Ngôn ngữ mặc định)
   │   └─ en.json  (Ngôn ngữ bổ sung)
   └─ plugins/
       └─ i18n.ts  (File cấu hình instance)
  ```

### Bước 2.2: Khởi tạo Configuration (Critical Step)
Tạo file `src/plugins/i18n.ts`:
```typescript
import { createI18n } from 'vue-i18n';
import vi from '../locales/vi.json';
import en from '../locales/en.json';

const savedLocale = localStorage.getItem('app-locale') || 'vi';

export const i18n = createI18n({
  legacy: false, // [BẮT BUỘC] Tắt legacy mode để dùng Composition API
  globalInjection: true, // Cho phép dùng $t trực tiếp trên template
  locale: savedLocale,
  fallbackLocale: 'vi',
  messages: { vi, en }
});
```
- Mở `main.ts` và đăng ký: `app.use(i18n)`.

### Bước 2.3: Xử lý Axios (Interceptor)
Mở `src/services/api.ts` để gài tự động Header:
```typescript
api.interceptors.request.use(config => {
  const currentLocale = localStorage.getItem('app-locale') || 'vi';
  config.headers['Accept-Language'] = currentLocale;
  return config;
});
```
*(Nếu cần dịch text ở ngoài Component như Pinia, hãy import `i18n` từ `plugins/i18n` và gọi `i18n.global.t('key')`)*.

### Bước 2.4: Xây dựng Language Switcher Component
- Nằm trên `Header.vue`.
- Gồm 1 dropdown (Cờ VN và Cờ UK).
- Logic:
  ```typescript
  import { useI18n } from 'vue-i18n';
  const { locale } = useI18n();
  
  const switchLanguage = (lang: 'vi' | 'en') => {
    locale.value = lang;
    localStorage.setItem('app-locale', lang);
    window.location.reload(); // Hoặc cập nhật reactive nếu không có API caching nặng
  }
  ```

---

## 3. Kế hoạch Phía Backend (Laravel 11)

### Bước 3.1: Quản lý Validation Messages
- **Lệnh thực thi:** `composer require laravel-lang/lang --dev` (Có thể thay thế bằng package tương đương hỗ trợ Laravel 11).
- Chạy lệnh publish bộ ngôn ngữ Tiếng Việt vào thư mục `lang/vi`.
- Thay đổi `config/app.php` (nếu có) hoặc biến môi trường:
  ```env
  APP_LOCALE=vi
  APP_FALLBACK_LOCALE=en
  ```

### Bước 3.2: Khởi tạo Middleware Chuyển Đổi (Locale Switcher)
- **Tạo Middleware:** `php artisan make:middleware Localization`
- **Logic bên trong `handle()`:**
  ```php
  public function handle(Request $request, Closure $next)
  {
      $locale = $request->header('Accept-Language');
      
      // Chỉ chấp nhận vi hoặc en
      if (in_array($locale, ['vi', 'en'])) {
          App::setLocale($locale);
      }
      
      return $next($request);
  }
  ```
- **Đăng ký:** Thêm vào nhóm Middleware `api` trong `bootstrap/app.php`.

### Bước 3.3: Dịch Response Tùy Chỉnh (Custom Messages)
- Với các API trả về Success, ta chuyển hardcode sang hàm `__()`.
  - Thay vì: `return response()->json(['message' => 'Cập nhật thành công']);`
  - Đổi thành: `return response()->json(['message' => __('messages.update_success')]);`
- Tạo file `lang/vi/messages.php` và `lang/en/messages.php` để lưu trữ các thông báo này.
