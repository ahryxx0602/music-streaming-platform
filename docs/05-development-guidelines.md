# HƯỚNG DẪN PHÁT TRIỂN & TIÊU CHUẨN (DEVELOPMENT GUIDELINES)

Tài liệu này tổng hợp các tiêu chuẩn phát triển, viết tài liệu và quản lý thay đổi (Đã được hợp nhất từ 11 file tiêu chuẩn).



# Documentation Standard (Core Manifesto)

Tài liệu này định nghĩa các nguyên tắc cốt lõi, kiến trúc thư mục tổng thể và quy tắc đặt tên cho hệ thống tài liệu.

## 1. Triết lý xây dựng tài liệu
- **AI-First Documentation**: Định dạng máy dễ phân tích (machine-readable) trước khi tối ưu cho người đọc, giảm token lãng phí.
- **Single Source of Truth (SSOT)**: Một nghiệp vụ/quy tắc chỉ định nghĩa tại một nơi duy nhất.
- **Mapping Driven**: Kết nối các thực thể phân tán thông qua đồ thị liên kết tĩnh (ID).
- **No-Assumption (Không giả định)**: Bất kỳ quy tắc nào cũng định nghĩa dứt khoát.

## 2. Kiến trúc thư mục (Directory Structure)
```text
docs/
├── standards/            # [01-11] Chứa toàn bộ tiêu chuẩn của dự án
├── screens/              # Tài liệu chi tiết UI/UX (Prefix: SCR)
├── apis/                 # Tài liệu đặc tả API (Prefix: API)
├── databases/            # Đặc tả cấu trúc lưu trữ (Prefix: DB)
├── flows/                # Sơ đồ luồng nghiệp vụ (Prefix: FLOW)
├── rules/                # Quy tắc, Policy, Validation (Prefix: RULE)
├── README.md             # Entry point
├── 03-system-architecture.md # Kiến trúc kỹ thuật cấp cao
└── traceability-matrix.md    # Ma trận truy vết các ID
```

## 3. Quy tắc đặt tên tài liệu (Naming Convention)
- **Định dạng file**: Bắt buộc `kebab-case`, chữ thường, đuôi `.md` (VD: `user-profile.md`).
- **Tài liệu mức hệ thống**: Sử dụng tiền tố số thứ tự (01, 02, 03...) tại root để AI xác định trình tự nạp ngữ cảnh.
- **Tài liệu chi tiết**: Đặt trong các folder tương ứng. Tên file chính là mã định danh (ID) tương ứng (VD: `SCR-INV-01.md`). KHÔNG dùng số thập phân để mở rộng.


# Metadata Standard

Mọi file `.md` tài liệu chi tiết bắt buộc phải khởi đầu bằng YAML Frontmatter. AI sẽ parse block này để trích xuất meta-context với O(1).

## 1. Cấu trúc YAML chuẩn

```yaml
---
id: [Mã định danh]
title: [Tên hiển thị]
version: 1.0.0
status: DRAFT | REVIEW | APPROVED | DEPRECATED
owner: [Người/Role chịu trách nhiệm]
reviewer: [Người duyệt]
review_date: YYYY-MM-DD
last_update: YYYY-MM-DD
priority: LOW | MEDIUM | HIGH | CRITICAL
feature: [ID Feature cha]
domain: [Nhóm nghiệp vụ]
tags: [tag1, tag2]
deprecated_since: [Version/Ngày nếu bị loại bỏ]
source_of_truth: [ID tài liệu rễ]
related_docs:
  - [ID 1]
  - [ID 2]
---
```
*(Bắt buộc chèn Table of Contents ngay sau khối yaml)*

## 2. Ý nghĩa và Ràng buộc

- **status**: AI chỉ được phép sinh code cho tài liệu ở trạng thái `APPROVED`.
- **reviewer / review_date**: Có thể bỏ trống nếu status là `DRAFT`.
- **feature**: Để trống nếu file này đóng vai trò là một Feature gốc.
- **deprecated_since**: Bắt buộc phải có giá trị nếu status là `DEPRECATED`.
- **related_docs**: Bắt buộc có dạng mảng, chứa các ID liên kết tĩnh. Nếu không có liên kết nào, để rỗng `[]`.


# ID Convention

Tài liệu định nghĩa hệ thống mã định danh (ID) duy nhất cho mọi thành phần trong dự án.

## 1. Bảng định danh (Prefix)

