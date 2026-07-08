# Nhóm Tính năng: Quản lý Kho dữ liệu & Cấu hình (Inventory & Settings)

**Mô tả:** Siêu cụm API (24 APIs) điều khiển toàn bộ xương sống của ứng dụng. Bao gồm thao tác trực tiếp vào Data Bài hát/Album (Inventory), quản lý Danh mục (Genres, Banners, System Playlists) và Cấu hình hệ thống (Settings).

---

## 💻 Chi tiết các APIs - Inventory (Kho Nhạc Khổng Lồ)

**[API-360] GET `/admin/inventory/songs`**
- Lấy toàn bộ bài hát trên DB (Bao gồm mọi trạng thái, mọi nghệ sĩ). Dùng để tra cứu nhanh.
**[API-361] POST `/admin/inventory/songs`**
- Admin trực tiếp Upload bài hát (Không qua luồng Artist). Bài hát sẽ tự động Approved.
**[API-362] PUT `/admin/inventory/songs/{id}`**
- Chỉnh sửa ép buộc metadata của bất kỳ bài hát nào.
**[API-363] DELETE `/admin/inventory/songs/{id}`**
- Ẩn bài hát vì lý do bản quyền (Soft Delete toàn cục).
**[API-364] GET `/admin/inventory/albums`**
- Tra cứu danh sách toàn bộ Album.
**[API-365] POST `/admin/inventory/albums`**
- Admin tự tay khởi tạo Album.
**[API-366] PUT `/admin/inventory/albums/{id}`**
- Chỉnh sửa Album bất kỳ.
**[API-367] GET `/admin/search/artists`**
- Gợi ý Autocomplete khi Admin gõ tên Artist (Để gán vào bài hát).
**[API-368] GET `/admin/search/songs`**
- Gợi ý Autocomplete bài hát.

---

## 💻 Chi tiết các APIs - Banners & Genres

**[API-380] GET `/admin/banners`**
- Lấy danh sách Banner trang chủ.
**[API-381] POST `/admin/banners`**
- Tạo banner mới (Upload ảnh).
**[API-382] PUT `/admin/banners/{id}`**
- Bật/Tắt (Active) hoặc sửa thông tin banner.
**[API-383] PUT `/admin/banners/reorder`**
- Kéo thả thứ tự hiển thị banner. Nhận mảng `[id1, id2, ...]`.

**[API-370] GET `/admin/genres`**
- Lấy danh sách Thể loại (Tree).
**[API-371] POST `/admin/genres`**
- Tạo thể loại mới.
**[API-372] PUT `/admin/genres/{id}`**
- Sửa tên, đổi Parent ID.

---

## 💻 Chi tiết các APIs - System Playlists

*(System Playlists là các Playlist chung xuất hiện ở Trang chủ như "Top 100", "Nhạc Lofi Chill", không thuộc về cá nhân nào).*

**[API-390] GET `/admin/playlists`**
- DS Playlist hệ thống.
**[API-391] POST `/admin/playlists`**
- Tạo Playlist rỗng.
**[API-392] PUT `/admin/playlists/{id}`**
- Sửa tên, đổi cover.
**[API-393] DELETE `/admin/playlists/{id}`**
- Xóa Playlist hệ thống.
**[API-394] POST `/admin/playlists/{id}/songs`**
- Thêm bài vào Playlist.
**[API-395] DELETE `/admin/playlists/{id}/songs`**
- Gỡ bài khỏi Playlist.

---

## 💻 Chi tiết các APIs - System Settings (Cấu hình Máy chủ)

**[API-430] GET `/admin/settings`**
- Tính năng: Lấy bảng cấu hình động (Ví dụ: Tỉ lệ chia tiền bản quyền, Dung lượng Upload tối đa, Bật/Tắt Anti-cheat). Trả về dạng JSON Key-Value.

**[API-431] PUT `/admin/settings/{key}`**
- Tính năng: Cập nhật một tham số cấu hình nóng mà không cần Deploy lại Code.
- Body: `{ "value": "20MB" }`
