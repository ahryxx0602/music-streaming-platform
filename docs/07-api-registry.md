# API REGISTRY (SINGLE SOURCE OF TRUTH)

**Dự án:** Nền tảng Âm nhạc Trực tuyến (Audio Streaming Web App)
**Phiên bản:** 1.0
**Cập nhật:** 2026-07-08

---

## 1. Giới thiệu

Tài liệu này là **nguồn sự thật duy nhất (Single Source of Truth)** cho toàn bộ API ID trong hệ thống. Mọi tài liệu khác (Screen Specs, API Documentation, Architecture) phải tham chiếu đến file này.

### Quy ước đặt tên

- **Format ID:** `[API-<STT>]` — đánh số 3 chữ số, phân vùng theo Role:
  - `001–099`: Guest APIs (không cần auth)
  - `100–199`: Listener APIs (role: Listener)
  - `200–299`: Artist APIs (role: Artist)
  - `300–499`: Admin APIs (role: Admin)
- **Base URL:** `/api/v1`
- **Authentication:** Laravel Sanctum SPA (HttpOnly Cookie)
- **Response Format:** JSend (`status`, `message`, `data`)
- **Pagination:** `?page=1&per_page=20`

---

## 2. Common & Guest APIs

### 2.1 Common Protected APIs (Yêu cầu Auth, dùng chung mọi Role)

Prefix: `/api/v1/auth`

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-050] | GET | `/auth/me` | Lấy thông tin tài khoản hiện tại (Eager load Profile theo Role) | — | Read: [DB-users], [DB-artist_profiles] |
| [API-051] | POST | `/auth/logout` | Đăng xuất an toàn | — | Delete: Session |

### 2.2 Guest APIs (Không yêu cầu Auth)

Prefix: `/api/v1/guest`

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-001] | GET | `/sanctum/csrf-cookie` | Lấy CSRF Token (Sanctum) | [SCR-SHR-01] | — |
| [API-002] | POST | `/guest/auth/login` | Đăng nhập Email/Password | [SCR-SHR-01] | Read: [DB-users] |
| [API-003] | POST | `/guest/auth/register` | Đăng ký tài khoản Listener | [SCR-LST-01] | Write: [DB-users] |
| [API-004] | POST | `/guest/auth/forgot-password` | Gửi email reset link | [SCR-SHR-02] | Write: [DB-password_reset_tokens] |
| [API-005] | POST | `/guest/auth/reset-password` | Đặt mật khẩu mới (token + password) | [SCR-SHR-02] | Write: [DB-users], Delete: [DB-password_reset_tokens] |
| [API-006] | GET | `/guest/auth/email/verify/{id}/{hash}` | Xác thực email (Signed URL) | [SCR-SHR-03] | Write: [DB-users].email_verified_at |
| [API-007] | GET | `/guest/auth/artist-register/validate` | Validate invitation token | [SCR-ART-01] | Read: [DB-artist_invitations] |
| [API-008] | POST | `/guest/auth/artist-register` | Đăng ký tài khoản Artist (Invite-Only) | [SCR-ART-01] | Write: [DB-users], [DB-artist_profiles], [DB-artist_invitations] |
| [API-009] | GET | `/guest/auth/redirect/{provider}` | OAuth2 redirect (Google/Facebook) | [SCR-SHR-01], [SCR-LST-01] | — |
| [API-010] | GET | `/guest/auth/callback/{provider}` | OAuth2 callback handler | [SCR-SHR-01], [SCR-LST-01] | Write: [DB-users] |
| [API-011] | GET | `/guest/explore/banners` | Banner trang chủ (Cached) | [SCR-LST-02] | Read: [DB-banners] |
| [API-012] | GET | `/guest/explore/trending` | Bài hát thịnh hành (top 10, 7 ngày) | [SCR-LST-02] | Read: Redis Cache ← [DB-streams] |
| [API-013] | GET | `/guest/explore/new-releases` | Nhạc mới phát hành | [SCR-LST-02] | Read: [DB-songs], [DB-albums] |
| [API-014] | GET | `/guest/genres` | Danh sách thể loại (Cached 24h) | [SCR-LST-06] | Read: [DB-genres] |
| [API-015] | GET | `/guest/search` | Tìm kiếm tổng hợp (Song, Artist, Album, Playlist) | [SCR-LST-06] | Read: [DB-songs], [DB-users], [DB-albums], [DB-playlists] |
| [API-016] | GET | `/guest/songs/{id}` | Chi tiết bài hát (metadata) | [SCR-LST-03] | Read: [DB-songs] |
| [API-017] | GET | `/guest/songs/{id}/preview` | Preview 30s (Signed URL, không cần auth) | [SCR-LST-04] | Read: MinIO |
| [API-018] | GET | `/guest/albums/{id}` | Chi tiết Album (songs list, metadata) | [SCR-LST-03] | Read: [DB-albums], [DB-songs] |
| [API-019] | GET | `/guest/artists/{id}` | Artist Profile public (bio, stats) | [SCR-LST-07] | Read: [DB-artist_profiles], [DB-users] |
| [API-020] | GET | `/guest/artists/{id}/top-tracks` | Top tracks của artist | [SCR-LST-07] | Read: [DB-songs], [DB-streams] |
| [API-021] | GET | `/guest/artists/{id}/albums` | Albums của artist | [SCR-LST-07] | Read: [DB-albums] |
| [API-022] | GET | `/guest/playlists/{id}` | Chi tiết Playlist public | [SCR-LST-03] | Read: [DB-playlists], [DB-playlist_songs] |
| [API-023] | GET | `/guest/explore/system-playlists` | Lấy danh sách System Playlists | [SCR-LST-02] | Read: [DB-playlists] |
| [API-024] | GET | `/guest/search/autocomplete` | Live search (Gợi ý nhanh) | [SCR-LST-06] | Read: Redis Cache |

