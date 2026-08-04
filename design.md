# 🎨 TÀI LIỆU THIẾT KẾ GIAO DIỆN HỆ THỐNG (UI/UX DESIGN SPECIFICATION)
## Hệ thống Helpdesk CNTT & Cơ sở vật chất — Trường Đại học Thủy Lợi (TLU Helpdesk)

Tài liệu này quy định chuẩn thiết kế giao diện người dùng (UI/UX), bố cục màn hình (Wireframes), hệ thống màu sắc và trải nghiệm tương tác cho 3 phân hệ người dùng dựa trên framework **Bootstrap 5** và **Bootstrap Icons**.

---

## 1. Hệ thống Thiết kế & Nguyên tắc Giao diện (Design System & Principles)

### 1.1. Bảng màu Chủ đạo (Color Palette — TLU Theme)
- **Brand Primary (Xanh Thủy Lợi)**: `#0d6efd` / `#004085` — Dùng cho Header, Primary Button, Sidebar active.
- **Success (Hoàn thành / Đúng SLA)**: `#198754` — Dùng cho badge `RESOLVED`, `CLOSED`, chỉ số SLA an toàn.
- **Warning (Đang xử lý / Sắp hết SLA)**: `#ffc107` — Dùng cho badge `IN_PROGRESS`, cảnh báo SLA còn dưới 25% thời gian.
- **Danger (Quá hạn SLA / Mức HIGH)**: `#dc3545` — Dùng cho ticket vi phạm SLA, ưu tiên `HIGH`.
- **Secondary / Mới tạo**: `#6c757d` / `#0dcaf0` — Dùng cho status `OPEN` hoặc thông tin bổ trợ.
- **Nền ứng dụng (Background)**: `#f8f9fa` (Xám nhạt) kết hợp Card nền trắng `#ffffff` tạo độ nổi và sạch sẽ.

### 1.2. Font chữ & Icons
- **Typography**: `Inter`, `Segoe UI`, system-ui.
- **Icons**: `Bootstrap Icons (BI)` (VD: `bi-ticket-detailed`, `bi-clock-history`, `bi-star-fill`).

---

## 2. Kiến trúc Bố cục Màn hình chính (Layout Architecture)

### A. Layout Portal Người dùng (Requester Layout)
- **Top Navigation Bar**: Logo TLU Helpdesk + Menu nhanh (Trang chủ, Báo sự cố, Sự cố của tôi) + Notification Bell + Dropdown Avatar cá nhân.
- **Container**: Max-width `1200px` căn giữa màn hình, hiển thị dạng Card thoáng đãng, tối ưu tốt cho Mobile/Tablet.

### B. Layout Quản trị & Kỹ thuật (Staff & Admin Layout)
- **Collapsible Sidebar (Bên trái)**:
  - Header: Logo TLU + Vai trò (Staff / Admin).
  - Menu nhóm: Dashboard, Danh sách Ticket, Quản lý Nhân sự, Cấu hình SLA, Báo cáo Thống kê.
- **Top Bar Header**: Nút toggle thu gọn Sidebar, Breadcrumb vị trí trang, Ô tìm kiếm nhanh ticket, User Profile menu.
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
    - Khung Drag & Drop tải nhiều ảnh (.png, .jpg).
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

#### 1.3. Trang Chi tiết Ticket & Trao đổi Hai chiều (Ticket Detail & Discussion — UC04, UC05)
- **Cột trái (Nội dung sự cố & Tiến độ)**:
  - **Stepper Trạng thái (Timeline Progress)**: Thanh tiến độ trực quan thể hiện 4 bước: `Mới gửi` $\rightarrow$ `Đang xử lý` $\rightarrow$ `Đã khắc phục` $\rightarrow$ `Đóng phiếu`.
  - **Thông tin bài viết**: Tiêu đề, Mô tả sự cố, thông tin người báo lỗi.
  - **Gallery Ảnh minh chứng**: Danh sách ảnh dạng Grid Thumbnail, bấm vào để xem phóng to (Lightbox Modal).
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
- **Khung Ghi chú Chuyên môn**:
  - Xem ghi chú chỉ đạo từ Trưởng bộ phận khi giao việc.
- **Nhật ký Chuyển trạng thái (Status Log Timeline)**:
  - Danh sách log lịch sử bất biến (Ai chuyển, Trạng thái cũ $\rightarrow$ Mới, Thời gian chính xác từng giây).

---

### 📌 PHÂN HỆ 3: Quản trị viên & Trưởng bộ phận (Manager Dashboard)

#### 3.1. Trang Tổng quan Báo cáo & Thống kê (Analytics Dashboard — UC14)
- **Top Stat Cards (KPIs)**:
  - Thẻ 1: Tổng số Ticket trong tháng (Icon `bi-ticket-perforated`).
  - Thẻ 2: Tỷ lệ hoàn thành đúng SLA (VD: `94.5%` — Color Success).
  - Thẻ 3: Ticket quá hạn chưa xong (VD: `3` — Color Danger).
  - Thẻ 4: Điểm hài lòng trung bình (VD: `4.8 / 5.0 ⭐`).
- **Biểu đồ Thống kê (Charts Area)**:
  - Biểu đồ cột: Thống kê số lượng sự cố theo từng Danh mục (Wi-Fi, Máy chiếu, Phần mềm...).
  - Biểu đồ tròn: Thống kê sự cố theo các Khoa / Phòng ban gửi tới.

#### 3.2. Trang Phân công & Quản lý Nhân sự (Assign Staff — UC10, UC12)
- **Modal Phân công Kỹ thuật viên (Assign Modal)**:
  - Hiển thị danh sách Kỹ thuật viên kèm thông tin: Chuyên môn chính, Ca trực, Số ticket đang đảm nhận (Ví dụ: *Nguyễn Văn A — Chuyên môn: Mạng — Đang xử lý: 2 ticket*).
  - Nút `[Phân công ngay]` kèm ô nhập Ghi chú chỉ đạo công việc.

#### 3.3. Trang Cấu hình Danh mục & SLA (Category Config — UC11)
- Bảng cấu hình danh mục lỗi và thời gian xử lý tối đa (`sla_hours`):
  - Ví dụ: *Lỗi Mạng Wi-Fi* $\rightarrow$ SLA `4 giờ`.
  - Ví dụ: *Máy chiếu hỏng bóng đèn* $\rightarrow$ SLA `24 giờ`.
  - Cho phép sửa trực tiếp số giờ SLA trên giao diện.

#### 3.4. Trang Báo cáo Vi phạm SLA (SLA Overdue Report — UC13)
- Bảng danh sách các sự cố bị trễ hạn xử lý, highlighted màu đỏ nổi bật.
- Cung cấp nút `[Đôn đốc]` hoặc `[Chuyển KTV khác]` khẩn cấp.
