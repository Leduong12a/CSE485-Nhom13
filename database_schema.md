# 🗄️ THIẾT KẾ CƠ SỞ DỮ LIỆU MYSQL (DATABASE DATA MODEL)

Tài liệu này chi tiết hóa cấu trúc lưu trữ cơ sở dữ liệu quan hệ **MySQL / MariaDB** cho hệ thống **Helpdesk CNTT & Cơ sở vật chất (TLU)**. Kiến trúc được thiết kế tối ưu theo **Chuẩn hóa 3NF (Third Normal Form)**, đảm bảo tính toàn vẹn dữ liệu, hiệu năng truy vấn cao, hỗ trợ đầy đủ các ràng buộc khóa ngoại (Foreign Keys), chỉ mục (Indexes), Trigger tự động hóa và đáp ứng trọn vẹn các yêu cầu nghiệp vụ thực tế.

---

## 1. Sơ đồ Quan hệ Thực thể Đầy đủ Thuộc tính & Khóa (ERD Schema Overview)

```mermaid
erDiagram
    departments {
        bigint id PK
        string code
        string name
        timestamp created_at
        timestamp updated_at
    }

    users {
        bigint id PK
        bigint department_id FK
        string name
        string email
        string password
        enum role
        boolean is_active
        timestamp email_verified_at
        timestamp created_at
        timestamp updated_at
    }

    staff_profiles {
        bigint id PK
        bigint user_id FK
        string phone
        string shift
        timestamp created_at
        timestamp updated_at
    }

    ticket_categories {
        bigint id PK
        string name
        text description
        unsigned_int sla_hours
        timestamp created_at
        timestamp updated_at
    }

    staff_specialties {
        bigint staff_profile_id PK, FK
        bigint category_id PK, FK
    }

    tickets {
        bigint id PK
        string title
        text description
        string location
        bigint category_id FK
        bigint requester_id FK
        bigint current_assignee_id FK
        enum status
        enum priority
        timestamp sla_deadline
        timestamp resolved_at
        timestamp closed_at
        timestamp created_at
        timestamp updated_at
    }

    ticket_comments {
        bigint id PK
        bigint ticket_id FK
        bigint user_id FK
        text content
        timestamp created_at
        timestamp updated_at
    }

    ticket_attachments {
        bigint id PK
        bigint ticket_id FK
        bigint comment_id FK
        string file_path
        string file_type
        timestamp created_at
    }

    ticket_assignments {
        bigint id PK
        bigint ticket_id FK
        bigint assigned_to_staff_id FK
        bigint assigned_by_user_id FK
        text note
        timestamp assigned_at
    }

    ticket_status_logs {
        bigint id PK
        bigint ticket_id FK
        bigint changed_by_user_id FK
        enum old_status
        enum new_status
        timestamp created_at
    }

    satisfaction_surveys {
        bigint id PK
        bigint ticket_id FK
        tinyint rating_stars
        text comment
        timestamp created_at
    }

    %% QUAN HỆ GIỮA CÁC BẢNG (RELATIONSHIPS)
    departments ||--o{ users : "thuộc phòng ban"
    users ||--o| staff_profiles : "hồ sơ kỹ thuật viên"
    staff_profiles ||--o{ staff_specialties : "chuyên môn thuộc danh mục"
    ticket_categories ||--o{ staff_specialties : "danh mục chuyên môn"

    ticket_categories ||--o{ tickets : "phân loại sự cố"
    users ||--o{ tickets : "người báo lỗi (requester)"
    users ||--o{ tickets : "KTV phụ trách (current_assignee)"

    tickets ||--o{ ticket_comments : "trao đổi hai chiều"
    users ||--o{ ticket_comments : "người bình luận"

    tickets ||--o{ ticket_attachments : "file đính kèm ticket"
    ticket_comments ||--o{ ticket_attachments : "file đính kèm chat"

    tickets ||--o{ ticket_assignments : "lịch sử phân công"
    users ||--o{ ticket_assignments : "phân công cho / bởi"

    tickets ||--o{ ticket_status_logs : "nhật ký đổi trạng thái"
    users ||--o{ ticket_status_logs : "người đổi trạng thái"

    tickets ||--o| satisfaction_surveys : "đánh giá sau đóng ticket"
```

---

## 2. Chi tiết Đặc tả Các Bảng CSDL (Table Specifications — 11 Tables)

### 2.1. Bảng `departments` — Cơ cấu Tổ chức & Phòng ban
Chứa danh sách các Khoa, Phòng ban, Trung tâm trong toàn trường Đại học Thủy Lợi.