---

## 3. Listener APIs (Role: Listener)

Prefix: `/api/v1/listener` — Yêu cầu HttpOnly Cookie hợp lệ.

### 3.1 Authentication & Profile

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-103] | POST | `/listener/auth/email/resend` | Gửi lại email xác thực | [SCR-SHR-03] | — |
| [API-104] | PUT | `/listener/auth/password` | Đổi mật khẩu | [SCR-SHR-04] | Write: [DB-users].password |
| [API-105] | GET | `/listener/auth/sessions` | DS sessions đang hoạt động | [SCR-SHR-04] | Read: Sessions |
| [API-106] | DELETE | `/listener/auth/sessions/{id}` | Revoke session | [SCR-SHR-04] | Delete: Session |
| [API-107] | PUT | `/listener/profile` | Cập nhật profile (name, settings) | [SCR-SHR-04] | Write: [DB-users] |
| [API-109] | POST | `/listener/profile/avatar` | Cập nhật Avatar (multipart) | [SCR-SHR-04] | Write: MinIO, [DB-users].avatar |

### 3.2 Streaming

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-108] | GET | `/listener/explore/recommendations` | Gợi ý cá nhân hóa | [SCR-LST-02] | Read: [DB-streams], [DB-songs] |
| [API-110] | GET | `/listener/stream/url/{id}` | Sinh Signed URL nghe nhạc (HLS m3u8) | [SCR-LST-04] | Read: MinIO |
| [API-111] | POST | `/listener/stream/track/{id}` | Ghi nhận lượt nghe hợp lệ (Anti-cheat) | [SCR-LST-04] | Write: [DB-streams], Update: [DB-songs].play_count |

### 3.3 Library & Favorites

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-120] | GET | `/listener/library/favorites/songs` | DS bài hát yêu thích (paginated) | [SCR-LST-05] | Read: [DB-favorite_songs] |
| [API-121] | GET | `/listener/library/favorites/albums` | DS album yêu thích | [SCR-LST-05] | Read: [DB-favorite_albums] |
| [API-122] | GET | `/listener/library/favorites/artists` | DS artist yêu thích | [SCR-LST-05] | Read: [DB-favorite_artists] |
| [API-123] | GET | `/listener/library/favorites/playlists` | DS playlist yêu thích | [SCR-LST-05] | Read: [DB-favorite_playlists] |
| [API-124] | POST | `/listener/library/toggle-favorite` | Toggle favorite (type + id) | [SCR-LST-03], [SCR-LST-04] | Write: [DB-favorite_*] |
| [API-125] | GET | `/listener/library/history` | Lịch sử nghe nhạc (Resume listening) | [SCR-LST-05] | Read: [DB-listening_histories] |
| [API-126] | DELETE | `/listener/library/history/{id}` | Xóa một mục lịch sử | [SCR-LST-05] | Delete: [DB-listening_histories] |
| [API-127] | DELETE | `/listener/library/history` | Xóa toàn bộ lịch sử | [SCR-LST-05] | Delete: [DB-listening_histories] |
| [API-128] | PUT | `/listener/library/history/sync` | Đồng bộ vị trí (Resume playback) | [SCR-LST-04] | Write: [DB-listening_histories].last_position |

