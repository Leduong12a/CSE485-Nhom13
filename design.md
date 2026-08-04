# 🎨 TÀI LIỆU THIẾT KẾ GIAO DIỆN HỆ THỐNG (UI/UX DESIGN SPECIFICATION)
## Hệ thống Helpdesk CNTT & Cơ sở vật chất — Trường Đại học Thủy Lợi (TLU Helpdesk)

Tài liệu này quy định chuẩn thiết kế giao diện người dùng (UI/UX), bố cục màn hình (Wireframes), hệ thống màu sắc, trạng thái UI đặc biệt và quy tắc tương tác cho 3 phân hệ người dùng dựa trên framework **Bootstrap 5** và **Bootstrap Icons**.

---

## 1. Hệ thống Thiết kế & Nguyên tắc Giao diện (Design System & Principles)

### 1.1. Bảng màu Chủ đạo (Color Palette — TLU Theme)
- **Brand Primary (Xanh Thủy Lợi)**: `#0d6efd` / `#004085` — Dùng cho Header, Primary Button, Sidebar active. (Đạt chuẩn độ tương phản WCAG 2.1 AA > 4.5:1).
- **Success (Hoàn thành / Đúng SLA)**: `#198754` — Dùng cho badge `RESOLVED`, `CLOSED`, chỉ số SLA an toàn.
- **Warning (Đang xử lý / Sắp hết SLA)**: `#ffc107` (Chữ đen `#212529`) — Dùng cho badge `IN_PROGRESS`, cảnh báo SLA còn dưới 25% thời gian.
- **Danger (Quá hạn SLA / Mức HIGH)**: `#dc3545` — Dùng cho ticket vi phạm SLA, ưu tiên `HIGH`.
- **Secondary / Mới tạo**: `#6c757d` / `#0dcaf0` — Dùng cho status `OPEN` hoặc thông tin bổ trợ.
- **Nền ứng dụng (Background)**: `#f8f9fa` (Xám nhạt) kết hợp Card nền trắng `#ffffff` tạo độ nổi và sạch sẽ.

### 1.2. Font chữ & Icons
- **Typography**: `Inter`, `Segoe UI`, system-ui.
- **Icons**: `Bootstrap Icons (BI)` (VD: `bi-ticket-detailed`, `bi-clock-history`, `bi-star-fill`, `bi-bell-fill`).

---

## 2. Kiến trúc Bố cục Màn hình chính (Layout Architecture)

### A. Layout Portal Người dùng (Requester Layout)
- **Top Navigation Bar**: Logo TLU Helpdesk + Menu nhanh (Trang chủ, Báo sự cố, Sự cố của tôi) + Notification Bell (Dropdown đếm tin chưa đọc) + Dropdown Avatar cá nhân.
- **Container**: Max-width `1200px` căn giữa màn hình, hiển thị dạng Card thoáng đãng, tối ưu tốt cho Mobile/Tablet.

### B. Layout Quản trị & Kỹ thuật (Staff & Admin Layout)
- **Collapsible Sidebar (Bên trái)**:
  - Header: Logo TLU + Vai trò (`Staff` / `Manager`).
  - Menu nhóm: Dashboard, Danh sách Ticket, Quản lý Nhân sự, Cấu hình SLA, Báo cáo Thống kê.
- **Top Bar Header**: Nút toggle thu gọn Sidebar (trên Mobile mở Offcanvas Drawer), Breadcrumb vị trí trang, Ô tìm kiếm nhanh ticket, User Profile menu.
- **Main Workspace**: Khu vực chứa nội dung tính năng chính.

---

## 3. Chi tiết Giao diện 3 Phân hệ Người dùng

### 📌 PHÂN HỆ 1: Sinh viên & Giảng viên (Requester Portal)

