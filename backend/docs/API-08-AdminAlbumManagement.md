# Tài liệu Đặc tả API & Kiến trúc (API Docs) - Quản lý Album (Admin)

Tài liệu này định nghĩa các quy tắc nghiệp vụ và chi tiết API cho Phân hệ Quản lý Album dành cho Admin (`SCR-ADM-10`). 

---

## 1. Quy tắc Nghiệp vụ (Business Rules)
1. **[RULE-ALB-01] Mối quan hệ 1-N:** Một Album chứa nhiều Bài hát. Tuy nhiên, một Bài hát **chỉ được phép thuộc về TỐI ĐA 1 Album**. (Do cột `album_id` trong bảng `songs` là Foreign Key).
2. **[RULE-ALB-02] Xóa Mềm & Gỡ Nhạc:** Khi Xóa một Album, KHÔNG được phép xóa các bài hát bên trong nó. Backend phải tự động update `album_id = null` cho toàn bộ bài hát thuộc Album đó (đưa chúng về trạng thái bài hát lẻ - Unassigned).
3. **[RULE-ALB-03] Auto-Approved:** Giống với Bài hát, Album do Admin tạo ra sẽ được auto set `status = 'Approved'` thay vì `Draft` hay `Pending`.

---

## 2. Thiết kế Cơ sở dữ liệu (Database)
Các bảng liên quan (Đã có sẵn Migration, KHÔNG cần tạo mới):
*   `albums`: `id`, `artist_id`, `title`, `cover_image`, `release_date`, `type` (Single, EP, Album), `status`, `description`.
*   `songs`: Chứa khóa ngoại `album_id` (nullable).

---

## 3. Đặc tả API (AdminAlbumController & AdminSongController)

### 3.1. [GET] /api/v1/admin/albums
*   **Mô tả:** Lấy danh sách Album có phân trang (dùng cho Tab Album).
*   **Tham số:** `page`, `filter[search]` (theo title), `filter[artist_id]`.
*   **Yêu cầu xử lý:** 
    *   Sử dụng `with('artist')` để tránh N+1 Query.
    *   Sử dụng `withCount('songs')` để trả về số lượng bài hát trong mỗi Album.

### 3.2. [POST] /api/v1/admin/albums
*   **Mô tả:** Tạo Album mới và (tùy chọn) gán luôn danh sách bài hát vào.
*   **Content-Type:** `multipart/form-data` (Để upload ảnh bìa `cover_image`).
*   **Request Body:**
    *   `title` (string, required, max:255)
    *   `artist_id` (integer, required, exists:artist_profiles,id)
    *   `release_date` (date, nullable)
    *   `type` (enum: Single, EP, Album, mặc định: Album)
    *   `description` (string, nullable)
    *   `cover_image` (file image, nullable)
    *   `song_ids` (array, nullable) - Ví dụ: `song_ids[]=1&song_ids[]=2`
*   **Logic:** Xử lý upload ảnh vào Storage (S3/local). Lưu Album. Nếu có mảng `song_ids`, gọi lệnh `Song::whereIn('id', $song_ids)->update(['album_id' => $album->id])`.

### 3.3. [PUT] /api/v1/admin/albums/{id}
*   **Mô tả:** Cập nhật Album.
*   **Request Body:** Tương tự POST (Nhưng không bắt buộc các trường). Do có thể có upload file mới, chú ý method spoofing `_method=PUT` nếu gửi form-data.
*   **Logic gán nhạc:** Nếu truyền mảng `song_ids`:
    1. Gỡ các bài cũ: `Song::where('album_id', $album->id)->update(['album_id' => null])`.
    2. Gán các bài mới: `Song::whereIn('id', $song_ids)->update(['album_id' => $album->id])`.

### 3.4. [DELETE] /api/v1/admin/albums/{id}
*   **Mô tả:** Xóa mềm Album.
*   **Logic:** Trước khi `$album->delete()`, phải gỡ hết nhạc: `Song::where('album_id', $album->id)->update(['album_id' => null])`.

### 3.5. [GET] /api/v1/admin/songs/unassigned
*(Viết trong `AdminSongController`)*
*   **Mô tả:** Lấy danh sách các bài hát lẻ (chưa thuộc Album nào) của 1 nghệ sĩ cụ thể.
*   **Tham số:** `artist_id` (required).
*   **Logic:** `Song::where('artist_id', $request->artist_id)->whereNull('album_id')->get()` (Trả về toàn bộ, không cần phân trang để FE đổ vào Dual Listbox kéo thả).