### 3.4 Follow

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-130] | POST | `/listener/artists/{id}/follow` | Toggle Follow/Unfollow artist | [SCR-LST-07] | Write: [DB-follows] |
| [API-131] | GET | `/listener/following` | DS artist đang follow | [SCR-LST-05] | Read: [DB-follows] |

### 3.5 Playlist Management

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-140] | GET | `/listener/playlists` | DS playlist cá nhân | [SCR-LST-05] | Read: [DB-playlists] |
| [API-141] | POST | `/listener/playlists` | Tạo playlist mới | [SCR-LST-05] | Write: [DB-playlists] |
| [API-142] | GET | `/listener/playlists/{id}` | Chi tiết playlist | [SCR-LST-03] | Read: [DB-playlists], [DB-playlist_songs] |
| [API-143] | PUT | `/listener/playlists/{id}` | Cập nhật playlist (title, privacy, cover) | — | Write: [DB-playlists] |
| [API-144] | DELETE | `/listener/playlists/{id}` | Xóa playlist (soft delete) | — | Write: [DB-playlists].deleted_at |
| [API-145] | POST | `/listener/playlists/{id}/songs` | Thêm bài vào playlist | [SCR-LST-03] | Write: [DB-playlist_songs] |
| [API-146] | DELETE | `/listener/playlists/{id}/songs` | Xóa bài khỏi playlist | [SCR-LST-03] | Delete: [DB-playlist_songs] |
| [API-147] | PUT | `/listener/playlists/{id}/reorder` | Sắp xếp lại vị trí bài hát | — | Write: [DB-playlist_songs].position |

### 3.6 Comments

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-150] | GET | `/listener/songs/{id}/comments` | DS comments (paginated, threaded) | [SCR-LST-03] | Read: [DB-comments] |
| [API-151] | POST | `/listener/songs/{id}/comments` | Tạo comment/reply | [SCR-LST-03] | Write: [DB-comments] |
| [API-152] | DELETE | `/listener/comments/{id}` | Xóa comment của chính mình | — | Write: [DB-comments].deleted_at |

### 3.7 Notifications

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-160] | GET | `/listener/notifications` | DS thông báo (paginated) | — | Read: [DB-notifications] |
| [API-161] | PUT | `/listener/notifications/{id}/read` | Đánh dấu đã đọc | — | Write: [DB-notifications].read_at |
| [API-162] | PUT | `/listener/notifications/read-all` | Đánh dấu tất cả đã đọc | — | Write: [DB-notifications].read_at |

### 3.8 Search History

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-170] | GET | `/listener/search/recent` | Lịch sử tìm kiếm (max 20) | [SCR-LST-06] | Read: [DB-recent_searches] |
| [API-171] | DELETE | `/listener/search/recent/{id}` | Xóa một từ khóa | [SCR-LST-06] | Delete: [DB-recent_searches] |
| [API-172] | DELETE | `/listener/search/recent` | Xóa toàn bộ lịch sử tìm | [SCR-LST-06] | Delete: [DB-recent_searches] |

---

## 4. Artist APIs (Role: Artist)

Prefix: `/api/v1/artist` — Yêu cầu HttpOnly Cookie + Role = `artist`.

### 4.1 Authentication

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-203] | PUT | `/artist/auth/password` | Đổi mật khẩu | [SCR-SHR-04] | Write: [DB-users].password |

### 4.2 Profile Management

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-210] | GET | `/artist/profile` | Lấy profile hiện tại | [SCR-ART-05] | Read: [DB-artist_profiles] |
| [API-211] | PUT | `/artist/profile` | Cập nhật profile (stage_name, bio, social links) | [SCR-ART-05] | Write: [DB-artist_profiles] |
| [API-212] | POST | `/artist/profile/avatar` | Upload avatar | [SCR-ART-05] | Write: [DB-users].avatar, MinIO |
| [API-213] | POST | `/artist/profile/cover` | Upload cover image | [SCR-ART-05] | Write: [DB-artist_profiles].cover_image, MinIO |