| Entity | Ý nghĩa | Tiền tố | Cấu trúc chuẩn | Ví dụ |
|---|---|---|---|---|
| **FEATURE** | Tính năng tổng thể | `FEAT` | `FEAT-[Module]` | `FEAT-INVENTORY` |
| **FLOW** | Luồng nghiệp vụ | `FLOW` | `FLOW-[Module]-[STT]` | `FLOW-AUTH-01` |
| **SCR** | Màn hình/Giao diện | `SCR` | `SCR-[Module]-[STT]` | `SCR-INV-01` |
| **CMP** | UI/System Component | `CMP` | `CMP-[Type]-[Name]` | `CMP-BTN-PRIMARY` |
| **API** | Điểm truy cập Backend | `API` | `API-[Entity]-[STT]` | `API-USR-01` |
| **DB** | Table/Entity Database | `DB` | `DB-[Table_Name]` | `DB-USERS` |
| **RULE** | Rule/Validation logic | `RULE` | `RULE-[Entity]-[STT]` | `RULE-PWD-01` |
| **PER** | Permission/Authorization | `PER` | `PER-[Entity]-[Action]` | `PER-INV-VIEW` |
| **ERR** | Error Code / Exception | `ERR` | `ERR-[Module]-[Code]` | `ERR-AUTH-401` |

## 2. Vòng đời và Trạng thái ID (State Machine)

1. **Khởi tạo**: Khởi tạo ID ngay lập tức khi một thực thể mới hình thành (VD: tạo bảng DB phải cấp DB ID).
2. **Tái sử dụng**: Dùng lại ID thông qua tham chiếu ở bất cứ đâu xuất hiện nghiệp vụ tương tự.
3. **Bất biến**: KHÔNG BAO GIỜ đổi ID khi đã map vào source code hoặc tài liệu khác.
4. **Hợp nhất (Merge) / Chia tách (Split)**: Nếu nghiệp vụ bị gộp hoặc tách, tạo ID mới hoàn toàn.
5. **Deprecated (Phế truất)**: Chuyển metadata status sang `DEPRECATED` cho các ID cũ. Không xóa hẳn file để giữ lịch sử.


# Writing Guideline

Quy chuẩn về cách thức trình bày và mô tả logic nghiệp vụ để đảm bảo con người dễ đọc, máy dễ hiểu.

## 1. Văn phong và Ngôn ngữ

- **Tính chất**: Kỹ thuật, mệnh lệnh cách, dứt khoát. Dùng thể chủ động.
- **Giới hạn độ dài**: Giới hạn câu đơn, tránh viết đoạn văn lồng ghép đa nghĩa.
- **Cấm kỵ**: Tuyệt đối không dùng từ cảm tính, ước lượng ("có thể", "khoảng", "có vẻ", "nên").

## 2. Mô tả Requirement (BDD - Given/When/Then)

Bắt buộc dùng BDD cho các luồng Use Case:
- **Sai**: Nếu nhập sai password thì chặn.
- **Đúng**: **WHEN** user nhập `password` sai quá 3 lần -> **THEN** hệ thống kích hoạt khóa tài khoản, trả về mã lỗi `ERR-AUTH-429`.

## 3. Mô tả Logic (Decision Table)

Với các logic rẽ nhánh phức tạp (nhiều điều kiện AND/OR), BẮT BUỘC dùng Decision Table:
| Điều kiện 1 | Điều kiện 2 | Kết quả |
|---|---|---|
| Role Admin | Đã Verify | Cho phép truy cập |

## 4. Mô tả Kiến trúc & Flow

- BẮT BUỘC dùng thẻ block code ``mermaid`` để vẽ biểu đồ Sequence, Flowchart, ERD.
- Không dùng ảnh chụp màn hình chèn vào tài liệu Flow vì AI không parse được nội dung trong ảnh tĩnh.


# Mapping Guideline

Định nghĩa cấu trúc đồ thị liên kết tĩnh (Topology) và định dạng Traceability Matrix.

## 1. Sơ đồ liên kết cốt lõi

Luồng tư duy tuyến tính đồ thị:
`FEATURE` → `FLOW` → `SCR` → `CMP` → `API` → (`DB` + `RULE` + `PER`) → `ERR`

*Quy tắc áp dụng*: Chỉ chèn ID vào tài liệu để tham chiếu, tuyệt đối không mô tả lại nội dung của ID đó ở file khác.

## 2. Ràng buộc Hard-link
- Một `API` bắt buộc phải map với ít nhất một `DB` (để truy xuất/lưu trữ), một `RULE` (để validate) và khai báo các `ERR` trả về.
- Một `SCR` phải map với ít nhất một `API` và sử dụng các `CMP`.

