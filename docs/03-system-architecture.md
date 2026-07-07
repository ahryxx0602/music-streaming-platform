# KIẾN TRÚC HỆ THỐNG & CHIẾN LƯỢC LƯU TRỮ (SYSTEM ARCHITECTURE)

**Dự án:** Nền tảng Âm nhạc Trực tuyến (Audio Streaming Web App)  
**Phiên bản:** 1.0

---

# 1. Introduction

Tài liệu này mô tả kiến trúc tổng thể của hệ thống, cách các thành phần tương tác với nhau, chiến lược lưu trữ dữ liệu và file, cơ chế xử lý nền, cache và triển khai.

Mục tiêu:

- Chuẩn hóa kiến trúc hệ thống
- Dễ dàng mở rộng
- Dễ bảo trì
- Đảm bảo hiệu năng và khả năng mở rộng

---

# 2. Technology Stack

| Layer | Technology |
|---------|------------|
| Frontend | Vue 3 |
| Backend | Laravel 13 |
| API | RESTful API |
| Database | MySQL 8 |
| Cache | Redis |
| Queue | Laravel Queue + Redis |
| Storage | MinIO / Local Storage |
| Authentication | Laravel Sanctum |
| Mail | SMTP |
| Web Server | Nginx |
| Runtime | PHP-FPM |

---

# 3. High-Level Architecture

```text
                    Browser
                       │
                       ▼
                  VueJS SPA
                       │
            REST API (HTTPS)
                       │
                       ▼
                Laravel Backend
        ┌──────────┼──────────┐
        ▼          ▼          ▼
     MySQL      Redis      MinIO
(Database)   (Cache/Queue) (Storage)
```

---

# 4. System Components

## 4.1 Frontend

Chịu trách nhiệm:

- Authentication
- UI Rendering
- Audio Player
- Playlist
- Artist Dashboard
- Admin Dashboard

Frontend chỉ giao tiếp thông qua REST API.

---

## 4.2 Backend

Laravel chịu trách nhiệm:

- Business Logic
- Authentication
- Authorization
- Validation
- File Upload
- Queue Dispatch
- Notification
- Analytics

---

## 4.3 Database

MySQL lưu trữ:

- User
- Song
- Album
- Playlist
- Notification
- Listening History
- Stream Analytics

---

## 4.4 Object Storage

MinIO lưu:

- Audio File
- Preview File
- Cover Image
- Avatar
- Banner

---

## 4.5 Redis

Redis được sử dụng cho:

- Cache
- Queue
- Rate Limiting
- Session (nếu cần)

---

---

## 4.6 Event-Driven Architecture

Để giảm sự phụ thuộc giữa các Module, hệ thống sử dụng cơ chế **Event-Driven Architecture** của Laravel.

### Các Event chính

- SongUploaded
- SongApproved
- SongRejected
- SongPublished
- PlaylistCreated
- UserFollowedArtist
- StreamCounted

### Các Listener

- Generate Preview Audio
- Send Notification
- Update Analytics
- Clear Cache
- Send Email

Workflow

```text
Controller
      │
      ▼
 Dispatch Event
      │
      ▼
 Event Bus
      │
 ┌────┴─────────────┐
 ▼                  ▼
Listener A      Listener B
 ▼                  ▼
Notification    Analytics
```

---


# 5. Request Flow

## 5.1 Overall Request Flow

Luồng xử lý một yêu cầu từ Client đến Database.

```text
User
   │
   ▼
VueJS SPA
   │
   ▼
Axios (HTTP Request)
   │
   ▼
Laravel REST API
   │
   ▼
Global Middleware
(Authentication, CORS, Rate Limit)
   │
   ▼
Route
   │
   ▼
Module Controller
   │
   ▼
Form Request Validation
   │
   ▼
Service Layer
(Business Logic)
   │
   ▼
Repository Layer
(Data Access)
   │
   ▼
Eloquent ORM
   │
   ▼
MySQL
```

### Trách nhiệm từng tầng