| Thuộc tính | Kiểu dữ liệu | Ràng buộc | Mô tả & Giải thích |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT` | `UNSIGNED, AUTO_INCREMENT, Primary Key` | ID phòng ban (Khóa chính) |
| `code` | `VARCHAR(20)` | `UNIQUE, NOT NULL` | Mã phòng ban (VD: CNTT, QT3B, DT) |
| `name` | `VARCHAR(100)` | `NOT NULL` | Tên phòng ban / đơn vị |
| `created_at` | `TIMESTAMP` | `NOT NULL, DEFAULT CURRENT_TIMESTAMP` | Thời điểm tạo bản ghi |
| `updated_at` | `TIMESTAMP` | `NOT NULL, DEFAULT CURRENT_TIMESTAMP ON UPDATE` | Thời điểm cập nhật bản ghi |

### 2.2. Bảng `users` — Tài khoản Người dùng & Phân quyền
Lưu trữ toàn bộ thông tin tài khoản đăng nhập của Sinh viên, Giảng viên, Kỹ thuật viên và Quản trị viên.

| Thuộc tính | Kiểu dữ liệu | Ràng buộc | Mô tả & Giải thích |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT` | `UNSIGNED, AUTO_INCREMENT, Primary Key` | ID người dùng (Khóa chính) |
| `department_id` | `BIGINT` | `UNSIGNED, NULLABLE, Foreign Key` | Trực thuộc phòng ban nào (FK -> departments.id, ON DELETE SET NULL) |
| `name` | `VARCHAR(100)` | `NOT NULL` | Họ và tên đầy đủ |
| `email` | `VARCHAR(100)` | `UNIQUE, NOT NULL` | Email TLU (`@st.tlu.edu.vn` hoặc `@tlu.edu.vn`) |
| `password` | `VARCHAR(255)` | `NOT NULL` | Mật khẩu đã mã hóa (Bcrypt Hash) |
| `role` | `ENUM` | `'REQUESTER', 'STAFF', 'MANAGER'` | Vai trò hệ thống: Người gửi / Kỹ thuật / Quản lý |
| `is_active` | `BOOLEAN` | `NOT NULL, DEFAULT TRUE` | Trạng thái tài khoản (True: Hoạt động, False: Khóa) |
| `email_verified_at`| `TIMESTAMP` | `NULLABLE` | Thời điểm xác thực email TLU |
| `created_at` | `TIMESTAMP` | `NOT NULL, DEFAULT CURRENT_TIMESTAMP` | Thời điểm tạo tài khoản |
| `updated_at` | `TIMESTAMP` | `NOT NULL, DEFAULT CURRENT_TIMESTAMP ON UPDATE` | Thời điểm cập nhật |

### 2.3. Bảng `staff_profiles` — Hồ sơ Kỹ thuật viên
Lưu thông tin nghiệp vụ mở rộng cho cán bộ kỹ thuật (Quan hệ 1-1 với `users`).

| Thuộc tính | Kiểu dữ liệu | Ràng buộc | Mô tả & Giải thích |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT` | `UNSIGNED, AUTO_INCREMENT, Primary Key` | ID hồ sơ (Khóa chính) |
| `user_id` | `BIGINT` | `UNSIGNED, UNIQUE, NOT NULL, Foreign Key` | Liên kết tài khoản (FK -> users.id, ON DELETE CASCADE) |
| `phone` | `VARCHAR(20)` | `NULLABLE` | Số điện thoại trực ca |
| `shift` | `VARCHAR(50)` | `NULLABLE` | Ca trực cố định (Sáng / Chiều / Tối) |
| `created_at` | `TIMESTAMP` | `NOT NULL, DEFAULT CURRENT_TIMESTAMP` | Thời điểm tạo |
| `updated_at` | `TIMESTAMP` | `NOT NULL, DEFAULT CURRENT_TIMESTAMP ON UPDATE` | Thời điểm cập nhật |

### 2.4. Bảng `staff_specialties` — Chuyên môn Kỹ thuật viên (Bảng phụ N-N)
Liên kết danh mục chuyên môn của Kỹ thuật viên với danh mục sự cố `ticket_categories` để hệ thống tự động gợi ý phân công.

| Thuộc tính | Kiểu dữ liệu | Ràng buộc | Mô tả & Giải thích |
| :--- | :--- | :--- | :--- |
| `staff_profile_id`| `BIGINT` | `UNSIGNED, NOT NULL, Foreign Key` | Thuộc hồ sơ staff nào (FK -> staff_profiles.id, ON DELETE CASCADE) |
| `category_id` | `BIGINT` | `UNSIGNED, NOT NULL, Foreign Key` | Phụ trách loại sự cố nào (FK -> ticket_categories.id, ON DELETE CASCADE) |
| `PRIMARY KEY` | `(staff_profile_id, category_id)` | `Composite Key` | Khóa chính phức hợp |

### 2.5. Bảng `ticket_categories` — Danh mục Sự cố & Cấu hình SLA
Danh mục loại lỗi kỹ thuật và thời gian cam kết khắc phục sự cố (SLA).

| Thuộc tính | Kiểu dữ liệu | Ràng buộc | Mô tả & Giải thích |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT` | `UNSIGNED, AUTO_INCREMENT, Primary Key` | ID danh mục (Khóa chính) |
| `name` | `VARCHAR(100)` | `UNIQUE, NOT NULL` | Tên loại sự cố (Lỗi Wi-Fi, Máy chiếu, Lỗi Kênh ĐKMH) |
| `description` | `TEXT` | `NULLABLE` | Mô tả chi tiết phạm vi sự cố |
| `sla_hours` | `INT` | `UNSIGNED, NOT NULL, DEFAULT 24` | Thời gian xử lý tối đa cam kết (Tính theo giờ) |
| `created_at` | `TIMESTAMP` | `NOT NULL, DEFAULT CURRENT_TIMESTAMP` | Thời điểm tạo |
| `updated_at` | `TIMESTAMP` | `NOT NULL, DEFAULT CURRENT_TIMESTAMP ON UPDATE` | Thời điểm cập nhật |

