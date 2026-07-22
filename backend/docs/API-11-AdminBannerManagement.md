# Tài liệu Đặc tả API & Kiến trúc (API Docs) - Quản lý Banner & Khám phá (Admin)

Tài liệu này định nghĩa các API cho Phân hệ Quản lý Banner (Dành cho Admin) (`SCR-ADM-05`).

---

## 1. Quy tắc Nghiệp vụ (Business Rules)
1. **[RULE-ADM-BNR-02] Xóa Cache Redis:** Bất kỳ thao tác Ghi nào (Tạo, Sửa, Re-order, Xóa) cũng phải gọi lệnh xóa Cache để Trang chủ cập nhật lập tức.
   - Hàm mẫu: `\Illuminate\Support\Facades\Cache::forget('explore_banners');` (Hoặc Redis facade).
2. **[RULE-ADM-BNR-03] Limit hiển thị:** Ở hàm API dành cho Client (Public), chỉ query ra tối đa 5 Banner có `is_active = 1`, sắp xếp `order ASC`. Tuy nhiên, đây là API cho Admin nên Admin sẽ lấy Toàn bộ (Kể cả cái tắt) để quản lý.

---

## 2. Đặc tả API (AdminBannerController)

Khởi tạo Controller mới: `Modules\Administration\Http\Controllers\AdminBannerController`

### 2.1. [GET] /api/v1/admin/banners
*   **Mô tả:** Lấy danh sách banner.
*   **Logic:**
    *   Query: `Banner::orderBy('order', 'asc')->get()`. (Không phân trang vì danh sách banner thường rất ít, cần lấy hết để làm giao diện Kéo thả mượt mà).

### 2.2. [POST] /api/v1/admin/banners
*   **Mô tả:** Tạo Banner mới (Hỗ trợ upload file ảnh).
*   **Request Body (`multipart/form-data`):**
    *   `title` (string, required)
    *   `image` (file, required, mimes:jpeg,png,jpg,webp)
    *   `target_url` (string, nullable)
    *   `is_active` (boolean, default 1)
*   **Logic:**
    *   Upload ảnh lên S3 hoặc Local Storage, lấy URL.
    *   Tính toán cột `order`: `$maxOrder = Banner::max('order'); newOrder = $maxOrder + 1;`
    *   Insert vào DB bảng `banners`.
    *   Xóa Cache: `Cache::forget('explore_banners')`.

### 2.3. [POST] /api/v1/admin/banners/{id}
*   **Mô tả:** Cập nhật thông tin / Hình ảnh của Banner. *(Dùng POST kèm `_method=PUT` nếu có upload file).*
*   **Request Body (`multipart/form-data`):**
    *   `title` (string, optional)
    *   `image` (file, optional, mimes:jpeg,png,jpg,webp)
    *   `target_url` (string, optional)
    *   `is_active` (boolean, optional)
*   **Logic:**
    *   Tìm Banner. Nếu có truyền file `image`, upload đè file mới và xóa file cũ trên S3.
    *   Cập nhật DB.
    *   Xóa Cache: `Cache::forget('explore_banners')`.

### 2.4. [PUT] /api/v1/admin/banners/reorder
*   **Mô tả:** Lưu thứ tự vị trí các Banner sau khi Admin kéo thả.
*   **Request Body:** `banner_ids` (Mảng ID đã được sắp xếp).
*   **Logic:**
    *   Lặp qua mảng `banner_ids`, cập nhật cột `order` = index của mảng cho từng Banner. (Nên bọc trong `DB::transaction`).
    *   Xóa Cache.

### 2.5. [DELETE] /api/v1/admin/banners/{id}
*   **Mô tả:** Xóa cứng Banner (Xóa luôn file vật lý).
*   **Logic:** Xóa file trên Storage -> Xóa DB -> Xóa Cache.

---

## 3. Cập nhật Routes (`api.php`)
Trong nhóm routes `admin` (`Modules/Administration/routes/api.php`):
```php
Route::prefix('banners')->group(function () {
    Route::get('/', [\Modules\Administration\Http\Controllers\AdminBannerController::class, 'index']);
    Route::post('/', [\Modules\Administration\Http\Controllers\AdminBannerController::class, 'store']);
    Route::put('/reorder', [\Modules\Administration\Http\Controllers\AdminBannerController::class, 'reorder']);
    Route::post('/{id}', [\Modules\Administration\Http\Controllers\AdminBannerController::class, 'update']);
    Route::delete('/{id}', [\Modules\Administration\Http\Controllers\AdminBannerController::class, 'destroy']);
});
```