| Layer | Responsibility |
|--------|----------------|
| VueJS | Hiển thị giao diện, gửi Request |
| Axios | Giao tiếp REST API |
| Middleware | Authentication, Authorization, Rate Limiting |
| Controller | Tiếp nhận Request và điều phối |
| Form Request | Validate dữ liệu đầu vào |
| Service | Xử lý nghiệp vụ |
| Repository | Truy vấn dữ liệu |
| Eloquent ORM | Mapping Model ↔ Database |
| MySQL | Lưu trữ dữ liệu |

---

## 5.2 Modular Monolith Architecture

Hệ thống được tổ chức theo mô hình **Modular Monolith**, trong đó mỗi Domain nghiệp vụ được đóng gói thành một Module độc lập thay vì sử dụng cấu trúc MVC truyền thống.

### Các Module chính

- Authentication
- Users
- Music
- Playlist
- Artist Workspace
- Analytics
- Notification
- Administration

### Cấu trúc thư mục

```text
app/
└── Modules/
    ├── Authentication/
    │   ├── Controllers/
    │   ├── Requests/
    │   ├── Services/
    │   ├── Repositories/
    │   ├── Policies/
    │   └── Models/
    │
    ├── Music/
    │   ├── Controllers/
    │   ├── Requests/
    │   ├── Services/
    │   ├── Repositories/
    │   ├── Events/
    │   ├── Listeners/
    │   ├── Jobs/
    │   └── Models/
    │
    ├── Playlist/
    ├── Artist/
    ├── Analytics/
    ├── Notification/
    └── Administration/
```

### Nguyên tắc

- Mỗi Module chỉ chịu trách nhiệm cho một Domain nghiệp vụ.
- Business Logic nằm trong Service.
- Repository chỉ thực hiện truy vấn dữ liệu.
- Không truy cập trực tiếp Repository của Module khác.
- Giao tiếp giữa các Module thông qua Service hoặc Event.
- Tuân thủ nguyên tắc SOLID và Separation of Concerns.

---

## 5.3 Event-driven Request Flow

Đối với các nghiệp vụ phát sinh tác vụ nền, hệ thống sử dụng Event và Queue để giảm tải cho Request chính.

```text
Controller
      │
      ▼
Service
      │
      ▼
Dispatch Event
      │
      ▼
Event
      │
 ┌────┴─────────────┐
 ▼                  ▼
Listener        Queue Job
 ▼                  ▼
Notification   Generate Preview
 ▼                  ▼
Database        MinIO
```

### Ví dụ

**Upload Song**

```text
Upload Song
      │
      ▼
Save Database
      │
      ▼
Dispatch SongUploaded Event
      │
      ├────────► Generate Preview Audio
      ├────────► Send Notification
      └────────► Update Analytics
```

Cách tiếp cận này giúp Request chính phản hồi nhanh hơn, giảm Coupling giữa các Module và tận dụng tối đa Queue của Laravel.

---

# 6. Authentication Flow

Hệ thống sử dụng **Laravel Sanctum SPA Authentication** (Cookie-based) kết hợp với VueJS thay vì phát hành Bearer Token thô nhằm chống lại các cuộc tấn công XSS (Tránh việc lưu Token ở `localStorage`).

```text
Frontend (VueJS)                Backend (Laravel)
      │                                │
      │ 1. GET /sanctum/csrf-cookie    │
      ├───────────────────────────────►│ (Khởi tạo Session & CSRF)
      │◄───────────────────────────────┤ Set-Cookie: XSRF-TOKEN
      │                                │
      │ 2. POST /auth/login            │ (Kèm credentials)
      ├───────────────────────────────►│ Validate
      │◄───────────────────────────────┤ Set-Cookie: session (HttpOnly, Secure)
      │                                │
      │ 3. Các Request tiếp theo       │ (Tự động đính kèm Cookie bởi trình duyệt)
      ├───────────────────────────────►│ Middleware: auth:sanctum
      │◄───────────────────────────────┤ Return Data
```

