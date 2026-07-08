# THIẾT KẾ CƠ SỞ DỮ LIỆU (DATABASE DESIGN)

**Dự án:** Nền tảng Âm nhạc Trực tuyến (Audio Streaming Web App)
**Phiên bản:** 1.0

---

# 1. Giới thiệu

Tài liệu này mô tả thiết kế cơ sở dữ liệu của hệ thống Audio Streaming Web App.

Mục tiêu của tài liệu:

* Xác định các thực thể (Entities)
* Định nghĩa mối quan hệ giữa các bảng
* Chuẩn hóa dữ liệu
* Làm cơ sở để xây dựng Migration, Model và ERD

Hệ thống sử dụng:

* Database: MySQL 8
* ORM: Laravel Eloquent ORM

---

# 2. Database Convention

## 2.1 Naming Convention

| Thành phần  | Quy ước                    |
| ----------- | -------------------------- |
| Table       | snake_case (plural)        |
| Column      | snake_case                 |
| Foreign Key | `<table>_id`               |
| Primary Key | `id`                       |
| Timestamp   | `created_at`, `updated_at` |
| Soft Delete | `deleted_at`               |

Ví dụ

```text
users
songs
artist_profiles
playlist_songs
```

---

## 2.2 Primary Key

Tất cả bảng sử dụng `BIGINT UNSIGNED AUTO_INCREMENT` làm khóa chính để tối ưu performance cho các truy vấn Foreign Key và B-Tree Index. Tuy nhiên, `id` của thông báo (`notifications`) sẽ được giữ chuẩn `UUID`.

---

## 2.3 Common Columns

Hầu hết các bảng đều có:

```text
id
created_at
updated_at
deleted_at
```

---

## 2.4 Database Design Principles

Hệ thống được thiết kế theo các nguyên tắc sau:

- Chuẩn hóa dữ liệu đến Third Normal Form (3NF).
- Toàn bộ bảng nghiệp vụ sử dụng `created_at` và `updated_at`.
- Các bảng nghiệp vụ hỗ trợ `Soft Delete` khi cần lưu lịch sử.
- Không sử dụng MySQL ENUM, ưu tiên `VARCHAR` kết hợp với PHP Enums trong Laravel.
- Tất cả Foreign Key đều được khai báo rõ ràng.
- Các bảng trung gian sử dụng Composite Unique Index để tránh dữ liệu trùng lặp.
- Ưu tiên khả năng mở rộng và tương thích với Laravel Eloquent ORM.

# 3. Entity Relationship Overview

Hệ thống gồm các nhóm dữ liệu chính:

```
Users
│
├── Artist Profile
├── Playlist
├── Listening History
├── Favorite Song
└── Notifications

Songs
│
├── Album
├── Genre
├── Lyrics
├── Stream
└── Comments

System
│
└── Settings (Key-Value Config)
```

---

# 4. Database Tables

## 4.1 [DB-users]

Lưu thông tin tài khoản.

| Column            | Type         | Description                       |
| ----------------- | ------------ | --------------------------------- |
| id                | bigint       | PK                                |
| name              | varchar(255) | Họ tên                            |
| email             | varchar(255) | Email                             |
| password          | varchar(255) | Mật khẩu                          |
| role              | varchar(50)  | Guest / Listener / Artist / Admin |
| avatar            | varchar      | Avatar                            |
| status            | varchar(50)  | Active / Suspended / Banned       |
| notification_prefs| json         | Cấu hình nhận thông báo (opt-out) |
| email_verified_at | timestamp    | Email Verify                      |
| created_at        | timestamp    |                                   |
| updated_at        | timestamp    |                                   |
| deleted_at        | timestamp    | Soft Delete                       |

### Business Rule

- `role` được quản lý bằng PHP Enum.
- `status` được quản lý bằng PHP Enum.
---

## 4.1.2 [DB-password_reset_tokens]
Lưu trữ các Token tạm thời dùng cho chức năng Khôi phục mật khẩu (Forgot Password) của Laravel.

| Column | Type | Attributes | Constraints / Description |
| :--- | :--- | :--- | :--- |
| email | varchar(255) | Primary Key | Khóa chính theo chuẩn của Laravel |
| token | varchar(255) | Not Null | Mã băm (hash) phục hồi |
| created_at | timestamp | Nullable | Thời điểm gửi yêu cầu |