## 3. Traceability Matrix (`traceability-matrix.md`)
- Là file trung tâm dạng Bảng (Markdown Table hoặc CSV) chứa toàn bộ ma trận liên kết ngang của dự án.
- **Broken Link & Orphan ID**: Cấm tồn tại các ID mồ côi (không nằm trong ma trận) hoặc Link gãy (có định danh trong ma trận nhưng file Markdown `.md` không tồn tại).


# Template Standard

Quy định cấu trúc phân tầng Section (Hierarchy) bắt buộc cho từng loại Spec. Không dùng template mẫu điền tay, đây là bộ khung bắt buộc.

## 1. Screen Spec (`SCR`)
1. Metadata (YAML Frontmatter)
2. UI Layout (Mô tả cấu trúc/Mockup text)
3. UI Components (Danh sách `CMP-xxx`)
4. API Integration (Danh sách `API-xxx`)
5. State & Triggers (Luồng sự kiện trên giao diện)

## 2. API Spec (`API`)
1. Metadata
2. Method & Endpoint
3. Authorization & Permission (`PER-xxx`)
4. Request Payload & Validation (`RULE-xxx`)
5. Response Schema (Map với `DB-xxx`)
6. Exception Handling (`ERR-xxx`)

## 3. Database Spec (`DB`)
1. Metadata
2. Table Definition (Tên bảng, Engine)
3. Columns (Tên, Kiểu dữ liệu, PK/FK, Constraints)
4. Indexes (Các chỉ mục tối ưu)
5. Relationships (Liên kết One-to-Many, Many-to-Many)

## 4. Business Flow (`FLOW`)
1. Metadata
2. Pre-conditions (Điều kiện tiên quyết)
3. Post-conditions (Trạng thái kết thúc)
4. Flow Diagram (Mermaid code)
5. Step-by-step Technical Logic

## 5. Validation / Rule (`RULE`)
1. Metadata
2. Error ID Mapping (Trường hợp fail thì trả `ERR` nào)
3. Decision Table / Điều kiện IF-THEN


# Change Management Standard

Định nghĩa Workflow cập nhật tài liệu kiểu "Thác nước" (Waterfall) khi có thay đổi nghiệp vụ.

## 1. Trình tự cập nhật bắt buộc (Waterfall)

`Requirement` → `FLOW` → `SCR` → `API` → `DB` → `RULE / PER / ERR` → `Traceability Matrix`

*Giải thích*: Flow định hình trải nghiệm. Giao diện (Screen) vẽ theo Flow. API được sinh ra để phục vụ Screen. DB lưu trữ theo chuẩn API. Trình tự này ngăn ngừa "Ảo giác" cho AI khi cập nhật gián đoạn.

## 2. Phân loại mức độ thay đổi

- **Thay đổi toàn bộ (Full Update)**: Áp dụng khi Business Flow rẽ nhánh mới (thêm luồng phụ, bỏ luồng chính). Phải đi từ `FLOW` xuống cuối con đường.
- **Bỏ qua bước (Nhảy cóc - Jump)**: 
  - Nếu chỉ đổi nội dung câu chữ thông báo lỗi: Chỉ cần update `ERR`.
  - Nếu chỉ thắt chặt Regex/Rule: Chỉ update `RULE`.
  - Tuyệt đối giữ đồng bộ Traceability Matrix sau mọi cú nhảy.


# Review Standard

Đóng vai trò là "Cổng gác" (Gatekeeper). Tài liệu chỉ được đổi status sang `APPROVED` khi vượt qua các Checklist này. AI và Reviewer phải dùng text-search/grep để verify.

## 1. Global / Metadata Checklist
- [ ] Dòng đầu tiên của file có chứa chuẩn YAML block chưa?
- [ ] Thuộc tính `status` đã được điền hợp lệ chưa?
- [ ] Mảng `related_docs` có chứa "Broken Link" nào không? Đã tạo file `.md` cho các ID đó chưa?

## 2. API Review Checklist
- [ ] API đã link tới ít nhất một `PER` (Quyền) chưa?
- [ ] Request payload đã có `RULE` validation cụ thể chưa?
- [ ] Cấu trúc Response trả về có khớp với các cột định nghĩa trong `DB` không?
- [ ] Có mã `ERR` nào được throw ra mà chưa khai báo trong docs không?

## 3. Screen Review Checklist
- [ ] Các hành động người dùng (Button click, form submit) đã trỏ tới đúng `API` chưa?
- [ ] Màn hình đã import đầy đủ danh sách `CMP` tái sử dụng chưa?
- [ ] Input form trên UI có tuân thủ các `RULE` không?


