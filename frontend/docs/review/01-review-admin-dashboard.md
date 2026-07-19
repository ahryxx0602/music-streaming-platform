Hiện tại tôi không hài lòng với giao diện Admin Dashboard của dự án. Mặc dù kiến trúc code (Component, FSD, UI Primitives...) đang đi đúng hướng, nhưng Design Language của Admin chưa đạt kỳ vọng.

Điểm mấu chốt là:

Client (Music Streaming Platform) đang sử dụng Aurora Blue Design System với phong cách Modern, Premium, Futuristic, trong khi Admin Dashboard lại giống một template CRUD hoặc AdminLTE mặc định.

Điều này khiến toàn bộ hệ thống bị mất tính thống nhất về trải nghiệm.

Tôi không muốn chỉ chỉnh sửa từng component nhỏ, mà muốn thiết kế lại toàn bộ Design Language của khu vực Admin ngay từ bây giờ. Nếu tiếp tục phát triển thêm 30–40 màn hình trên nền UI hiện tại thì sau này việc refactor sẽ rất tốn thời gian và chi phí.

Mục tiêu

Hãy xem toàn bộ Admin như một sản phẩm B2B SaaS hiện đại.

Tham khảo phong cách thiết kế của:

Linear
Vercel Dashboard
Stripe Dashboard
GitHub Dashboard
shadcn/ui
Radix UI
Tremor

Yêu cầu:

Hiện đại
Premium
Functional
High Data Density
Dễ mở rộng
Đồng bộ với Aurora Blue Design System nhưng có Design Language riêng cho Admin.

Lưu ý: Không biến Admin thành giao diện Glassmorphism giống Client. Admin cần ưu tiên khả năng quản trị dữ liệu, mật độ thông tin và hiệu suất thao tác.

Những vấn đề cần giải quyết
1. Drawer chưa hòa vào Layout

Hiện tại giao diện đang có cảm giác:

Sidebar

↓

Content

↓

Drawer

là ba khối hoàn toàn tách biệt.

Drawer giống một Popup hơn là một phần của Dashboard.

Tôi muốn Drawer trở thành một phần tự nhiên của Layout.

Ví dụ:

┌─────────────────────────────────────────────┐
│ Sidebar │ Header                           │
│         │──────────────────────────────────│
│         │                                  │
│         │ Table                 Drawer     │
│         │                                  │
└─────────────────────────────────────────────┘

Drawer phải có cảm giác liền mạch với Dashboard.

2. Thiết kế lại Sidebar

Sidebar hiện tại khá giống AdminLTE.

Tôi muốn Sidebar hiện đại hơn.

Ví dụ:

LOGO

ahryxx

────────────

General

 Dashboard
 User Management
 Music
 Artist
 Albums
 Playlist

────────────

Moderation

 Reports
 Reviews

────────────

System

 Settings
 Permissions

Yêu cầu:

Có grouping.
Có section title.
Khoảng trắng hợp lý.
Icon đồng bộ.
Hover đẹp.
Active state nổi bật.
Có khả năng Collapse sau này.

Phong cách tham khảo:

Linear
Vercel
3. Sidebar Width

Sidebar hiện tại:

220px

khá chật.

Đề xuất:

260px

để menu thông thoáng hơn.

4. Sidebar Background

Không dùng nền đen đặc.

Đề xuất:

background:
linear-gradient(
180deg,
#111827,
#0F172A
);

Tông Slate kết hợp Aurora Blue.

5. Thiết kế lại Header

Không dùng:

Khu vực Quản trị Hệ thống

Tôi muốn Header theo kiểu:

Bên trái

Dashboard
/
User Management

Bên phải

Search
Notification
Theme Switch
User Profile

Phong cách:

Stripe Dashboard.

6. Thiết kế lại Content

Hiện tại

Title

↓

Tabs

↓

Filter

↓

Table

quá đơn giản.

Tôi muốn:

Page Header

↓

Statistics Cards

↓

Toolbar

↓

Table

Ví dụ:

Users

2,418

Artists

53

Staff

6

Các thống kê phải được đặt ở đầu trang.

7. Tabs

Tabs hiện tại quá nhạt.