### Business Rule
- Bảng này không có trường `id` hay `updated_at`.
- Khi người dùng reset mật khẩu thành công, bản ghi chứa email của họ sẽ lập tức bị Delete khỏi bảng này.

---

## 4.2 [DB-artist_profiles]

Thông tin mở rộng dành cho Artist (Nghệ sĩ).

| Column      | Type         | Description                                         |
| ----------- | ------------ | --------------------------------------------------- |
| id          | bigint       | Primary Key                                         |
| user_id     | FK           | Tham chiếu đến `users.id` (Chủ sở hữu hồ sơ)        |
| stage_name  | varchar(100) | Nghệ danh hoạt động của nghệ sĩ                     |
| bio         | text         | Tiểu sử, thông tin giới thiệu về nghệ sĩ            |
| cover_image | varchar(255) | Đường dẫn ảnh bìa/ảnh nền của trang hồ sơ nghệ sĩ   |
| is_verified | boolean      | Trạng thái nghệ sĩ đã xác minh (Tích xanh Official) |
| verified_at | timestamp    | Thời điểm được hệ thống/Admin cấp quyền xác minh    |
| facebook    | varchar(255) | Liên kết đến trang fanpage/cá nhân Facebook         |
| instagram   | varchar(255) | Liên kết đến tài khoản Instagram                    |
| youtube     | varchar(255) | Liên kết đến kênh YouTube chính thức                |
| website     | varchar(255) | Trang web cá nhân hoặc link tổng hợp (vd: Linktree) |

Relationship

```text
User (1)

↓

Artist Profile (1)
```
### Business Rule

- Chỉ Admin được xác minh Artist.
- Artist đã xác minh sẽ hiển thị huy hiệu Verified.

---

## 4.3 [DB-genres]

Danh mục thể loại.

| Column      | Type    | Attributes | Constraints |
| ----------- | ------- | ---------- | ----------- |
| id          | bigint  | PK         | Tự tăng     |
| parent_id   | bigint  | FK, Null   | Tham chiếu về `genres.id` (Chứa danh mục cha) |
| name        | varchar | Not Null   | Tên thể loại (VD: EDM, Pop) |
| slug        | varchar | Unique     | Dùng cho URL (VD: edm, pop) |
| icon        | varchar | Null       | URL icon/ảnh đại diện của danh mục |
| description | text    | Null       | Mô tả |
| is_active   | boolean | Default 1  | 1: Hiển thị, 0: Ẩn |

**Business Rule:** 
- Sử dụng mô hình **Adjacency List** (Cây liền kề) bằng cột `parent_id`. Ví dụ: Nhạc Âm Hưởng (Cha) -> Nhạc Quê Hương (Con) -> Nhạc Trữ Tình (Cháu).
- Nếu `parent_id` là `null`, đó là danh mục gốc (Root Category).

---

## 4.4 [DB-albums]

Thông tin Album.

| Column       | Type                    | Description             |
| ------------ | ----------------------- | ----------------------- |
| id           | bigint                  | Primary Key             |
| artist_id    | FK                      | Tham chiếu đến `artist_profiles.id` |
| title        | varchar                 | Tên Album               |
| cover_image  | varchar                 | Ảnh bìa Album           |
| release_date | date                    | Ngày phát hành          |
| type         | enum(Single, EP, Album) | Loại Album              |
| status       | varchar(50)             | Draft / Published / Hidden |
| description  | text (nullable)         | Mô tả album             |
| created_at   | timestamp               |                         |
| updated_at   | timestamp               |                         |
| deleted_at   | timestamp (nullable)    | Soft Delete             |

### Status Enum

```
Draft → Published ↔ Hidden
```

### Business Rule

- `status` được quản lý bằng PHP Enum: `Draft`, `Published`, `Hidden`.
- `album_id` trong `[DB-songs]` là **nullable** — bài hát có thể phát hành dạng Single mà không cần Album.
- Album chỉ được Published khi có ít nhất một Song ở trạng thái `Approved`.

---

## 4.5 [DB-songs]

Thông tin bài hát.