# Versioning Standard

Quy chuẩn Semantic Versioning (SemVer) áp dụng riêng cho Tài liệu (Documentation), độc lập với source code.

## 1. MAJOR (`X.0.0`) - Breaking Changes
Tăng MAJOR khi có thay đổi nghiệp vụ gây vỡ cấu trúc và cần sửa đổi diện rộng.
- *Ví dụ*: Đổi luồng thanh toán (FLOW) từ 1 bước sang 3 bước.
- *Ví dụ*: Đổi Data Type của PK từ Integer sang UUID.
- *Hành động*: Buộc phải cập nhật tất cả file liên đới trong sơ đồ Traceability.

## 2. MINOR (`0.X.0`) - Backward Compatible
Tăng MINOR khi thêm logic mới nhưng không phá vỡ logic cũ.
- *Ví dụ*: Thêm một column optional vào Database (`DB`).
- *Ví dụ*: Bổ sung một bộ lọc vào Màn hình (`SCR`).
- *Ví dụ*: Định nghĩa thêm một mã lỗi `ERR` mới.

## 3. PATCH (`0.0.X`) - Non-logic Changes
Tăng PATCH cho công tác bảo trì nội dung.
- *Ví dụ*: Sửa lỗi chính tả.
- *Ví dụ*: Định dạng lại bảng, vẽ lại sơ đồ Mermaid cho đẹp.
- *Ví dụ*: Fix lỗi syntax trong file YAML.


# Glossary Standard

Đóng vai trò "Lá chắn" bảo vệ AI và Developer khỏi sự nhập nhằng ngôn ngữ.

## 1. Cấu trúc một Từ vựng
Mỗi entry trong `glossary-standard` (hoặc file từ điển chung) phải tuân theo cấu trúc:
- **Term**: Tên thuật ngữ gốc (Ví dụ: `Purchase Order`).
- **Alias**: Tên gọi tắt dùng trong docs và code (Ví dụ: `PO`).
- **Forbidden Words (Từ cấm)**: Các từ tuyệt đối cấm dùng (Ví dụ: cấm dùng chữ `Order` đơn lẻ vì sẽ nhầm với `Sales Order`).
- **Synonyms (Từ đồng nghĩa)**: Buộc quy về Term gốc (Ví dụ: `Basket`, `Cart` -> Chỉ dùng `Cart`).
- **Translation (Bản dịch Anh/Việt)**: Map 1-1 chặt chẽ (Ví dụ: UI hiện "Đơn mua" -> ID/Code phải là `PurchaseOrder`).

## 2. Cập nhật Từ điển
- Mọi thuật ngữ mới phát sinh phải được bổ sung vào đây trước tiên.
- AI khi review tài liệu nếu phát hiện "Forbidden Words" sẽ đánh fail tự động.


# AI Agent Guideline

Chỉ thị sinh tồn (Minimal Directives) dành cho các AI Coding Agent (Antigravity, Claude, Codex).

## 1. Trình tự Nạp Ngữ Cảnh (Bootstrapping)
Trước khi lập trình, AI BẮT BUỘC đọc:
1. `README.md`
2. `03-system-architecture.md`
3. `traceability-matrix.md`
4. Các file `01` đến `11` trong thư mục `standards/`.

## 2. Tiền kiểm (Pre-Code Check)
- Khi nhận yêu cầu xử lý một `ID` (VD sửa `API-xxx`), phải `grep_search` `ID` đó để tìm các tài liệu phụ thuộc. Nạp toàn bộ graph đó vào bối cảnh.

## 3. Cập nhật Tài liệu (Post-Code Update)
- Bất kỳ thay đổi logic code nào cũng **MẶC ĐỊNH** yêu cầu cập nhật lại file `.md` chứa ID. KHÔNG ĐƯỢC để code và tài liệu out-of-sync.

## 4. Phân tích tác động (Impact Analysis)
- Đổi DB schema? AI bắt buộc quét toàn bộ codebase + docs để báo cáo các `API/SCR` vỡ logic. Đợi user xác nhận mới alter.

## 5. Chống suy diễn (Fail-Fast Boundary)
- Thiếu `RULE`, thiếu mã `ERR`? Tuyệt đối KHÔNG ĐƯỢC tự suy diễn giả định. Dừng tiến trình và bắt buộc đặt câu hỏi làm rõ cho người dùng.
- AI không được phép xóa (delete) bất kỳ file ID nào trừ khi User dùng lệnh xóa/deprecated rõ ràng.