Đề xuất:

Listener (2418)

Artist (53)

Staff (6)

Mỗi Tab có Badge hiển thị số lượng.

8. Search

Ô Search hiện tại quá ngắn.

Đề xuất:

340px ~ 380px

để phù hợp Dashboard.

9. Button

Button hiện tại quá bo góc.

Giống Mobile UI.

Đề xuất:

Border Radius

10px

không dùng:

18px

Button cần mạnh mẽ hơn.

10. Table

Đây là phần cần nâng cấp nhiều nhất.

Không chỉ hiển thị

Name

Email

Role

Mà nên có:

Avatar

↓

Name

↓

Role

↓

Status Badge

↓

Created Date

↓

Action

Ví dụ:

● Avatar

Taylor Swift

Artist

Verified

12/07/2026

⋯

Table cần có cảm giác "Premium SaaS".

11. Empty State

Không dùng:

Không có dữ liệu

Đề xuất:

🎵

No artists yet

Create your first artist

Có icon.

Có CTA.

Có hướng dẫn.

12. Drawer

Drawer hiện tại chỉ chứa Form.

Tôi muốn cấu trúc:

Header

────────────

General

Account

Permission

Avatar

────────────

Footer

Có Divider.

Header cố định.

Footer cố định.

Body Scroll.

13. Color System

Admin không nên sử dụng Aurora Blue quá nhiều.

Đề xuất:

95%

Slate

5%

Aurora Blue

Aurora Blue chỉ xuất hiện tại:

Primary Button
Active Menu
Focus Ring
Hyperlink
Active Badge
Loading
Progress

Không lạm dụng màu xanh.

14. Spacing

Hiện tại Dashboard khá chật.

Chuẩn hóa:

Page Padding

32px

Section Gap

24px

Card Padding

24px

Áp dụng thống nhất.

Nâng cấp Design System

Ngoài FSD hiện tại:

ui

shared

features

views

Tôi muốn xây dựng thêm một Admin Design System riêng:

admin/
    theme/
        tokens/
            colors.ts
            spacing.ts
            radius.ts
            shadow.ts
            typography.ts
            motion.ts

Tuyệt đối không hardcode màu sắc, spacing, radius hoặc shadow trong Component.

Toàn bộ phải sử dụng Design Tokens.

Layout mục tiêu
┌────────────────────────────────────────────────────────────────────┐
│ ahryxx                                                🔔 ⚙ 👤       │
├───────────────┬────────────────────────────────────────────────────┤
│               │ Dashboard / User Management                        │
│               │                                                    │
│ Dashboard     │ ┌────────┐ ┌────────┐ ┌────────┐                  │
│               │ │Users   │ │Artist  │ │Staff   │                  │
│ User          │ └────────┘ └────────┘ └────────┘                  │
│ Music         │                                                    │
│ Artist        │ Search______________ Filter_____ + New Artist      │
│ Albums        │                                                    │
│ Playlist      │ ┌──────────────────────────────────────────────┐   │
│               │ │ Avatar Name Email Status Date Action         │   │
│ Moderation    │ │                                              │   │
│               │ │                                              │   │
│ Settings      │ └──────────────────────────────────────────────┘   │
│               │                                                    │
└───────────────┴────────────────────────────────────────────────────┘
Yêu cầu khi thực hiện
Không sửa UI theo kiểu vá lỗi từng component.
Thiết kế lại Design Language của Admin theo một hệ thống thống nhất.
Giải thích trước khi code:
Những điểm chưa hợp lý của giao diện hiện tại.
Giải pháp thiết kế mới.
Danh sách Component cần refactor.
Design Tokens cần bổ sung.
Roadmap triển khai theo từng bước (Step 1 → Step N).
Chỉ sau khi thống nhất kiến trúc và giao diện mới thì mới bắt đầu refactor code.

Mục tiêu cuối cùng là tạo ra một Aurora Admin Design System chuyên biệt cho khu vực quản trị, có chất lượng tương đương các Dashboard SaaS hiện đại như Linear, Vercel hoặc Stripe, đồng thời vẫn giữ được sự đồng nhất thương hiệu với Aurora Blue Design System của ứng dụng client.