| Column              | Type                 | Description                                |
| ------------------- | -------------------- | ------------------------------------------ |
| id                  | bigint               | Primary Key                                |
| artist_id           | FK                   | Tham chiếu đến `artist_profiles.id` (Nghệ sĩ chính) |
| album_id            | FK (nullable)        | Tham chiếu đến `albums.id` (nullable — bài hát có thể là Single) |
| genre_id            | FK                   | Tham chiếu đến `genres.id`                 |
| title               | varchar              | Tên bài hát                                |
| lyrics              | longtext             | Lời bài hát                                |
| duration            | integer              | Thời lượng (giây)                          |
| original_file_path  | varchar (nullable)   | Đường dẫn file gốc trên MinIO (dùng để Retry) |
| hls_path            | varchar              | Đường dẫn file HLS trên MinIO              |
| original_size       | bigint               | Dung lượng file gốc (bytes)                |
| bitrate             | integer              | Bitrate (kbps)                             |
| sample_rate         | integer              | Sample rate (Hz)                           |
| channels            | tinyint              | Số kênh âm thanh                           |
| checksum            | varchar(64)          | Mã băm SHA-256 để phát hiện file trùng     |
| cover_image         | varchar              | Ảnh bìa bài hát                            |
| status              | varchar(50)          | Draft / Processing / Pending / Approved / Hidden / Rejected / Failed |
| play_count          | bigint               | Tổng lượt nghe                             |
| rejected_reason     | text (nullable)      | Lý do từ chối, hiển thị cho Artist         |
| rejected_at         | timestamp (nullable) | Thời điểm từ chối                          |
| approved_at         | timestamp (nullable) | Thời điểm phê duyệt                       |
| processing_error    | text (nullable)      | Thông báo lỗi FFmpeg nếu transcoding thất bại |
| processing_attempts | tinyint (default 0)  | Số lần thử transcoding                     |
| created_at          | timestamp            |                                            |
| updated_at          | timestamp            |                                            |
| deleted_at          | timestamp (nullable) | Soft Delete                                |

### Status Enum

```
Status: Draft → Processing → Pending → Approved ↔ Hidden
                                         ↓
                                      Rejected
         Processing → Failed (retry possible)
```

### Business Rule
- Metadata được sinh tự động sau khi Upload.
- Checksum dùng để phát hiện file trùng lặp.
- `album_id` là nullable — bài hát có thể phát hành dạng Single mà không cần Album.
- Khi transcoding thất bại, `status` chuyển sang `Failed` và `processing_error` lưu thông báo lỗi. `original_file_path` bắt buộc phải tồn tại để thực hiện Retry.
- `processing_attempts` được tăng sau mỗi lần thử transcoding, hỗ trợ retry logic.

---

## 4.5.1 [DB-song_artists] (Pivot)

Bảng trung gian quản lý các nghệ sĩ hợp tác (Featured Artists). Hỗ trợ chia sẻ doanh thu và metadata chính xác.

| Column      | Type    | Description                                      |
| ----------- | ------- | ------------------------------------------------ |
| song_id     | FK      | Tham chiếu `songs.id`                            |
| artist_id   | FK      | Tham chiếu `artist_profiles.id`                  |
| role        | varchar | `main` / `featured` / `producer`                 |

**Business Rule:**
- Primary Key (Composite): `(song_id, artist_id)`.
- Nghệ sĩ upload mặc định được gán `role = main`.

---

## 4.6 [DB-playlists]

| Column      | Type                  | Description                                        |
| ----------- | --------------------- | -------------------------------------------------- |
| id          | bigint                | Primary Key                                        |
| user_id     | FK                    | Tham chiếu đến `users.id`                          |
| title       | varchar               | Tên playlist                                       |
| description | text                  | Mô tả playlist                                     |
| cover_image | varchar               | Ảnh bìa playlist                                   |
| privacy     | enum(Public, Private) | Quyền riêng tư                                     |
| type        | varchar (default 'user') | `user` hoặc `system` (Phân biệt playlist hệ thống vs cá nhân) |
| created_at  | timestamp             |                                                    |
| updated_at  | timestamp             |                                                    |
| deleted_at  | timestamp (nullable)  | Soft Delete                                        |

---

## 4.7 [DB-playlist_songs]

Bảng trung gian.

| Column      | Type      | Description                      |
| ----------- | --------- | -------------------------------- |
| playlist_id | FK        | Tham chiếu đến `playlists.id`    |
| song_id     | FK        | Tham chiếu đến `songs.id`       |
| position    | integer   | Thứ tự bài hát trong playlist    |
| created_at  | timestamp | Thời điểm thêm bài vào playlist |

Unique Index

(playlist_id, song_id)

Index

