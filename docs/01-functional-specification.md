# TÀI LIỆU ĐẶC TẢ YÊU CẦU & TÍNH NĂNG (FSD)

**Dự án:** Nền tảng Âm nhạc Trực tuyến (Audio Streaming Web App)
**Phiên bản:** 1.0

---

# 1. Introduction (Giới thiệu)

Tài liệu này cung cấp đặc tả chi tiết về các chức năng, luồng nghiệp vụ và quy tắc hoạt động của hệ thống Nền tảng Âm nhạc Trực tuyến. Dự án được xây dựng theo mô hình tách biệt Frontend (VueJS) và Backend (Laravel RESTful API), hướng đến trải nghiệm nghe nhạc trực tuyến mượt mà, đồng thời cung cấp môi trường quản lý nội dung dành cho nghệ sĩ và quản trị viên.

---

## 2. Glossary (Thuật ngữ)

| Thuật ngữ       | Mô tả                                                                           |
| --------------- | ------------------------------------------------------------------------------- |
| Stream          | Lượt nghe hợp lệ đáp ứng điều kiện ghi nhận của hệ thống.                       |
| Queue           | Danh sách các bài hát sẽ phát tiếp theo.                                        |
| Preview         | Đoạn nghe thử 30 giây đầu của bài hát.                                          |
| Workspace       | Không gian làm việc riêng dành cho Artist.                                      |
| Analytics       | Thống kê hiệu suất bài hát, album và nghệ sĩ.                                   |
| Like / Favorite | Thao tác lưu Song, Album, Artist hoặc Playlist vào thư viện yêu thích của người dùng. |
| Soft Delete     | Xóa mềm, dữ liệu vẫn tồn tại trong CSDL.                                        |

---

# 3. Project Scope (Phạm vi Dự án)

## 3.1 Target Users

* Music Listener
* Independent Artist
* Administrator

---

## 3.2 In Scope (Giai đoạn 1)

Hệ thống hỗ trợ các chức năng:

* Authentication
* Audio Streaming
* Playlist Management
* Artist Workspace
* Notification
* Analytics Dashboard
* Banner & Genre Management

---

## 3.3 Out of Scope

Các chức năng chưa triển khai trong Phase 1:

* Podcast
* Livestream
* Music Video
* Payment Gateway
* Premium Subscription
* Offline Download
* Content Moderation & User Reports

---

## 3.4 Assumptions

* Người dùng có kết nối Internet.
* Trình duyệt hỗ trợ HTML5 Audio.
* MinIO hoạt động ổn định.
* SMTP Mail hoạt động bình thường.

---

## 3.5 Constraints

* Laravel 13
* Vue 3
* PHP 8.4
* Node.js
* MySQL 8
* Redis
* MinIO
* Max Upload Size: Audio < 20MB, Cover < 2MB

---

# 4. User Roles & Permission

## 4.1 Actors

* Guest
* Listener
* Artist
* Admin

---

## 4.2 Navigation

| Actor                    | Route                           |
| ------------------------ | ------------------------------- |
| Artist                   | `/artist/dashboard`             |
| Admin                    | `/admin/dashboard`              |
| Admin (Artist Analytics) | `/admin/artists/{id}/analytics` |

> Admin không truy cập trực tiếp Artist Workspace mà chỉ xem dữ liệu thông qua Admin Panel.

---

## 4.3 Permission Matrix