#### 1.1. Trang Tạo Ticket Báo Sự cố (Create Ticket — UC02)
- **Bố cục chính**: Split-card Form (Trái: Form nhập dữ liệu; Phải: Hướng dẫn & Lưu ý hỗ trợ).
- **Thành phần UI**:
  - **Tiêu đề phiếu**: Input `form-control-lg` với placeholder rõ ràng.
  - **Loại sự cố (Category)**: Select dropdown nhóm theo biểu tượng (Mạng Wi-Fi, Máy chiếu, Đăng ký môn học...).
  - **Mức độ ưu tiên**: Radio Pills chọn nhanh (`Thấp`, `Trung bình`, `Cao`).
  - **Khu vực Tải ảnh minh chứng (Multi-upload)**:
    - Khung Drag & Drop tải nhiều ảnh (.png, .jpg, .pdf).
    - Thumbnail preview ảnh đính kèm có nút `[x]` để xóa ảnh trước khi gửi.
  - **Nút bấm**: `[Gửi yêu cầu hỗ trợ]` (Button Primary kích thước lớn).

#### 1.2. Trang Danh sách Ticket cá nhân (My Tickets — UC03)
- **Thanh Công cụ (Filter Bar)**:
  - Ô tìm kiếm theo Mã ticket / Tiêu đề.
  - Bộ lọc trạng thái: All | Mới gửi | Đang xử lý | Đã xong.
  - Nút `[+ Tạo ticket mới]` nổi bật góc trên bên phải.
- **Bảng dữ liệu Ticket (Responsive Table)**:
  - Cột: `Mã Ticket`, `Tiêu đề sự cố`, `Danh mục`, `Mức ưu tiên`, `Trạng thái (Badge colored)`, `Ngày gửi`, `Thao tác`.
  - Hỗ trợ xem dạng Card View khi xem trên điện thoại.

#### 1.3. Trang Chi tiết Ticket, Trao đổi & Mở lại Ticket (UC04, UC05)
- **Cột trái (Nội dung sự cố & Tiến độ)**:
  - **Stepper Trạng thái (Timeline Progress)**: Thanh tiến độ trực quan thể hiện 4 bước: `Mới gửi` $\rightarrow$ `Đang xử lý` $\rightarrow$ `Đã khắc phục` $\rightarrow$ `Đóng phiếu`.
  - **Thông tin bài viết**: Tiêu đề, Mô tả sự cố, thông tin người báo lỗi.
  - **Gallery Ảnh minh chứng**: Danh sách ảnh dạng Grid Thumbnail, bấm vào để xem phóng to (Lightbox Modal).
  - **Nút Mở lại Ticket (Reopen Ticket)**: Nếu ticket ở trạng thái `RESOLVED` hoặc `CLOSED` nhưng người dùng chưa hài lòng, hiển thị nút `[⚠️ Mở lại sự cố]` kèm Modal bắt buộc nhập lý do chưa hài lòng (chuyển ticket quay về `OPEN`).
  - **Khảo sát 5 Sao (Satisfaction Survey)**: Khi ticket chuyển `RESOLVED`, hiển thị Card đánh giá nổi bật gồm 5 ngôi sao tương tác (`1-5 stars`) + Ô nhập lời nhắn cảm ơn/góp ý.
- **Cột phải (Khung Chat Trao đổi)**:
  - Khung hội thoại dạng Message Stream với Kỹ thuật viên phụ trách.
  - Khung nhập tin nhắn + nút gửi đính kèm tệp tiện lợi.

---

### 📌 PHÂN HỆ 2: Cán bộ Kỹ thuật (IT Staff Workspace)

#### 2.1. Trang Công việc được giao (Assigned Workdesk — UC06, UC09)
- **Thanh Đếm ngược SLA (SLA Countdown Bar)**:
  - Mỗi ticket hiển thị đồng hồ đếm ngược thời gian cam kết còn lại (`02 giờ 15 phút`).
  - Đổi màu nhãn SLA: Green (>50% thời gian), Yellow (còn <25%), Red Flashing (Đã quá hạn / SLA Overdue).
- **Chế độ xem linh hoạt**:
  - **Dạng Bảng (Table List)**: Sắp xếp theo mức độ ưu tiên và thời gian SLA còn lại ngắn nhất lên đầu.
  - **Dạng Thẻ (Kanban Board)**: Drag & drop chuyển nhanh giữa các cột `Cần xử lý`, `Đang xử lý`, `Đã xong`.