### 4.3 Song Management

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-220] | GET | `/artist/songs` | DS bài hát của artist | [SCR-ART-03] | Read: [DB-songs] |
| [API-221] | POST | `/artist/songs` | Upload bài hát mới (multipart/form-data) | [SCR-ART-03] | Write: [DB-songs], MinIO, Queue: media_processing |
| [API-222] | PUT | `/artist/songs/{id}` | Cập nhật metadata bài hát | [SCR-ART-03] | Write: [DB-songs] |
| [API-223] | DELETE | `/artist/songs/{id}` | Xóa mềm bài hát | [SCR-ART-03] | Write: [DB-songs].deleted_at |
| [API-224] | GET | `/artist/songs/{id}/status` | Trạng thái processing FFmpeg | [SCR-ART-03] | Read: [DB-songs].status |
| [API-225] | POST | `/artist/songs/{id}/retry` | Retry bài hát Failed | [SCR-ART-03] | Write: [DB-songs], Queue: media_processing |
| [API-226] | GET | `/artist/songs/unassigned` | Bài hát chưa thuộc album | [SCR-ART-04] | Read: [DB-songs] WHERE album_id IS NULL |

### 4.4 Album Management

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-230] | GET | `/artist/albums` | DS album | [SCR-ART-04] | Read: [DB-albums] |
| [API-231] | POST | `/artist/albums` | Tạo album mới | [SCR-ART-04] | Write: [DB-albums], Update: [DB-songs].album_id |
| [API-232] | PUT | `/artist/albums/{id}` | Cập nhật album | [SCR-ART-04] | Write: [DB-albums] |
| [API-233] | DELETE | `/artist/albums/{id}` | Xóa album (soft delete) | [SCR-ART-04] | Write: [DB-albums].deleted_at |

### 4.5 Analytics & Notifications

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-240] | GET | `/artist/analytics` | Dashboard thống kê (streams, followers, charts) | [SCR-ART-02] | Read: Redis Cache |
| [API-250] | GET | `/artist/notifications` | DS thông báo | — | Read: [DB-notifications] |
| [API-251] | PUT | `/artist/notifications/{id}/read` | Đánh dấu đã đọc | — | Write: [DB-notifications].read_at |
| [API-252] | PUT | `/artist/notifications/read-all` | Đánh dấu tất cả đã đọc | — | Write: [DB-notifications].read_at |

### 4.6 Utilities

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-260] | GET | `/artist/genres/tree` | Cây thể loại cho dropdown upload | [SCR-ART-03] | Read: [DB-genres] |

---

## 5. Admin APIs (Role: Admin)

Prefix: `/api/v1/admin` — Phân quyền chi tiết qua Spatie RBAC.

### 5.1 Authentication

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-301] | POST | `/admin/auth/login` | Đăng nhập Admin | — | Read: [DB-users] |
| [API-303] | PUT | `/admin/auth/password` | Đổi mật khẩu | [SCR-SHR-04] | Write: [DB-users].password |

### 5.2 Dashboard & Analytics

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-310] | GET | `/admin/dashboard` | Thống kê tổng quan (widgets) | [SCR-ADM-09] | Read: Redis Cache |
| [API-311] | GET | `/admin/analytics/streams` | Biểu đồ streams theo ngày | [SCR-ADM-09] | Read: Redis Cache |
| [API-312] | GET | `/admin/analytics/top-songs` | Top songs | [SCR-ADM-09] | Read: Redis Cache |
| [API-313] | GET | `/admin/analytics/user-growth` | User growth chart | [SCR-ADM-09] | Read: Redis Cache |
| [API-314] | GET | `/admin/analytics/artists/{id}` | Chi tiết analytics một artist | [SCR-ADM-09] | Read: Redis Cache |