### 2.6. Bảng `tickets` — Phiếu Phản ánh Sự cố (Central Table)
Bảng trung tâm chứa thông tin yêu cầu hỗ trợ sự cố từ Sinh viên/Giảng viên.

| Thuộc tính | Kiểu dữ liệu | Ràng buộc | Mô tả & Giải thích |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT` | `UNSIGNED, AUTO_INCREMENT, Primary Key` | ID phiếu sự cố (Khóa chính) |
| `title` | `VARCHAR(255)` | `NOT NULL` | Tiêu đề ngắn gọn mô tả sự cố |
| `description` | `TEXT` | `NOT NULL` | Mô tả chi tiết hiện trạng lỗi |
| `location` | `VARCHAR(150)` | `NULLABLE` | Vị trí vật lý sự cố (VD: Phòng 302 - Nhà C5) |
| `category_id` | `BIGINT` | `UNSIGNED, NOT NULL, Foreign Key` | Thuộc loại sự cố nào (FK -> ticket_categories.id, RESTRICT) |
| `requester_id` | `BIGINT` | `UNSIGNED, NOT NULL, Foreign Key` | Người báo lỗi (FK -> users.id, ON DELETE RESTRICT) |
| `current_assignee_id` | `BIGINT` | `UNSIGNED, NULLABLE, Foreign Key` | KTV đang phụ trách (FK -> users.id, ON DELETE SET NULL) |
| `status` | `ENUM` | `'OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED', 'REOPENED'` | Trạng thái ticket |
| `priority` | `ENUM` | `'LOW', 'MEDIUM', 'HIGH'` | Mức độ ưu tiên khắc phục |
| `sla_deadline` | `TIMESTAMP` | `NULLABLE` | Mốc thời gian hết hạn SLA (`created_at + sla_hours`) |
| `resolved_at` | `TIMESTAMP` | `NULLABLE` | Thời điểm chuyển sang trạng thái RESOLVED |
| `closed_at` | `TIMESTAMP` | `NULLABLE` | Thời điểm đóng ticket hoàn toàn (CLOSED) |
| `created_at` | `TIMESTAMP` | `NOT NULL, DEFAULT CURRENT_TIMESTAMP` | Thời điểm khởi tạo ticket |
| `updated_at` | `TIMESTAMP` | `NOT NULL, DEFAULT CURRENT_TIMESTAMP ON UPDATE` | Thời điểm cập nhật gần nhất |

### 2.7. Bảng `ticket_attachments` — Tệp & Hình ảnh Minh chứng
Lưu trữ danh sách ảnh minh chứng ban đầu của Ticket HOẶC tệp gửi đính kèm trong khung Chat.

| Thuộc tính | Kiểu dữ liệu | Ràng buộc | Mô tả & Giải thích |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT` | `UNSIGNED, AUTO_INCREMENT, Primary Key` | ID file đính kèm (Khóa chính) |
| `ticket_id` | `BIGINT` | `UNSIGNED, NOT NULL, Foreign Key` | Thuộc ticket nào (FK -> tickets.id, ON DELETE CASCADE) |
| `comment_id` | `BIGINT` | `UNSIGNED, NULLABLE, Foreign Key` | Thuộc câu chat nào (FK -> ticket_comments.id, ON DELETE CASCADE). `NULL` = Ảnh gốc |
| `file_path` | `VARCHAR(255)` | `NOT NULL` | Đường dẫn lưu trữ ảnh/file trên Server |
| `file_type` | `VARCHAR(50)` | `NULLABLE` | Định dạng file (image/jpeg, image/png, application/pdf) |
| `created_at` | `TIMESTAMP` | `NOT NULL, DEFAULT CURRENT_TIMESTAMP` | Thời điểm tải file lên |

