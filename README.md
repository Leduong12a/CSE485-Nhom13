# 🛠️ TLU Helpdesk — Hệ thống Hỗ trợ Kỹ thuật & Cơ sở Vật chất (TLU)

Hệ thống **TLU Helpdesk** tiếp nhận, phân công và quản lý tiến độ xử lý các sự cố CNTT & Cơ sở vật chất (lỗi Wi-Fi, máy chiếu, thiết bị phòng học, phần mềm đăng ký môn học...) cho Sinh viên, Giảng viên và Cán bộ tại Trường Đại học Thủy Lợi.

---

## 🚀 Công nghệ Sử dụng (Tech Stack)

- **Backend Framework**: [Laravel 12](https://laravel.com/) (PHP ^8.2)
- **Frontend Framework**: Blade Templates, [Bootstrap 5.3](https://getbootstrap.com/), [Bootstrap Icons](https://icons.getbootstrap.com/), [Vite](https://vitejs.dev/)
- **Database Engine**: MySQL / MariaDB (Phiên bản yêu cầu: MySQL >= 8.0.16 / MariaDB >= 10.2.1)
- **Tooling & Package Manager**: Composer, NPM, Axios, Concurrently

---

## 📚 Tài liệu Kiến trúc & Thiết kế

Hệ thống được thiết kế chi tiết với đầy đủ tài liệu chuẩn hóa:

1. 🗄️ **Tài liệu Thiết kế Cơ sở Dữ liệu (Database Schema)**: [database_schema.md](database_schema.md)
   - Đặc tả 11 bảng chuẩn 3NF (`departments`, `users`, `staff_profiles`, `staff_specialties`, `ticket_categories`, `tickets`, `ticket_attachments`, `ticket_assignments`, `ticket_comments`, `ticket_status_logs`, `satisfaction_surveys`).
   - Tích hợp 3 MySQL Database Triggers tự động hóa đồng bộ KTV, tính mốc SLA và reset mốc thời gian khi ticket mở lại (`REOPENED`).
   - Script SQL DDL chuẩn production kèm chỉ mục tối ưu hiệu năng (Indexes).

2. 🎨 **Tài liệu Thiết kế Giao diện Hệ thống (UI/UX Specification)**: [design.md](design.md)
   - Quy chuẩn giao diện Bootstrap 5 cho 3 phân hệ: **Requester** (Sinh viên/GV), **Staff** (Kỹ thuật viên) và **Manager** (Quản lý).
   - Thiết kế hệ thống màu TLU Theme, Layout Responsive, quy chuẩn trạng thái UI (Empty State, Loading Skeleton, Error Toast, Pagination).
   - Ma trận phân quyền (Role-based Matrix) và Quy tắc vận hành kỹ thuật (Upload file <= 5MB, tính SLA giờ hành chính).

---

## 💻 Hướng dẫn Cài đặt & Chạy Ứng dụng

### 1. Yêu cầu Hệ thống
- PHP >= 8.2 (đã bật các extension: `pdo`, `pdo_mysql`, `mbstring`, `openssl`)
- Composer >= 2.x
- Node.js >= 18.x & NPM

### 2. Các bước Cài đặt Dự án

```bash
# 1. Cài đặt các thư viện PHP Backend
composer install

# 2. Cài đặt các thư viện Frontend (bao gồm Bootstrap 5 & Popper.js)
npm install

# 3. Tạo file cấu hình môi trường .env từ mẫu
cp .env.example .env

# 4. Khởi tạo khóa ứng dụng Laravel
php artisan key:generate

# 5. Cấu hình thông số kết nối MySQL/MariaDB trong file .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=tlu_helpdesk
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Chạy Migration khởi tạo bảng CSDL và Triggers
php artisan migrate
```

### 3. Cài đặt & Cấu hình Bootstrap 5 trong Frontend

Nếu chưa tích hợp Bootstrap 5 trong gói NPM, chạy lệnh sau:
```bash
npm install bootstrap @popperjs/core bootstrap-icons
```

Import Bootstrap vào file assets (`resources/css/app.css` và `resources/js/app.js`):
- Trong `resources/css/app.css`:
  ```css
  @import 'bootstrap/dist/css/bootstrap.min.css';
  @import 'bootstrap-icons/font/bootstrap-icons.css';
  ```
- Trong `resources/js/app.js`:
  ```javascript
  import 'bootstrap';
  ```

---

## ⚡ Chạy Ứng dụng ở Môi trường Phát triển (Development)

Chạy đồng thời cả Server Laravel và Vite Bundler:
```bash
composer run dev
```

Hoặc chạy thủ công ở 2 cửa sổ terminal riêng biệt:
```bash
# Terminal 1: Chạy Server Laravel (http://127.0.0.1:8000)
php artisan serve

# Terminal 2: Chạy Vite hot-reload cho Bootstrap Assets
npm run dev
```

---

## 👥 Đơn vị Thực hiện
- **Đề tài**: Bài tập lớn / Đồ án môn học CSE485 — Hệ thống Helpdesk CNTT & Cơ sở vật chất TLU
- **Nhóm thực hiện**: CSE485 - Nhóm 13 (Trường Đại học Thủy Lợi)