(playlist_id, position)

Relationship

```
Playlist

N

↓

Songs
```

---

## 4.8 [DB-favorite_songs]

Lưu danh sách bài hát yêu thích của người dùng.

| Column | Type | Description |
|---------|------|-------------|
| id | bigint | Primary Key |
| user_id | FK | Tham chiếu đến `users.id` |
| song_id | FK | Tham chiếu đến `songs.id` |
| created_at | timestamp | Thời gian thêm vào danh sách yêu thích |

### Relationship

- User (1) → (N) Favorite Songs
- Song (1) → (N) Favorite Songs

### Constraints

- Foreign Key: `user_id → users.id`
- Foreign Key: `song_id → songs.id`
- Unique Index: `(user_id, song_id)`

### Business Rule

- Một người dùng chỉ được Favorite một lần đối với mỗi bài hát.

---

## 4.9 [DB-favorite_albums]

Lưu danh sách album yêu thích của người dùng.

| Column | Type | Description |
|---------|------|-------------|
| id | bigint | Primary Key |
| user_id | FK | Tham chiếu đến `users.id` |
| album_id | FK | Tham chiếu đến `albums.id` |
| created_at | timestamp | Thời gian thêm vào danh sách yêu thích |

### Relationship

- User (1) → (N) Favorite Albums
- Album (1) → (N) Favorite Albums

### Constraints

- Foreign Key: `user_id → users.id`
- Foreign Key: `album_id → albums.id`
- Unique Index: `(user_id, album_id)`

### Business Rule

- Một người dùng chỉ được Favorite một lần đối với mỗi album.

---

## 4.10 [DB-favorite_artists]

Lưu danh sách nghệ sĩ yêu thích của người dùng.

| Column | Type | Description |
|---------|------|-------------|
| id | bigint | Primary Key |
| user_id | FK | Tham chiếu đến `users.id` |
| artist_id | FK | Tham chiếu đến `artist_profiles.id` |
| created_at | timestamp | Thời gian thêm vào danh sách yêu thích |

### Relationship

- User (1) → (N) Favorite Artists
- Artist Profile (1) → (N) Favorite Artists

### Constraints

- Foreign Key: `user_id → users.id`
- Foreign Key: `artist_id → artist_profiles.id`
- Unique Index: `(user_id, artist_id)`

### Business Rule

- Một người dùng chỉ được Favorite một lần đối với mỗi nghệ sĩ.

---

## 4.11 [DB-favorite_playlists]

Lưu danh sách playlist yêu thích của người dùng.

| Column | Type | Description |
|---------|------|-------------|
| id | bigint | Primary Key |
| user_id | FK | Tham chiếu đến `users.id` |
| playlist_id | FK | Tham chiếu đến `playlists.id` |
| created_at | timestamp | Thời gian thêm vào danh sách yêu thích |

### Relationship

- User (1) → (N) Favorite Playlists
- Playlist (1) → (N) Favorite Playlists

### Constraints

- Foreign Key: `user_id → users.id`
- Foreign Key: `playlist_id → playlists.id`
- Unique Index: `(user_id, playlist_id)`

### Business Rule

- Một người dùng chỉ được Favorite một lần đối với mỗi playlist.
---


## 4.12 [DB-follows]

Lưu danh sách nghệ sĩ mà người dùng theo dõi để nhận thông báo khi có bài hát hoặc album mới.

| Column | Type | Description |
|---------|------|-------------|
| id | bigint | Primary Key |
| listener_id | FK | Tham chiếu đến `users.id` |
| artist_id | FK | Tham chiếu đến `artist_profiles.id` |
| created_at | timestamp | Thời gian theo dõi |

### Relationship

- User (1) → (N) Follow Artist
- Artist Profile (1) → (N) Followers

### Constraints

- Foreign Key: `listener_id → users.id`
- Foreign Key: `artist_id → artist_profiles.id`
- Unique Index: `(listener_id, artist_id)`

### Business Rule

- Một Listener chỉ được Follow một Artist một lần.
- Artist không được Follow chính mình.

---

## 4.1.1 [DB-artist_invitations]
Lưu trữ các mã mời (Token) do Admin phát hành để cấp quyền đăng ký tài khoản Artist cho đối tác. Đảm bảo tính độc quyền (Invite-Only).

