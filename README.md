# 🛠️ TLU Helpdesk — Hệ thống Hỗ trợ Kỹ thuật & Cơ sở Vật chất (TLU)

Hệ thống **TLU Helpdesk** tiếp nhận, phân công và quản lý tiến độ xử lý các sự cố CNTT & Cơ sở vật chất (lỗi Wi-Fi, máy chiếu, thiết bị phòng học, phần mềm đăng ký môn học...) cho Sinh viên, Giảng viên và Cán bộ tại Trường Đại học Thủy Lợi.

---

## 🚀 Công nghệ sử dụng (Tech Stack)

- **Backend**: [Laravel 12](https://laravel.com/) (PHP ^8.2)
- **Frontend**: Blade Templates, [Bootstrap 5](https://getbootstrap.com/), [Vite](https://vitejs.dev/)
- **Database**: MySQL / MariaDB (Hỗ trợ SQLite cho môi trường thử nghiệm)
- **Quản lý gói & Tooling**: Composer, NPM, Concurrently

---

## 🗄️ Cấu trúc Cơ sở Dữ liệu

Chi tiết đặc tả 10 bảng, sơ đồ ERD và quy tắc ràng buộc bảo mật CSDL xem tại:
👉 [database_schema.md](database_schema.md)

---

## 💻 Hướng dẫn Cài đặt & Chạy Ứng dụng

### 1. Yêu cầu hệ thống
- PHP >= 8.2 (đã bật extension pdo, mbstring, openssl)
- Composer >= 2.x
- Node.js >= 18.x & NPM

### 2. Các bước cài đặt
```bash
# 1. Cài đặt các thư viện Backend & Frontend
composer install
npm install

# 2. Tạo file cấu hình môi trường .env
cp .env.example .env

# 3. Tạo APP_KEY cho Laravel
php artisan key:generate

# 4. Cấu hình thông số kết nối Database trong file .env, sau đó chạy Migration
php artisan migrate
```

### 3. Chạy Ứng dụng ở Môi trường Phát triển (Development)

Chạy đồng thời cả Laravel Server và Vite Asset Bundler:
```bash
composer run dev
```

Hoặc chạy thủ công ở 2 cửa sổ terminal riêng biệt:
```bash
# Terminal 1: Chạy Server Laravel (http://127.0.0.1:8000)
php artisan serve

# Terminal 2: Chạy Vite hot-reload
npm run dev
```

---

## 👥 Đơn vị Thực hiện
- **Đề tài**: Bài tập lớn / Đồ án môn học CSE485
- **Nhóm thực hiện**: CSE485 - Nhóm 13 (TLU)