### 2.8. Bảng `ticket_assignments` — Lịch sử Phân công Kỹ thuật viên
Lưu vết lịch sử giao việc hoặc đổi Kỹ thuật viên phụ trách Ticket.

| Thuộc tính | Kiểu dữ liệu | Ràng buộc | Mô tả & Giải thích |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT` | `UNSIGNED, AUTO_INCREMENT, Primary Key` | ID lượt phân công (Khóa chính) |
| `ticket_id` | `BIGINT` | `UNSIGNED, NOT NULL, Foreign Key` | Phân công cho ticket nào (FK -> tickets.id, ON DELETE CASCADE) |
| `assigned_to_staff_id` | `BIGINT` | `UNSIGNED, NOT NULL, Foreign Key` | Kỹ thuật viên nhận nhiệm vụ (FK -> users.id, ON DELETE RESTRICT) |
| `assigned_by_user_id` | `BIGINT` | `UNSIGNED, NOT NULL, Foreign Key` | Người thực hiện giao việc (FK -> users.id, ON DELETE RESTRICT) |
| `note` | `TEXT` | `NULLABLE` | Ghi chú / Chỉ đạo chuyên môn khi giao việc |
| `assigned_at` | `TIMESTAMP` | `NOT NULL, DEFAULT CURRENT_TIMESTAMP` | Thời điểm thực hiện phân công |

### 2.9. Bảng `ticket_comments` — Trao đổi Hai chiều
Bình luận, trao đổi thông tin qua lại giữa Người báo lỗi và Kỹ thuật viên xử lý.

| Thuộc tính | Kiểu dữ liệu | Ràng buộc | Mô tả & Giải thích |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT` | `UNSIGNED, AUTO_INCREMENT, Primary Key` | ID bình luận (Khóa chính) |
| `ticket_id` | `BIGINT` | `UNSIGNED, NOT NULL, Foreign Key` | Thuộc ticket nào (FK -> tickets.id, ON DELETE CASCADE) |
| `user_id` | `BIGINT` | `UNSIGNED, NOT NULL, Foreign Key` | Người viết bình luận (FK -> users.id, ON DELETE RESTRICT) |
| `content` | `TEXT` | `NOT NULL` | Nội dung câu trao đổi |
| `created_at` | `TIMESTAMP` | `NOT NULL, DEFAULT CURRENT_TIMESTAMP` | Thời điểm gửi bình luận |

### 2.10. Bảng `ticket_status_logs` — Nhật ký Chuyển Trạng thái (Append-Only Log)
Lưu vết lịch sử thay đổi tiến độ của Ticket. Bảng này bất biến (chỉ INSERT, cấm UPDATE/DELETE).

| Thuộc tính | Kiểu dữ liệu | Ràng buộc | Mô tả & Giải thích |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT` | `UNSIGNED, AUTO_INCREMENT, Primary Key` | ID log (Khóa chính) |
| `ticket_id` | `BIGINT` | `UNSIGNED, NOT NULL, Foreign Key` | Thuộc ticket nào (FK -> tickets.id, ON DELETE CASCADE) |
| `changed_by_user_id` | `BIGINT` | `UNSIGNED, NOT NULL, Foreign Key` | Người bấm chuyển trạng thái (FK -> users.id, ON DELETE RESTRICT) |
| `old_status` | `ENUM` | `'OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED', 'REOPENED', NULLABLE` | Trạng thái trước khi chuyển |
| `new_status` | `ENUM` | `'OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED', 'REOPENED'` | Trạng thái mới chuyển sang |
| `created_at` | `TIMESTAMP` | `NOT NULL, DEFAULT CURRENT_TIMESTAMP` | Thời điểm ghi nhận thao tác |

### 2.11. Bảng `satisfaction_surveys` — Khảo sát Đánh giá Mức độ Hài lòng
Bài đánh giá chất lượng phục vụ sau khi Ticket đã hoàn thành xử lý.

| Thuộc tính | Kiểu dữ liệu | Ràng buộc | Mô tả & Giải thích |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT` | `UNSIGNED, AUTO_INCREMENT, Primary Key` | ID bài đánh giá (Khóa chính) |
| `ticket_id` | `BIGINT` | `UNSIGNED, UNIQUE, NOT NULL, Foreign Key` | Khóa ngoại duy nhất (FK -> tickets.id, ON DELETE CASCADE) |
| `rating_stars` | `TINYINT UNSIGNED` | `NOT NULL, CHECK (rating_stars BETWEEN 1 AND 5)` | Mức điểm chấm: 1 đến 5 sao |
| `comment` | `TEXT` | `NULLABLE` | Ý kiến nhận xét đóng góp |
| `created_at` | `TIMESTAMP` | `NOT NULL, DEFAULT CURRENT_TIMESTAMP` | Thời điểm gửi đánh giá |
