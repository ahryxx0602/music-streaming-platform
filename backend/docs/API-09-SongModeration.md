# Tài liệu Đặc tả API & Kiến trúc (API Docs) - Kiểm duyệt Bài hát (Admin)

Tài liệu này định nghĩa các quy tắc nghiệp vụ và chi tiết API cho Phân hệ Kiểm duyệt Bài hát dành cho Admin (`SCR-ADM-04`). Đây là chốt chặn cuối cùng trước khi nhạc của Nghệ sĩ được đưa ra Public.

---

## 1. Quy tắc Nghiệp vụ (Business Rules)
1. **[RULE-MOD-01] Điều kiện hiển thị:** API Lấy danh sách chờ duyệt chỉ được phép trả về các bài hát thỏa mãn ĐỒNG THỜI 2 điều kiện: `status = 'Pending'` (chưa duyệt) VÀ `processing_status = 'completed'` (đã được FFmpeg xử lý xong, có link HLS để nghe).
2. **[RULE-MOD-02] Logic Approve (Duyệt):** Đổi `status` thành `'Approved'` và cập nhật thời gian `approved_at = now()`.
3. **[RULE-MOD-03] Logic Reject (Từ chối):** Đổi `status` thành `'Rejected'`, ghi nhận lý do vào cột `rejected_reason` và đánh dấu thời gian `rejected_at = now()`.
4. **[RULE-MOD-04] Notification:** (Ngoại vi) Tạm thời bỏ qua việc gửi Email thực tế, Backend chỉ cần lưu trạng thái vào CSDL. Nếu hệ thống có bảng notifications thì trigger, tạm thời chỉ focus vào update model `Song`.

---

## 2. Đặc tả API (AdminModerationController)

Khởi tạo Controller mới: `Modules\Music\Http\Controllers\AdminModerationController`

### 2.1. [GET] /api/v1/admin/moderation/songs
*   **Mô tả:** Lấy danh sách bài hát chờ duyệt.
*   **Tham số:** `page`, `filter[search]` (tìm theo tên bài hát).
*   **Logic:**
    *   Query: `Song::where('status', 'Pending')->where('processing_status', 'completed')`
    *   Sử dụng `with(['artist', 'genre'])` để load thông tin liên quan.
    *   Phân trang (paginate 15).

### 2.2. [GET] /api/v1/admin/moderation/songs/{id}
*   **Mô tả:** Lấy chi tiết bài hát để xem trên Popup Review.
*   **Logic:** `Song::with(['artist', 'genre'])->findOrFail($id)`. 

### 2.3. [PUT] /api/v1/admin/moderation/songs/{id}/approve
*   **Mô tả:** Chấp nhận bài hát.
*   **Logic:**
    *   Tìm `Song` theo ID. Đảm bảo nó đang ở trạng thái `Pending`.
    *   Update: `status = 'Approved'`, `approved_at = now()`.
    *   *(Tùy chọn)* Xóa Cache Redis cho danh sách nhạc mới nếu đã cài đặt Redis.

### 2.4. [PUT] /api/v1/admin/moderation/songs/{id}/reject
*   **Mô tả:** Từ chối bài hát.
*   **Request Body:** `reject_reason` (string, required, min:10).
*   **Logic:**
    *   Tìm `Song` theo ID.
    *   Update: `status = 'Rejected'`, `rejected_reason = $request->reject_reason`, `rejected_at = now()`.

---

## 3. Cập nhật Routes (`api.php`)
Trong nhóm routes `admin`, thêm:
```php
Route::prefix('moderation')->group(function () {
    Route::get('songs', [\Modules\Music\Http\Controllers\AdminModerationController::class, 'index']);
    Route::get('songs/{id}', [\Modules\Music\Http\Controllers\AdminModerationController::class, 'show']);
    Route::put('songs/{id}/approve', [\Modules\Music\Http\Controllers\AdminModerationController::class, 'approve']);
    Route::put('songs/{id}/reject', [\Modules\Music\Http\Controllers\AdminModerationController::class, 'reject']);
});
```
