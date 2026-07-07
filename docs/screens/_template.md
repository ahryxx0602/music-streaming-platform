# SCREEN SPECIFICATION: [Tên Màn Hình]

## 1. Metadata
- **Tên màn hình:** [Tên hiển thị]
- **Screen ID:** `SCR-[DOMAIN]-[ID]` (VD: SCR-AUTH-001)
- **Role truy cập:** [Guest / Listener / Artist / Admin]
- **Trạng thái:** [Draft / Review / Approved]
- **Người viết:** [Tên/Email]
- **Ngày cập nhật:** [YYYY-MM-DD]

---

## 2. Overview & Flow
**Mục đích:**
[Mô tả ngắn gọn mục đích của màn hình này dùng để làm gì, mang lại giá trị gì cho user.]

**User Flow (In/Out):**
- **In:** Truy cập từ đâu (VD: Click nút "Đăng nhập" ở Header).
- **Out:** Thành công thì đi đâu (VD: Redirect về trang Chủ), Thất bại/Hủy thì đi đâu.

---

## 3. UI Reference & Layout Assembly
- **Figma / Prototype Link:** [URL]
- **Kiểu Layout sử dụng:** [MainLayout / AuthLayout / EmptyLayout]
- **Responsive / Breakpoints Note:** [Ghi chú hành vi thay đổi Layout ở Mobile/Tablet (VD: Sidebar ẩn thành Drawer, Grid 2 cột thành 1 cột)]
- **Ảnh chụp giao diện (Đánh số chú thích):**
  *(Chèn ảnh giao diện tại đây, các vùng quan trọng được đánh số 1, 2, 3... tương ứng với bảng bên dưới)*

---

## 4. UI Elements & Component Dictionary (Static Rules)

| ID | Tên UI | Tên Component | Data Source | Form Rules / Ràng buộc (Validation) |
| :--- | :--- | :--- | :--- | :--- |
| `#1` | [Ví dụ: Input Email] | `VTextField` / `BaseInput` | User Input | Required, Format: Email chuẩn, Max: 255. |
| `#2` | [Ví dụ: Btn Submit] | `BaseButton` (Primary) | N/A | Tắt (Disabled) nếu form chưa valid. |

---

## 5. Actions & Business Rules (Dynamic Logic)

| Action Name | Pre-conditions | Policy / Authorization | Business Rules (Quy tắc Frontend/Backend) | Expected Result & Event Tracking |
| :--- | :--- | :--- | :--- | :--- |
| [VD: Click "Xóa bài hát"] | Form hợp lệ | `User ID == Author ID` | 1. Frontend gọi API. 2. Backend check Auth... | - Thành công: Update `authStore`, chuyển trang.<br>- Tracking: `track('DELETE_SONG', { id: 123 })` |

---

## 6. Screen States (Trạng thái tổng thể)

- **State Management (Pinia/Vuex):** 
  [Xác định dữ liệu màn hình này là Local State hay Global State. VD: State sử dụng: `useAuthStore`, `usePlayerStore`...]
- **Default / Loading State:** 
  [Khi mới load trang thì gọi API nào? Hiện Skeleton hay Spinner? Disable những nút nào?]
- **Empty State (Trạng thái rỗng):** 
  [Nếu API trả về mảng rỗng (VD: Chưa có bài hát nào), hiển thị icon/text gì? Nút CTA điều hướng đi đâu?]
- **Error State (Trạng thái lỗi):** 
  [Xử lý khi HTTP 401 (Hết phiên), HTTP 422 (Lỗi Form), HTTP 500 hoặc mất kết nối mạng (Offline).]

---

## 7. API Mapping
Bảng liên kết các hành động trên UI với API Endpoints. Link đối chiếu sang tài liệu `04-api-documentation.md`.

| UI Action | HTTP Method | Endpoint | Payload (FE gửi lên) |
| :--- | :--- | :--- | :--- |
| [VD: Nút Login] | `POST` | `/api/v1/guest/auth/login` | `{ "email": "...", "password": "..." }` |
