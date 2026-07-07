---
name: system-architect-docs
description: "Kỹ năng chuyên biệt để viết và cấu trúc tài liệu hệ thống (System Architecture) theo chuẩn Cross-Referencing ID và Traceability Matrix."
---

# Kỹ năng: System Architect Documentation (Docs Writer)

Kỹ năng này cung cấp các nguyên tắc để Agent tạo ra các tài liệu đặc tả hệ thống có tính liên kết cao, tối ưu cho quy mô Enterprise.

## 1. Cấu trúc thư mục chuẩn (Must Follow)
* `docs/README.md`: Entry point
* `docs/id-convention.md`: Quy chuẩn ID
* `docs/screens/`: Chứa file định nghĩa cho từng màn hình riêng lẻ (vd: `SCR-001-login.md`)
* `docs/traceability-matrix.md`: Ma trận liên kết chéo tổng hợp toàn bộ dự án.
* Các file tài liệu gốc đánh số từ `01` đến `11`.

## 2. Tiêu chuẩn Đặt Mã định danh (ID Naming Convention)
* **Tính năng (Feature):** `[FEAT-<STT>]` (VD: `[FEAT-001]`)
* **Màn hình (Screen):** `[SCR-<STT>]` (VD: `[SCR-001]`)
* **API Endpoint:** `[API-<STT>]` (VD: `[API-001]`)
* **Validation Rule:** `[RULE-<STT>]` (VD: `[RULE-001]`)
* **Permission:** `[PER-<STT>]` (VD: `[PER-001]`)
* **Error Code:** `[ERR-<STT>]` (VD: `[ERR-001]`)
* **Bảng Database:** `[DB-<Tên_Bảng>]` (VD: `[DB-users]`)

## 3. Template cho Screen File (`docs/screens/_template.md`)
Mỗi file màn hình trong thư mục `screens/` phải tuân thủ chuẩn sau:
```markdown
# [SCR-XXX] Tên màn hình

## 1. Thông tin chung
- **Route:** `/duong-dan`
- **Layout:** `TenLayout`
- **Quyền truy cập (Permission):** `[PER-XXX]`

## 2. Dữ liệu & APIs (Data & APIs)
- **Lấy dữ liệu (Fetch):** `[API-XXX]`
- **Tương tác (Mutations):** `[API-YYY]`

## 3. Quy tắc nghiệp vụ (Business Rules & Validations)
- `[RULE-XXX]`: Mô tả logic.

## 4. Bảng dữ liệu liên quan (Database)
- `[DB-xxx]`
```

## Lời nhắc nhở cho AI (Agent Prompt)
Tuân thủ tuyệt đối cấu trúc thư mục và ID ở trên. Không được gộp chung các màn hình vào 1 file, phải tách rời vào thư mục `docs/screens/`.