#### 2.2. Trang Xử lý & Cập nhật Trạng thái Ticket (UC07, UC08)
- **Thanh Action nhanh (Quick Action Header)**:
  - Nút bấm đổi trạng thái 1-click: `[▶ Bắt đầu xử lý]` `[✔ Đã khắc phục]` `[🔒 Đóng ticket]`.
- **Khung Ghi chú Chuyên môn**: Xem ghi chú chỉ đạo từ Trưởng bộ phận khi giao việc.
- **Nhật ký Chuyển trạng thái (Status Log Timeline)**: Danh sách log lịch sử bất biến (Ai chuyển, Trạng thái cũ $\rightarrow$ Mới, Thời gian chính xác từng giây).

---

### 📌 PHÂN HỆ 3: Quản trị viên & Trưởng bộ phận (Manager Dashboard)

#### 3.1. Trang Tổng quan Báo cáo & Thống kê (Analytics Dashboard — UC14)
- **Top Stat Cards (KPIs)**:
  - Thẻ 1: Tổng số Ticket trong tháng (Icon `bi-ticket-perforated`).
  - Thẻ 2: Tỷ lệ hoàn thành đúng SLA (VD: `94.5%` — Color Success).
  - Thẻ 3: Ticket quá hạn chưa xong (VD: `3` — Color Danger).
  - Thẻ 4: Điểm hài lòng trung bình (VD: `4.8 / 5.0 ⭐`).
- **Biểu đồ Thống kê (Charts Area)**: Biểu đồ cột theo Danh mục sự cố và Biểu đồ tròn theo Khoa/Phòng ban.

#### 3.2. Trang Phân công & Quản lý Nhân sự (Assign Staff — UC10, UC12)
- **Modal Phân công Kỹ thuật viên**: Hiển thị danh sách KTV kèm chuyên môn, ca trực, số ticket đang giữ + Nút `[Phân công ngay]`.

#### 3.3. Trang Cấu hình Danh mục & SLA (Category Config — UC11)
- Bảng cấu hình danh mục lỗi và thời gian xử lý tối đa (`sla_hours`).

#### 3.4. Trang Báo cáo Vi phạm SLA (SLA Overdue Report — UC13)
- Bảng danh sách các sự cố bị trễ hạn xử lý, highlighted màu đỏ nổi bật + Nút `[Đôn đốc]`/`[Chuyển KTV khẩn cấp]`.

---

## 4. Luồng Giao diện Bổ sung (Auth, Notifications & Profile Flows)

### 4.1. Màn hình Đăng nhập & Quên mật khẩu (Auth — UC01)
- **Đăng nhập Email TLU**: Cho phép đăng nhập qua Email trường `@st.tlu.edu.vn` (Sinh viên) hoặc `@tlu.edu.vn` (Giảng viên/KTV).
- **Quên mật khẩu**: Form nhập email nhận link khôi phục mật khẩu.

### 4.2. Màn hình Hồ sơ Cá nhân & Đổi Mật khẩu
- Hiển thị thông tin họ tên, email, phòng ban.
- Form đổi mật khẩu bảo mật (Mật khẩu cũ, Mật khẩu mới, Xác nhận mật khẩu).

### 4.3. Trung tâm Thông báo (Notification Center Popover)
- Bấm vào icon Bell trên Top bar mở Popover 5 thông báo mới nhất.
- Nút `[Đánh dấu tất cả đã đọc]` và liên kết `[Xem tất cả thông báo]`.
- Phân loại thông báo: 🔵 Phân công công việc mới | 🟢 Trạng thái ticket được cập nhật | 💬 Có bình luận mới.

---

## 5. Quy chuẩn Trạng thái Giao diện (UI States & Feedbacks)