**Lợi ích:**
- `HttpOnly` Cookie không thể bị Javascript (XSS) đọc được.
- Cơ chế bảo vệ CSRF được xử lý tự động thông qua Header `X-XSRF-TOKEN` của Axios.
---

# 7. Authorization

Hệ thống sử dụng Role-based Access Control (RBAC).

Role:

- Guest
- Listener
- Artist
- Admin (Base Role cho trang quản trị)

> **Lưu ý dành riêng cho Admin:** Base Role `Admin` (được lưu tại bảng `users`) sẽ được tích hợp với thư viện `spatie/laravel-permission` để cấu hình phân quyền động (Dynamic RBAC) bên trong Admin Dashboard. Từ đó, "Super Admin" có thể chủ động tạo ra các Role con (như `Moderator`, `Finance`) và gán các Quyền (Permissions) cụ thể cho nhân viên mà không cần sửa code.

Middleware:

```text
auth:sanctum
role:artist
role:admin
```

---

# 8. File Storage Strategy

## 8.1 Storage Structure

```text
storage/

songs/

covers/

avatars/

banners/

lyrics/

preview/
```

---

## 8.2 MinIO Bucket

```text
music-storage

songs/

covers/

preview/

avatars/
```

**Cấu hình bắt buộc cho MinIO:**
- **CORS (Cross-Origin Resource Sharing):** Phải cấu hình bucket chỉ cho phép Origin từ Domain của Frontend truy cập (ngăn chặn nhúng link nhạc sang web lậu).
- **Byte-Range Requests:** Bắt buộc bật HTTP Header `Accept-Ranges: bytes` trên MinIO để trình duyệt có thể stream từng đoạn (chunk) và cho phép user tua (seek) mượt mà thay vì phải tải toàn bộ file MP3 dung lượng lớn.

---

## 8.3 Upload Workflow (Direct-to-MinIO Strategy)

**Tối ưu hóa (Optimization):** Sử dụng cơ chế **Presigned URL Upload**. Frontend sẽ tải file trực tiếp lên MinIO, tránh việc đẩy file âm thanh dung lượng lớn qua Laravel API (gây ngốn RAM và thắt cổ chai băng thông Web Server).

```text
Artist (Frontend)
       │
       ▼
1. Xin cấp phép Upload (Request Presigned URL)
       │
       ▼
2. Laravel xác thực quyền (Validate Policy) & Cấp Presigned URL
       │
       ▼
3. Frontend đẩy trực tiếp file lên MinIO (Direct Upload) bằng URL vừa nhận
       │
       ▼
4. Frontend gọi API Laravel xác nhận Upload thành công (Webhook/Callback)
       │
       ▼
5. Laravel lưu Database & Dispatch Job xử lý Media
```

---

# 9. Queue Architecture & Media Processing

Các Job chạy nền:

- Generate Preview Audio (Chạy trên Dedicated Node)
- Send Notification
- Recalculate Trending
- Send Email
- Clean Cache

**Kiến trúc Phân bổ Worker (Chống CPU Starvation):**
- **Web Node:** Chạy các queue nhẹ (email, notification, trending) chung với PHP-FPM.
- **Media Node:** Server chuyên biệt cấu hình CPU mạnh, cài đặt sẵn FFmpeg. Chỉ chạy supervisor để lắng nghe queue `media_processing`. Tuyệt đối không phục vụ Web API trên Node này.

Workflow Upload & Generate Preview:

```text
API Node (Nginx/PHP-FPM)
       │
Dispatch Job (queue=media_processing)
       │
   Redis Queue
       │
Media Node (Worker only + FFmpeg)
       │
    Complete
```

---

# 10. Cache Strategy

Hệ thống sử dụng **Redis** làm bộ nhớ đệm (Cache) nhằm giảm tải Database và tăng tốc độ phản hồi của các API thường xuyên được truy cập.

---

## 10.1 Cache Data

