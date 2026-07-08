# [SCR-LST-05] Thư viện cá nhân (My Library)

> **Mô tả ngắn:** Không gian lưu trữ cá nhân của mỗi Khán giả. Nơi chứa các bài hát họ đã thả tim (Favorites), danh sách Album/Playlist họ đã lưu lại và những Nghệ sĩ mà họ đang theo dõi (Following). 

## 1. Thông tin chung (Meta)
*   **Module:** Listener / Personalization
*   **Route / URL:** `/library`
*   **Layout sử dụng:** `MainLayout.vue`
*   **Quyền truy cập:** `[PER-AUTH]` (Khách - Guest nếu truy cập sẽ bị yêu cầu Đăng nhập).
*   **Component con (Children):**
    *   `LibraryTabs.vue` (Điều hướng giữa các mục: Bài hát, Album, Playlist, Nghệ sĩ).
    *   `FavoriteTracksList.vue` (Danh sách bài hát yêu thích).
    *   `FollowedArtistsGrid.vue` (Lưới Avatar các nghệ sĩ đang follow).

## 2. Thành phần giao diện (UI Elements)
*   **Khu vực Điều hướng (Tabs):**
    *   Mặc định mở vào Tab **"Bài hát yêu thích" (Liked Songs)**. Giao diện giống chi tiết Playlist nhưng không có nút Edit, chỉ có nút "Phát tất cả" (Play All) hoặc Trộn bài (Shuffle).
*   **Tab Nghệ Sĩ (Artists):**
    *   Hiển thị danh sách Avatar hình tròn của các nghệ sĩ mà người dùng đã bấm Follow. Có thanh tìm kiếm nội bộ để gõ tìm nhanh tên nghệ sĩ trong thư viện của mình.
*   **Tab Lịch sử nghe (Listening History):**
    *   (Tùy chọn) Hiển thị dạng Timeline các bài hát vừa nghe trong 7 ngày gần nhất. Rất hữu ích khi người dùng nghe thấy bài hay nhưng quên chưa thả tim.

## 3. Liên kết dữ liệu & Logic (State & APIs)

### Tương tác API (Network)
*   **Lấy dữ liệu (Fetch):**
    *   `[API-120]` - `GET /api/v1/library/songs` (Lấy danh sách Bài hát thả tim).
    *   `[API-121]` - `GET /api/v1/library/artists` (Lấy danh sách Nghệ sĩ đang follow).
    *   `[API-125]` - `GET /api/v1/library/history` (Lịch sử nghe).

## 4. Quy tắc nghiệp vụ (Business Rules)
*   **[RULE-LIB-01] - Cấu trúc dữ liệu "Thả tim":** Khi bấm thả tim ở `[SCR-LST-04] Global Player`, Backend lưu vào bảng `[DB-favorite_songs]`. Màn hình này sẽ fetch ra và sắp xếp mặc định theo `created_at DESC` (Bài nào mới thả tim thì nằm trên cùng).
*   **[RULE-LIB-02] - Xử lý tính vẹn toàn (Data Integrity):** Nếu bài hát bị Admin ẩn (`status = hidden`) hoặc Nghệ sĩ bị khóa, bài hát đó vẫn nằm trong CSDL Favorites nhưng khi load lên Frontend sẽ hiển thị trạng thái làm mờ (Grayed out) kèm thông báo "Bài hát hiện không khả dụng" và không cho phép bấm Play. Không được phép tự động xóa nó khỏi danh sách của User.

## 5. Tác động Cơ sở dữ liệu (Database Impact)
*   **Đọc (Read):** Bảng `[DB-favorite_songs]`, `[DB-user_follows]`, `[DB-streams]`.