| Feature                  | Guest | Listener |  Artist  |      Admin      |
| ------------------------ | :---: | :------: | :------: | :-------------: |
| Search & Explore         |   ✅   |     ✅    |     ✅    |        ✅        |
| Preview (30s)            |   ✅   |     ✅    |     ✅    |        ✅        |
| Full Stream              |   ❌   |     ✅    |     ✅    |        ✅        |
| Like / Favorite Song     |   ❌   |     ✅    |     ✅    |        ✅        |
| Favorite Album           |   ❌   |     ✅    |     ✅    |        ✅        |
| Favorite Artist          |   ❌   |     ✅    |     ✅    |        ✅        |
| Favorite Playlist        |   ❌   |     ✅    |     ✅    |        ✅        |
| Follow Artist            |   ❌   |     ✅    |     ✅    |        ✅        |
| Comment                  |   ❌   |     ✅    |     ✅    |        ✅        |
| Create Playlist          |   ❌   |     ✅    |     ✅    |        ✅        |
| Upload Song              |   ❌   |     ❌    |     ✅    |        ✅        |
| Artist Dashboard         |   ❌   |     ❌    |     ✅    |        ❌        |
| View Artist Analytics    |   ❌   |     ❌    | Own Data   | Via Admin Panel  |
| Approve / Reject Song    |   ❌   |     ❌    |     ❌    |        ✅        |
| Manage Users             |   ❌   |     ❌    |     ❌    |        ✅        |
| Manage Banner            |   ❌   |     ❌    |     ❌    |        ✅        |

---

# 5. Functional Modules

1. Authentication
2. Users
- Profile
- Follow Artist
- Favorite Song
- Favorite Album
- Favorite Artist
- Favorite Playlist
- Listening History
- Recent Search

3. Music
4. Playlist
5. Artist Workspace
6. Administration
7. Notification

---

## 6. Functional Requirements

| Feature                  | Description                                                   | Actor            | Business Rule                                         |
| ------------------------ | ------------------------------------------------------------- | ---------------- | ----------------------------------------------------- |
| Resume Listening         | Tiếp tục phát từ vị trí gần nhất                              | Listener, Artist | Cập nhật Last Position                                |
| Queue Persistence        | Queue không mất khi refresh                                   | Listener, Artist | Lưu Local Storage                                     |
| Upload Song              | Upload audio + metadata + cover                               | Artist           | Validate file                                         |
| Search Content           | Tìm kiếm Song, Artist, Album, Playlist                        | All              | BR04                                                  |
| Like / Favorite Song | Thả tim bài hát để lưu vào thư viện yêu thích. | Listener | Một người dùng chỉ được Favorite một lần cho mỗi bài hát. |
| Favorite Album | Lưu Album vào thư viện yêu thích. | Listener | Một người dùng chỉ được Favorite một lần cho mỗi Album. |
| Favorite Artist | Lưu Artist vào danh sách yêu thích. | Listener | Một người dùng chỉ được Favorite một lần cho mỗi Artist. |
| Favorite Playlist | Lưu Playlist Public vào thư viện. | Listener | Chỉ Playlist Public được phép Favorite. |
| Follow Artist            | Theo dõi nghệ sĩ                                              | Listener         | Không follow chính mình                               |
| Listening History        | Lưu lịch sử nghe                                              | Listener         | Cập nhật sau mỗi phiên phát hợp lệ                    |
| Lyrics Sync              | Đồng bộ lời bài hát theo thời gian                            | All              | File LRC hợp lệ                                       |
| Playlist Sharing         | Chia sẻ playlist công khai                                    | Listener         | Playlist phải Public                                  |
| Manage Admin Roles | Phân quyền động, tạo Role con (Moderator, Finance) trong Admin | Admin | Dành riêng cho Super Admin phân quyền cho nhân viên |
| Delegate Permissions | Gán các quyền (Approve Song, Ban User...) cho từng Role | Admin | Sử dụng RBAC nội bộ Admin Panel |

---

## 7. Business Rules

**BR01** Một bài hát chỉ được Public khi:

* Status = Approved
* Artist không bị khóa

**BR02**

Album chỉ được Public khi có ít nhất **01 bài hát Approved**.

**BR03**

Guest không được Like, Follow hoặc Clone Playlist.

**BR04**

Chỉ Playlist Public mới xuất hiện trong kết quả Search.

**BR05**

Artist bị khóa sẽ mất toàn bộ quyền Upload, Edit và Release.

**BR06**

Song Rejected có thể chỉnh sửa và Submit lại.

**BR07**

Song, Album và Playlist chỉ được Soft Delete.

**BR08**

Một người dùng chỉ được Favorite một lần đối với mỗi Song.