| Data | TTL | Mục đích |
|------|-----|----------|
| Genres | 24 giờ | Danh sách thể loại ít thay đổi |
| Trending Songs | 10 phút | Danh sách bài hát thịnh hành |
| Banners | 30 phút | Banner hiển thị trên trang chủ |
| Artist Analytics | 5 phút | Thống kê Dashboard của Artist |
| Song Detail | 15 phút | Thông tin chi tiết bài hát |
| Album Detail | 15 phút | Thông tin Album |
| Playlist Public | 10 phút | Playlist công khai |
| Search Suggestions | 5 phút | Gợi ý tìm kiếm phổ biến |

---

## 10.2 Cache Pattern

Hệ thống áp dụng **Cache Aside Pattern**.

Workflow:

```text
Client
   │
   ▼
Request API
   │
   ▼
Check Redis
   │
 ┌─┴─────────────┐
 │               │
 ▼               ▼
Hit             Miss
 │               │
 ▼               ▼
Return Cache   Query Database
                   │
                   ▼
             Save to Redis
                   │
                   ▼
             Return Response
```

Nguyên tắc

- Chỉ cache dữ liệu đọc nhiều (Read-heavy).
- Không cache dữ liệu có tính giao dịch.
- Dữ liệu hết hạn sẽ tự động được làm mới khi có Request tiếp theo.

---

## 10.3 Cache Invalidation Strategy (Cache Tags)

Hệ thống sử dụng **Cache Tags** của Laravel (Redis) để nhóm các Cache Key có liên kết logic với nhau. Điều này giúp Invalidate hàng loạt key liên quan chỉ với O(1) thay vì dùng vòng lặp dò tìm.

**Ví dụ Gán Tag:**
Khi cache bài hát, ta gán tag của bài hát, album và artist:
`Cache::tags(['song:123', 'album:45', 'artist:7'])->remember(...)`

| Kịch bản thay đổi (Action) | Xóa Tag (Invalidate) | Kết quả |
|---------|--------------|---------|
| Update tên Song 123 | `Cache::tags(['song:123'])->flush()` | Xóa chính xác cache của Song 123. |
| Update Cover Album 45 | `Cache::tags(['album:45'])->flush()` | Lập tức xóa cache của tất cả các bài hát thuộc Album 45. |
| Artist 7 đổi Avatar | `Cache::tags(['artist:7'])->flush()` | Lập tức xóa cache Profile Artist, mọi Album và mọi Song của Artist này. |
| Update Banner / Genre | Xóa tag `banners` / `genres` | Cập nhật lại giao diện. |

Nguyên tắc:
- Không sử dụng vòng lặp để xóa từng `song:{id}`.
- Không sử dụng lệnh xóa toàn bộ `Cache::flush()`.

---

## 10.4 Cache Key Convention

Để dễ quản lý và tránh trùng lặp, hệ thống thống nhất quy tắc đặt tên Cache Key.

```text
genres

song:{id}

album:{id}

playlist:{id}

artist:{id}:analytics

trending:songs

banner:homepage

search:suggestions
```

Ví dụ

```text
song:25

album:12

artist:5:analytics

playlist:18

trending:songs
```

---

## 10.5 Không Cache

Các dữ liệu sau **không được lưu Cache** nhằm đảm bảo tính chính xác theo thời gian thực:

- Authentication Token
- User Permission
- Listening History
- Stream Analytics
- Notification chưa đọc
- Queue Job Status

Những dữ liệu này luôn được đọc trực tiếp từ Database hoặc Queue.
---

# 11. Streaming Strategy

**Chiến lược Signed URL với Buffer Time (Chống gián đoạn):**
Vì file MP3 được bảo vệ trên MinIO và phát qua Signed URL có thời hạn (ví dụ: TTL = 1 tiếng), nếu URL hết hạn khi user đang nghe, việc phát nhạc sẽ vỡ (lỗi HTTP 403). 
➔ **Giải pháp:** Frontend Player (Pinia Store) sẽ thiết lập một `Buffer Time` (Ví dụ: trigger gọi lại API xin cấp URL mới ở phút 55 thay vì đợi đến phút 60), đảm bảo nhạc phát liên tục không bị giật lag.