### 5.3 User Management

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-320] | GET | `/admin/users` | DS users (filter: role, search, status) | [SCR-ADM-03] | Read: [DB-users] |
| [API-321] | POST | `/admin/users/artist` | Tạo nhanh tài khoản Artist | [SCR-ADM-03] | Write: [DB-users], [DB-artist_profiles] |
| [API-322] | PUT | `/admin/users/{id}/status` | Ban / Unban / Suspend user | [SCR-ADM-03] | Write: [DB-users].status |
| [API-323] | POST | `/admin/users/{id}/reset-password` | Gửi email reset cho user | [SCR-ADM-03] | Write: [DB-password_reset_tokens] |
| [API-324] | DELETE | `/admin/users/{id}` | Xóa staff account | [SCR-ADM-03] | Delete: [DB-users] |
| [API-325] | PUT | `/admin/users/{id}/roles` | Gán role cho admin user | [SCR-ADM-03] | Write: [DB-model_has_roles] |

### 5.4 Artist Management

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-330] | GET | `/admin/artists` | DS toàn bộ artist | [SCR-ADM-03] | Read: [DB-users], [DB-artist_profiles] |
| [API-331] | PUT | `/admin/artists/{id}` | Chỉnh sửa thông tin artist | [SCR-ADM-03] | Write: [DB-artist_profiles] |
| [API-332] | POST | `/admin/artists/{id}/verify` | Xác minh artist (tích xanh) | [SCR-ADM-03] | Write: [DB-artist_profiles].is_verified |

### 5.5 Artist Invitations

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-340] | GET | `/admin/artist-invitations` | DS invitations | [SCR-ADM-02] | Read: [DB-artist_invitations] |
| [API-341] | POST | `/admin/artist-invitations` | Tạo invitation mới + gửi email | [SCR-ADM-02] | Write: [DB-artist_invitations], Queue: email |
| [API-342] | PUT | `/admin/artist-invitations/{id}/revoke` | Thu hồi invitation | [SCR-ADM-02] | Write: [DB-artist_invitations].expires_at |

### 5.6 Song Moderation

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-350] | GET | `/admin/moderation/songs` | DS bài hát chờ duyệt (status=Pending) | [SCR-ADM-04] | Read: [DB-songs] |
| [API-351] | GET | `/admin/moderation/songs/{id}` | Chi tiết bài hát để review | [SCR-ADM-04] | Read: [DB-songs] |
| [API-352] | PUT | `/admin/moderation/songs/{id}/approve` | Phê duyệt bài hát | [SCR-ADM-04] | Write: [DB-songs].status, .approved_at |
| [API-353] | PUT | `/admin/moderation/songs/{id}/reject` | Từ chối (payload: rejected_reason) | [SCR-ADM-04] | Write: [DB-songs].status, .rejected_reason, .rejected_at |

### 5.7 Song & Album Inventory

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-360] | GET | `/admin/inventory/songs` | DS tổng bài hát (filter nâng cao) | [SCR-ADM-10] | Read: [DB-songs] |
| [API-361] | POST | `/admin/inventory/songs` | Admin upload nhạc (auto-approved) | [SCR-ADM-10] | Write: [DB-songs], MinIO, Queue |
| [API-362] | PUT | `/admin/inventory/songs/{id}` | Sửa metadata bài hát | [SCR-ADM-10] | Write: [DB-songs] |
| [API-363] | DELETE | `/admin/inventory/songs/{id}` | Soft delete / ẩn bài hát | [SCR-ADM-10] | Write: [DB-songs].deleted_at |
| [API-364] | GET | `/admin/inventory/albums` | DS album | [SCR-ADM-10] | Read: [DB-albums] |
| [API-365] | POST | `/admin/inventory/albums` | Tạo album | [SCR-ADM-10] | Write: [DB-albums] |
| [API-366] | PUT | `/admin/inventory/albums/{id}` | Sửa album | [SCR-ADM-10] | Write: [DB-albums] |
| [API-367] | GET | `/admin/search/artists` | Autocomplete tìm artist | [SCR-ADM-10] | Read: [DB-users] |
| [API-368] | GET | `/admin/search/songs` | Autocomplete tìm songs | [SCR-ADM-11] | Read: [DB-songs] |

### 5.8 Genre Management

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-370] | GET | `/admin/genres` | DS thể loại (tree) | [SCR-ADM-07] | Read: [DB-genres] |
| [API-371] | POST | `/admin/genres` | Tạo thể loại | [SCR-ADM-07] | Write: [DB-genres] |
| [API-372] | PUT | `/admin/genres/{id}` | Cập nhật thể loại | [SCR-ADM-07] | Write: [DB-genres] |