**BR09**

Một người dùng chỉ được Favorite một lần đối với mỗi Album.

**BR10**

Một người dùng chỉ được Favorite một lần đối với mỗi Artist.

**BR11**

Một người dùng chỉ được Favorite một lần đối với mỗi Playlist.

**BR12**

Chỉ Playlist có trạng thái `Public` mới được phép Favorite.

**BR13**

**Chống Cheat Stream:** Ghi nhận Stream phải đi qua Validate Anti-cheat (kiểm tra giới hạn số lượt stream từ cùng 1 Session/IP trong ngày).

**BR14**

**Giới hạn thiết bị:** Một tài khoản Listener chỉ được phép có tối đa 1 phiên stream nhạc tại một thời điểm (Ngăn chặn chia sẻ tài khoản). Phát nhạc ở thiết bị mới sẽ ngắt phiên stream ở thiết bị cũ.

**BR15**

**Tiêu chuẩn Stream hợp lệ (Anti-cheat 30s):** Thời gian nghe `30 giây` phải là **thời gian nghe thực tế liên tục (Continuous playback)**. Việc user tua đi tua lại (scrubbing) một đoạn nhạc 5 giây nhiều lần sẽ không được cộng dồn (Not Accumulated) để tính là 1 lượt stream, nhằm chặn thủ thuật gian lận tiền bản quyền.

---

# 8. Business Workflows

## 8.1 Notification Events

| Event           | Receiver  |
| --------------- | --------- |
| Song Approved   | Artist    |
| Song Rejected   | Artist    |
| Song Processing Failed | Artist |
| New Release     | Followers |
| New Follower    | Artist    |

---

## 8.2 Error Scenarios

### Upload thất bại

Artist Upload

↓

Validation

↓

HTTP 422

↓

Hiển thị lỗi chi tiết.

---

### Song đã bị ẩn

Listener Play Song

↓

Song Hidden

↓

HTTP 404

↓

Skip Queue

↓

Hiển thị:

> Song no longer available

---

### Streaming Rate Limit

Listener liên tục gửi yêu cầu **Play/Pause** hoặc Streaming trong thời gian ngắn.

↓

Rate Limit Middleware phát hiện hành vi bất thường.

↓

HTTP **429 (Too Many Requests)**.

↓

Tạm thời không ghi nhận lượt Stream và hiển thị thông báo:

> Too many requests. Please try again later.


## 8.3 Streaming Workflow

```text
Listener Click Play
        │
        ▼
Validate Permission
        │
        ▼
Start Streaming
        │
        ▼
Update Listening History
        │
        ▼
Update Resume Position
        │
        ▼
Nghe >= 30s hoặc >= 50%
        │
        ▼
Validate Anti-cheat (Session/IP limit)
        │
        ▼
Count Stream
        │
        ▼
Update Analytics
```

---

# 9. State Transition

```text
Draft
   │
   ▼
Pending
   │
   ├────────► Rejected
   │             │
   │             ▼
   │        Resubmit
   │             │
   └─────────────┘
   │
   ▼
Approved
   │
   ▼
Hidden
```

---

# 10. Non-functional Requirements

## Availability

* Uptime ≥ 99%

## Performance

* CRUD API < 300ms
* Audio không bị gián đoạn khi chuyển trang

## Scalability

* Hỗ trợ tối thiểu 1000 concurrent users

## Maintainability

* Modular Architecture
* SOLID
* RESTful API

## Compatibility

* Chrome
* Firefox
* Edge
* Safari

## Security

* Laravel Sanctum
* SQL Injection Prevention
* XSS Protection
* CSRF Protection
* Rate Limiting

## Logging & Audit

Hệ thống ghi nhận Audit Log cho các thao tác quan trọng:

* Login
* Upload Song
* Approve Song
* Reject Song
* Delete Song
* Ban User

---

# 11. Future Scope

* Premium Subscription
* Offline Download (DRM)
* AI Recommendation
* Podcast
* Music Video
* Realtime Listening Room
* Multi-language
* Smart Notification