```text
User (Frontend Player)
       │
       ▼
1. Yêu cầu nghe nhạc `[API-STREAM-01]`
       │
       ▼
2. Backend kiểm tra quyền (Premium/Free) & Sinh MinIO Signed URL (TTL 1h)
       │
       ▼
3. Trình duyệt tải Audio stream trực tiếp từ MinIO
       │
       ▼
(Phút 55: Trình duyệt tự động chạy ngầm renew Signed URL)
       │
       ▼
4. Sau khi nghe >30s hoặc >50% thời lượng ➔ Ghi nhận Stream (Gọi API Analytics)
```

---

# 12. Notification Architecture

Nguồn phát sinh:

- Song Approved
- Song Rejected
- New Release
- Follow Artist
- Report Resolved

Channel:

- Database
- Email
- Broadcast (Future)

---

# 13. Logging Strategy

Audit Log:

- Login
- Logout
- Upload Song
- Delete Song
- Ban User
- Approve Song

Application Log:

- Error
- Warning
- Exception

---

# 14. Security Architecture

Authentication

- Laravel Sanctum

Authorization

- Policy
- Gate
- Middleware

Protection

- SQL Injection
- XSS
- CSRF
- Rate Limit
- File Validation

---

# 15. Deployment Architecture

### Mermaid Diagram

```mermaid
flowchart TD

A[Browser]

A --> B[VueJS SPA]

B --> C[Laravel REST API]

C --> D[(MySQL)]

C --> E[(Redis)]

C --> F[(MinIO)]

E --> G[Queue Worker]

F --> H[Audio Storage]
```

---

# 16. Scalability Strategy

Hệ thống hỗ trợ mở rộng theo chiều ngang:

- Nhiều Web Server
- Redis dùng chung
- MinIO Cluster
- Database Replica (Future)

---

# 17. Backup Strategy

Database:

- Backup hàng ngày

Storage:

- Đồng bộ MinIO

Retention:

- 30 ngày

---

# 18. Monitoring

Theo dõi:

- CPU
- RAM
- Disk
- Queue
- Redis
- MySQL
- API Response Time

---

# 19. Disaster Recovery

Trong trường hợp xảy ra sự cố:

1. Khôi phục Database.
2. Khôi phục Storage.
3. Khởi động Queue Worker.
4. Kiểm tra Cache.
5. Đồng bộ dữ liệu.

---

# 20. Future Architecture

Có thể mở rộng:

- CDN
- Elasticsearch
- AI Recommendation Service
- Load Balancing
- Kubernetes
- Object Detection
- Music Recommendation Engine

## Roadmap

Phase 1

- Modular Monolith
- REST API
- MinIO
- Redis

↓

Phase 2

- CDN
- Elasticsearch
- AI Recommendation

↓

Phase 3

- Auto Scaling
- Load Balancing
- Kubernetes
- Distributed Cache

# 21. Luồng xử lý Streaming và Anti-cheat [FLOW-STREAM-01]

# 1. Pre-conditions
- User đang đăng nhập với role `Listener` hoặc `Artist`.
- Bài hát (Song) ở trạng thái `Approved`.

# 2. Luồng thực thi

```mermaid
sequenceDiagram
    actor U as Listener
    participant UI as Frontend
    participant API as API Server
    participant DB as Database
    
    U->>UI: Bấm Play bài hát
    UI->>API: Gọi API sinh Audio URL
    API-->>UI: Trả về Signed URL
    UI->>U: Bắt đầu phát nhạc
    
    note over U,UI: Nghe liên tục >= 30s
    UI->>API: POST /listener/stream/track/{id} (API-LST-04)
    API->>API: RULE-STREAM-01 (Check Anti-cheat)
    
    alt Gian lận (Cheat Detected)
        API-->>UI: 429 Too Many Requests (ERR-429-RATE)
    else Hợp lệ
        API->>DB: Insert vào `[DB-streams]`
        API-->>UI: 201 Created
    end
```