### 5.9 Banner Management

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-380] | GET | `/admin/banners` | DS banners | [SCR-ADM-05] | Read: [DB-banners] |
| [API-381] | POST | `/admin/banners` | Tạo banner (multipart) | [SCR-ADM-05] | Write: [DB-banners], MinIO |
| [API-382] | PUT | `/admin/banners/{id}` | Cập nhật banner | [SCR-ADM-05] | Write: [DB-banners] |
| [API-383] | PUT | `/admin/banners/reorder` | Sắp xếp lại thứ tự | [SCR-ADM-05] | Write: [DB-banners].order |

### 5.10 System Playlists

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-390] | GET | `/admin/playlists` | DS system playlists | [SCR-ADM-11] | Read: [DB-playlists] WHERE type='system' |
| [API-391] | POST | `/admin/playlists` | Tạo system playlist | [SCR-ADM-11] | Write: [DB-playlists] |
| [API-392] | PUT | `/admin/playlists/{id}` | Cập nhật playlist | [SCR-ADM-11] | Write: [DB-playlists], [DB-playlist_songs] |
| [API-393] | DELETE | `/admin/playlists/{id}` | Xóa playlist (soft) | [SCR-ADM-11] | Write: [DB-playlists].deleted_at |
| [API-394] | POST | `/admin/playlists/{id}/songs` | Thêm bài vào playlist | [SCR-ADM-11] | Write: [DB-playlist_songs] |
| [API-395] | DELETE | `/admin/playlists/{id}/songs` | Xóa bài khỏi playlist | [SCR-ADM-11] | Delete: [DB-playlist_songs] |

### 5.11 RBAC (Roles & Permissions)

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-400] | GET | `/admin/roles` | DS roles | [SCR-ADM-06] | Read: roles (Spatie) |
| [API-401] | POST | `/admin/roles` | Tạo role mới | [SCR-ADM-06] | Write: roles |
| [API-402] | PUT | `/admin/roles/{id}` | Cập nhật permissions của role | [SCR-ADM-06] | Write: role_has_permissions |
| [API-403] | DELETE | `/admin/roles/{id}` | Xóa role (nếu không có user) | [SCR-ADM-06] | Delete: roles |
| [API-404] | GET | `/admin/permissions` | DS tất cả permissions | [SCR-ADM-06] | Read: permissions (Spatie) |

### 5.12 Audit & Moderation

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-410] | GET | `/admin/audit-logs` | Nhật ký hệ thống (filter: date, action, admin) | [SCR-ADM-08] | Read: [DB-audit_logs] |
| [API-420] | DELETE | `/admin/comments/{id}` | Xóa comment vi phạm | [SCR-ADM-09] | Write: [DB-comments].deleted_at |

### 5.13 System Settings

| API ID | Method | Endpoint | Mô tả | Screen Ref | DB Impact |
|--------|--------|----------|--------|------------|-----------|
| [API-430] | GET | `/admin/settings` | Lấy toàn bộ cấu hình hệ thống | [SCR-ADM-12] | Read: [DB-settings] |
| [API-431] | PUT | `/admin/settings/{key}` | Cập nhật một giá trị cấu hình | [SCR-ADM-12] | Write: [DB-settings].value |

---

## 6. Cross-Reference Mapping

Bảng ánh xạ giữa hệ thống ID mới và các ID cũ trong tài liệu hiện tại.

### 6.1 Guest APIs

| New ID | Doc 04 (Old) | Screen Specs (Old) |
|--------|-------------|-------------------|
| [API-001] | — | [API-AUTH-00] |
| [API-002] | [API-GUEST-02] | [API-AUTH-01] |
| [API-003] | [API-GUEST-01] | [API-AUTH-04] |
| [API-004] | — | [API-AUTH-07] |
| [API-005] | — | [API-AUTH-08] |
| [API-006] | — | [API-AUTH-09] |
| [API-007] | — | [API-AUTH-05] |
| [API-008] | — | [API-AUTH-06] |
| [API-009] | — | [API-AUTH-02] |
| [API-010] | — | [API-AUTH-03] |
| [API-011] | [API-GUEST-06] | [API-LST-01] |
| [API-012] | [API-GUEST-03] | [API-LST-02] |
| [API-013] | — | [API-LST-03] |
| [API-014] | [API-GUEST-05] | [API-LST-16] |
| [API-015] | [API-GUEST-07] | [API-LST-15] |
| [API-016] | [API-GUEST-04] | — |
| [API-017] | — | — |
| [API-018] | — | — |
| [API-019] | — | [API-LST-20] |
| [API-020] | — | [API-LST-21] |
| [API-021] | — | [API-LST-22] |
| [API-022] | — | — |