| Trạng thái UI | Quy cách thiết kế trên Giao diện Bootstrap |
| :--- | :--- |
| **Empty State (Chưa có dữ liệu)** | Hiển thị Card căn giữa với Icon rỗng nhạt (`bi-inbox-fill` 48px) + Tiêu đề *"Chưa có sự cố nào"* + Nút gợi ý `[+ Báo sự cố ngay]`. |
| **Loading State (Đang tải)** | • **Table/Card**: Hiển thị **Skeleton Loaders** (Thanh phôi xám nhấp nháy `placeholder-glow`).<br>• **Button Submit**: Hiển thị Spinner `spinner-border-sm` và vô hiệu hóa nút (`disabled`). |
| **Error / Validation State** | • **Input Form**: Viền đỏ `is-invalid` + Dòng chữ nhỏ màu đỏ `invalid-feedback` giải thích lý do lỗi bên dưới.<br>• **Global Error**: Hiển thị **Toast Notification** đỏ góc trên bên phải khi mất mạng hoặc upload file thất bại. |
| **Pagination (Phân trang)** | Sử dụng component `pagination` của Bootstrap 5 bên dưới bảng dữ liệu (Cho phép chọn `10`, `25`, `50` dòng/trang + Nút `Trang trước`, `Trang sau`). |

---

## 6. Giới hạn Kỹ thuật UX & Quy tắc Vận hành (Technical UX Rules)

1. **Quy định Upload Tệp đính kèm**:
   - Tối đa **5 file** / ticket.
   - Dung lượng tối đa **5MB / file**.
   - Định dạng chấp nhận: `.jpg`, `.jpeg`, `.png`, `.pdf`.
2. **Quy tắc Tính đồng hồ SLA**:
   - SLA đếm ngược theo **Giờ hành chính** (8:00 – 17:00, từ Thứ 2 đến Thứ 6) hoặc **24/7** tùy cấu hình danh mục. Đồng hồ tạm dừng khi ticket chuyển trạng thái chờ thông tin từ Requester.
3. **Responsive Breakpoints (Bootstrap 5 Standard)**:
   - **Mobile (`< 576px`)**: Bảng chuyển sang Card View stack đứng. Sidebar thu thành Offcanvas Drawer trượt từ bên trái.
   - **Tablet (`576px – 992px`)**: Sidebar thu gọn icon-only.
   - **Desktop (`≥ 992px`)**: Bố cục 2 cột cố định thoáng đãng.
4. **Quy tắc Xử lý Khảo sát Hài lòng**:
   - Nếu user bỏ qua không khảo sát 5 sao khi đóng ticket, hệ thống hiển thị Banner nhắc nhở nhẹ màu vàng ở đầu trang Chi tiết ticket, không chặn các thao tác khác.

---

## 7. Phân quyền Giao diện & Accessibility (Role Matrix & WCAG)

### 7.1. Ma trận Phân quyền Giao diện (Role-based UI Matrix)

| Chức năng / Màn hình | REQUESTER (Sinh viên/GV) | STAFF (Kỹ thuật viên) | MANAGER (Trưởng bộ phận) |
| :--- | :---: | :---: | :---: |
| **Tạo Ticket & Xem ticket cá nhân** | ✅ Có | ✅ Có | ✅ Có |
| **Nhắn tin / Đánh giá 5 sao** | ✅ Có | ✅ Có | ✅ Có |
| **Xem Kanban / Workdesk nhóm** | ❌ Không | ✅ Có | ✅ Có (Toàn quyền) |
| **Đổi trạng thái Ticket** | ❌ Không | ✅ Có | ✅ Có |
| **Phân công / Giao lại Ticket** | ❌ Không | ❌ Không | ✅ Có |
| **Báo cáo Thống kê & SLA** | ❌ Không | ❌ Không | ✅ Có |
| **Cấu hình Danh mục SLA & User** | ❌ Không | ❌ Không | ✅ Có |

### 7.2. Khả năng truy cập (Accessibility - WCAG 2.1 AA)
- Tất cả các nút bấm và link đều hỗ trợ **Focus Outline** (`focus-visible`) khi duyệt bằng bàn phím (phím Tab).
- Tỷ lệ tương phản màu sắc chữ trên nền luôn đạt từ `4.5:1` trở lên.