| Column | Type | Attributes | Constraints / Description |
| :--- | :--- | :--- | :--- |
| id | bigint | Primary Key | |
| email | string(255) | Nullable | Email dự kiến gửi lời mời (Tùy chọn) |
| token | string(64) | Unique, Index | Mã băm (hash) gửi qua URL |
| expires_at | timestamp | Not Null | Hạn sử dụng của link |
| used_at | timestamp | Nullable | Thời điểm nghệ sĩ đăng ký thành công |
| created_by | bigint | FK | Tham chiếu `users.id` (Admin tạo link) |
| created_at | timestamp | | |
| updated_at | timestamp | | |

### Business Rule
- Mỗi `token` chỉ được sử dụng thành công đúng 1 lần (khi dùng xong sẽ cập nhật `used_at`).
- Nếu quá hạn `expires_at`, link đăng ký sẽ tự động vô hiệu hóa.

---

## 4.13 [DB-listening_histories]

Lưu lịch sử nghe nhạc và hỗ trợ Resume Listening.

| Column | Type | Description |
|---------|------|-------------|
| id | bigint | Primary Key |
| user_id | FK | Người nghe |
| song_id | FK | Bài hát |
| last_position | integer | Vị trí phát cuối cùng (giây) |
| listened_at | timestamp | Thời điểm nghe gần nhất |
| created_at | timestamp | Created Time |
| updated_at | timestamp | Updated Time |

### Relationship

- User (1) → (N) Listening History
- Song (1) → (N) Listening History

### Constraints

- Foreign Key: `user_id → users.id`
- Foreign Key: `song_id → songs.id`

### Business Rule

- Một User chỉ có một bản ghi lịch sử cho mỗi bài hát.
- Mỗi lần phát sẽ cập nhật `last_position`.

---

## 4.14 [DB-streams]
Lưu trữ lượt nghe (play) hợp lệ của bài hát để tính toán Analytics và Trả tiền bản quyền (Royalties).
Bảng này sẽ phình to rất nhanh (hàng triệu bản ghi mỗi ngày). 

> **💡 Tối ưu hóa (Optimization): Table Partitioning**
> Bảng này PHẢI áp dụng kỹ thuật **Database Partitioning theo thời gian** (Month/Year) ngay từ đầu để tránh nghẽn cổ chai (bottleneck) khi query thống kê. Các dữ liệu quá hạn (ví dụ > 3 năm) có thể được Archive ra Cold Storage hoặc xoá bớt.

| Column | Type | Attributes | Constraints / Description |
| :--- | :--- | :--- | :--- |
| id | bigint | Primary Key |
| user_id | FK | Người nghe |
| song_id | FK | Bài hát |
| device | varchar | Desktop / Mobile |
| ip_address | varbinary(16)| Địa chỉ IP (Tối ưu lưu trữ và truy vấn IPv4/IPv6) |
| session_id | varchar | Nhận diện Session (Anti-cheat) |
| duration | integer | Thời gian đã nghe |
| streamed_at | timestamp | Thời điểm ghi nhận |

### Relationship

- Song (1) → (N) Streams
- User (1) → (N) Streams

### Business Rule

- Chỉ ghi nhận khi:
  - Nghe ≥ 30 giây
  - Hoặc ≥ 50% thời lượng bài hát
- Một User chỉ được tính một Stream cho cùng một bài hát trong khoảng thời gian quy định.
---

## 4.15 [DB-recent_searches]

Lưu lịch sử tìm kiếm của người dùng.

| Column | Type | Description |
|---------|------|-------------|
| id | bigint | Primary Key |
| user_id | FK | Người dùng |
| keyword | varchar | Nội dung tìm kiếm |
| created_at | timestamp | Thời gian tìm kiếm |

### Relationship

- User (1) → (N) Recent Search

### Business Rule

- Chỉ lưu tối đa 20 từ khóa gần nhất.
- Từ khóa cũ sẽ bị ghi đè hoặc xóa khi vượt giới hạn.

---

## 4.16 [DB-notifications]

Sử dụng cấu trúc mặc định của Laravel Notification.

| Column | Type | Description |
|---------|------|-------------|
| id | uuid | Primary Key |
| type | varchar | Notification Class |
| notifiable_type | varchar | Model Type |
| notifiable_id | bigint | Model ID |
| data | json | Nội dung thông báo |
| read_at | timestamp | Thời điểm đã đọc |
| created_at | timestamp | Created Time |
| updated_at | timestamp | Updated Time |