### 6.2 Listener APIs

| New ID | Doc 04 (Old) | Screen Specs (Old) |
|--------|-------------|-------------------|
| [API-101] | [API-LST-01] | — |
| [API-102] | [API-LST-02] | — |
| [API-103] | — | [API-AUTH-10] |
| [API-104] | — | [API-AUTH-11] |
| [API-110] | [API-LST-03] | — |
| [API-111] | [API-LST-04] | [API-LST-07] |
| [API-120]–[API-123] | [API-LST-05] (gộp) | [API-LST-10]–[API-LST-12] |
| [API-124] | [API-LST-06] | [API-LST-06] |
| [API-125] | [API-LST-07] | [API-LST-12] |
| [API-130] | — | [API-LST-23] |
| [API-140]–[API-147] | [API-LST-08]–[API-LST-11] | — |

### 6.3 Artist APIs

| New ID | Doc 04 (Old) | Screen Specs (Old) |
|--------|-------------|-------------------|
| [API-201] | [API-ART-01] | [API-ART-10] |
| [API-202] | [API-ART-02] | — |
| [API-210]–[API-213] | — | [API-ART-10]–[API-ART-13] |
| [API-220] | [API-ART-03] | [API-ART-02] |
| [API-221] | [API-ART-04] | [API-ART-03] |
| [API-222] | [API-ART-05] | [API-ART-04] |
| [API-223] | [API-ART-06] | — |
| [API-230]–[API-232] | [API-ART-07] | [API-ART-06]–[API-ART-09] |
| [API-240] | [API-ART-08] | [API-ART-01] |

### 6.4 Admin APIs

| New ID | Doc 04 (Old) | Screen Specs (Old) |
|--------|-------------|-------------------|
| [API-301] | [API-ADM-01] | — |
| [API-310] | [API-ADM-02] | [API-ADM-23] |
| [API-320] | [API-ADM-03] | [API-ADM-03] |
| [API-321] | [API-ADM-05] | [API-ADM-04] |
| [API-322] | [API-ADM-03] (PUT) | [API-ADM-05] |
| [API-330] | [API-ADM-04] | — |
| [API-331] | [API-ADM-06] | — |
| [API-332] | [API-ADM-07] | — |
| [API-340]–[API-342] | — | [API-ADMIN-01]–[API-ADMIN-02] |
| [API-350] | [API-ADM-11] | [API-ADM-07] |
| [API-352] | [API-ADM-12] | [API-ADM-09] |
| [API-353] | [API-ADM-13] | [API-ADM-10] |
| [API-360] | [API-ADM-10] | [API-ADM-26] |
| [API-363] | [API-ADM-14] | [API-ADM-30] |
| [API-370]–[API-372] | [API-ADM-08] | [API-ADM-19]–[API-ADM-21] |
| [API-380]–[API-383] | [API-ADM-09] | [API-ADM-11]–[API-ADM-14] |
| [API-400]–[API-404] | — | [API-ADM-15]–[API-ADM-18] |
| [API-410] | [API-ADM-17] | [API-ADM-22] |
| [API-420] | [API-ADM-16] | [API-ADM-25] |

---

## 7. Thống kê

| Nhóm | Số lượng API |
|------|-------------|
| Guest | 24 |
| Listener | 37 |
| Artist | 22 |
| Admin | 48 |
| **Tổng** | **131** |

---

## 8. Changelog

| Ngày | Thay đổi |
|------|---------|
| 2026-07-08 | Tạo mới — Hợp nhất 3 hệ thống ID cũ (Doc 04, Auth/Listener Screens, Admin/Artist Screens) thành 1 hệ thống ID thống nhất. Bổ sung ~35 endpoints thiếu từ Gap Analysis. |