### Business Rule

- Notification có thể được gửi qua Database, Email hoặc Broadcast.
- Notification chưa đọc sẽ có `read_at = NULL`.
---

## 4.17 [DB-comments]

Lưu bình luận của người dùng trên bài hát.

| Column | Type | Description |
|---------|------|-------------|
| id | bigint | Primary Key |
| user_id | FK | Người bình luận |
| song_id | FK | Bài hát |
| parent_id | FK (nullable) | Bình luận cha |
| content | text | Nội dung |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp | Soft Delete |

### Relationship

- User (1) → (N) Comments
- Song (1) → (N) Comments
- Comment (1) → (N) Replies

### Constraints

- FK `user_id -> users.id`
- FK `song_id -> songs.id`
- FK `parent_id -> comments.id`

### Business Rule

- Chỉ người dùng đã đăng nhập được bình luận.
- Reply nhiều cấp.
- Xóa sử dụng Soft Delete.
- Admin có quyền xóa mọi bình luận.

---

## 4.18 [DB-audit_logs]

Lưu vết (Audit Trail) các thao tác quan trọng của hệ thống để dễ dàng truy vết (Ai đã làm gì, vào lúc nào).

| Column | Type | Description |
|---------|------|-------------|
| id | bigint | Primary Key |
| user_id | FK | Người thực hiện (Admin / Artist) |
| action | varchar | Tên thao tác (VD: `approve_song`, `ban_user`, `update_artist`) |
| entity_type | varchar | Model bị tác động (VD: `Song`, `User`) |
| entity_id | bigint | ID của Model bị tác động |
| old_values | json (nullable) | Dữ liệu cũ trước khi sửa |
| new_values | json (nullable) | Dữ liệu mới sau khi sửa / tạo |
| ip_address | varchar | IP thực hiện |
| created_at | timestamp | Thời gian thực hiện |

### Business Rule

- Bảng này là dạng Insert-only (Chỉ thêm, tuyệt đối không được Sửa/Xóa).
- Hỗ trợ cho API `/admin/audit-logs` để theo dõi minh bạch.

---

## 4.19 [DB-banners]

Quản lý banner quảng cáo / giới thiệu trên trang chủ (Home/Explore).

| Column | Type | Description |
|---------|------|-------------|
| id | bigint | Primary Key |
| title | varchar | Tiêu đề banner |
| image_url | varchar | Link ảnh trên MinIO |
| target_url | varchar (nullable)| Link điều hướng khi user click |
| order | int | Thứ tự hiển thị |
| is_active | boolean | Trạng thái bật/tắt (Mặc định `true`) |
| created_at | timestamp | |
| updated_at | timestamp | |

### Business Rule

- Quản lý độc quyền bởi Admin.
- API lấy danh sách Banner (`GET /guest/banners`) chỉ lấy các banner có `is_active = true` và sắp xếp theo `order`.

---

## 4.20 Spatie Permission Tables (RBAC)

Quản lý quyền động (Dynamic Roles & Permissions) dành riêng cho module Admin (ví dụ: tạo Sub-Admin, Moderator). Sử dụng schema chuẩn của thư viện `spatie/laravel-permission`:

- `roles`: Bảng lưu trữ tên role (VD: `Content Moderator`, `Finance`).
- `permissions`: Bảng lưu trữ tên quyền (VD: `approve song`, `ban user`).
- `model_has_roles`: Bảng trung gian liên kết User (chỉ Admin) với Role.
- `role_has_permissions`: Bảng trung gian liên kết Role với Permissions.
- `model_has_permissions`: Bảng trung gian gán quyền trực tiếp cho User.

---

## 4.21 [DB-settings]

Bảng lưu trữ cấu hình hệ thống dạng Key-Value.

| Column | Type | Attributes | Constraints / Description |
| :--- | :--- | :--- | :--- |
| id | bigint | Primary Key | |
| key | varchar(255) | Unique | Khóa cấu hình (VD: `payout_rate_per_stream`, `max_upload_size`) |
| value | text | Not Null | Giá trị cấu hình |
| description | varchar(255) | Nullable | Mô tả ngắn cho Admin UI |
| created_at | timestamp | | |
| updated_at | timestamp | | |

### Business Rule
- Bảng này dùng cho cấu hình động (thay đổi runtime) mà không cần deploy lại.
- Các giá trị nhạy cảm (secret key, API key) PHẢI lưu trong `.env`, KHÔNG lưu ở đây.

# 5. Relationship Summary

```
User
│
├── 1 : 1 Artist Profile
├── 1 : N Playlist
├── 1 : N Favorite Song
├── 1 : N Favorite Album
├── 1 : N Favorite Artist
├── 1 : N Favorite Playlist
├── 1 : N Listening History
├── 1 : N Notification
└── 1 : N Comment

Artist Profile
│
├── 1 : N Album
└── 1 : N Song

Album
│
├── 1 : N Song
└── 1 : N Favorite Album

Genre
│
└── 1 : N Song

Playlist
│
├── N : N Song
└── 1 : N Favorite Playlist

Song
│
├── 1 : N Comment
├── 1 : N Stream
├── N : N Playlist
└── 1 : N Favorite Song
```
---

## 6. Indexing Strategy

Để tối ưu hiệu năng truy vấn, hệ thống sử dụng các Index sau:

| Table | Index |
|---------|--------|
| users | email (UNIQUE) |
| songs | title |
| songs | checksum (UNIQUE) |
| songs | artist_id |
| songs | genre_id |
| songs | status |
| songs | (status, artist_id) COMPOSITE |
| playlists | user_id |
| playlists | type |
| playlist_songs | playlist_id |
| favorite_songs | (user_id, song_id) UNIQUE |
| favorite_albums | (user_id, album_id) UNIQUE |
| favorite_artists | (user_id, artist_id) UNIQUE |
| favorite_playlists | (user_id, playlist_id) UNIQUE |
| follows | (listener_id, artist_id) UNIQUE |
| streams | song_id |
| streams | streamed_at |
| listening_histories | (user_id, song_id) UNIQUE |
| settings | key (UNIQUE) |
---

## 7. Foreign Key Strategy

| Relationship | On Delete | On Update | Note |
|--------------|-----------|-----------|------|
| User → Playlist | CASCADE | CASCADE | |
| User → Favorite | CASCADE | CASCADE | |
| User → Listening History | CASCADE | CASCADE | |
| Song → Comment | CASCADE | CASCADE | |
| Comment → Reply | SET NULL | CASCADE | |
| Album → Song | SET NULL | CASCADE | `album_id` is NULLABLE |
| Artist → Song | RESTRICT | CASCADE | |

## 8. Data Integrity Rules

- Không được xóa vật lý dữ liệu nghiệp vụ (Soft Delete).
- Toàn bộ Foreign Key phải đảm bảo Referential Integrity.
- Dữ liệu Favorite và Follow không được phép trùng lặp.
- Một người dùng chỉ được Favorite một lần đối với mỗi Song.
- Một người dùng chỉ được Favorite một lần đối với mỗi Album.
- Một người dùng chỉ được Favorite một lần đối với mỗi Artist.
- Một người dùng chỉ được Favorite một lần đối với mỗi Playlist.
- Song chỉ được Public khi `status = Approved`.
- Album chỉ được Public khi có ít nhất một Song ở trạng thái `Approved`.
- **Soft Delete Behavior:** Vì MySQL `ON DELETE CASCADE` không hoạt động với Soft Delete, tầng Application (Laravel) phải tự xử lý ẩn Song trong các bảng liên kết (`playlist_songs`, `favorite_songs`, `listening_histories`) thông qua Eloquent Global Scope hoặc Model Events khi Song bị soft delete.
- **Rác dữ liệu MinIO (Garbage Collection):** Khi Soft Delete Song, folder chứa hàng trăm file HLS chunk (`.ts`) và playlist (`.m3u8`) vẫn còn trên MinIO gây tốn kém lưu trữ kinh khủng. Yêu cầu thiết lập Cronjob hàng ngày để dò tìm và **Hard Delete** vật lý toàn bộ thư mục HLS của những bài hát đã bị soft delete quá 30 ngày.
---

# 9. Future Database Expansion

Các bảng dự kiến bổ sung trong Phase tiếp theo:

* subscriptions
* payments
* invoices
* music_videos
* podcasts
* recommendation_logs
* listening_rooms
* chat_messages

---

# 10. Entity Relationship Diagram (ERD)

Sơ đồ ERD chi tiết được trình bày trong tài liệu riêng hoặc sinh tự động từ công cụ thiết kế (Draw.io, dbdiagram.io, MySQL Workbench).