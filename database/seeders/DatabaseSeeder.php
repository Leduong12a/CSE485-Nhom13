<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Thêm Phòng ban mẫu (departments)
        $cnttId = DB::table('departments')->insertGetId([
            'code' => 'CNTT',
            'name' => 'Khoa Công nghệ thông tin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $qt3bId = DB::table('departments')->insertGetId([
            'code' => 'QT3B',
            'name' => 'Phòng Quản trị thiết bị 3B',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dtId = DB::table('departments')->insertGetId([
            'code' => 'DT',
            'name' => 'Phòng Đào tạo TLU',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ctctId = DB::table('departments')->insertGetId([
            'code' => 'CTCT',
            'name' => 'Phòng Công tác sinh viên',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Thêm Danh mục sự cố mẫu & SLA (ticket_categories)
        $wifiCatId = DB::table('ticket_categories')->insertGetId([
            'name' => 'Lỗi kết nối Mạng Wi-Fi TLU',
            'description' => 'Sự cố không thể truy cập hoặc sóng Wi-Fi yếu tại khu vực giảng đường và ký túc xá.',
            'sla_hours' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $projectorCatId = DB::table('ticket_categories')->insertGetId([
            'name' => 'Máy chiếu & Thiết bị phòng học hỏng',
            'description' => 'Lỗi hỏng bóng đèn máy chiếu, mất tín hiệu HDMI, loa giảng đường bị rè hoặc không có tiếng.',
            'sla_hours' => 12,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dkmhCatId = DB::table('ticket_categories')->insertGetId([
            'name' => 'Lỗi Hệ thống Đăng ký môn học',
            'description' => 'Sự cố nghẽn mạng, không đăng nhập được hoặc sai thông tin học phần trên cổng ĐKMH.',
            'sla_hours' => 24,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $softwareCatId = DB::table('ticket_categories')->insertGetId([
            'name' => 'Sự cố Phần mềm & Email sinh viên',
            'description' => 'Quên mật khẩu email @st.tlu.edu.vn, lỗi phần mềm thực hành tại phòng máy.',
            'sla_hours' => 8,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Tạo Tài khoản Người dùng mẫu (users)
        // Manager / Admin Account
        $managerId = DB::table('users')->insertGetId([
            'department_id' => $cnttId,
            'name' => 'Quản trị viên TLU',
            'email' => 'admin@tlu.edu.vn',
            'password' => Hash::make('password'),
            'role' => 'MANAGER',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Staff Accounts (Kỹ thuật viên)
        $staff1Id = DB::table('users')->insertGetId([
            'department_id' => $qt3bId,
            'name' => 'KTV. Nguyễn Văn Kỹ Thuật',
            'email' => 'staff1@tlu.edu.vn',
            'password' => Hash::make('password'),
            'role' => 'STAFF',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $staff2Id = DB::table('users')->insertGetId([
            'department_id' => $qt3bId,
            'name' => 'KTV. Trần Văn Mạng',
            'email' => 'staff2@tlu.edu.vn',
            'password' => Hash::make('password'),
            'role' => 'STAFF',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Requester Accounts (Sinh viên & Giảng viên)
        $studentId = DB::table('users')->insertGetId([
            'department_id' => $cnttId,
            'name' => 'Sinh viên Nguyễn Văn A',
            'email' => 'student1@st.tlu.edu.vn',
            'password' => Hash::make('password'),
            'role' => 'REQUESTER',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $teacherId = DB::table('users')->insertGetId([
            'department_id' => $dtId,
            'name' => 'Giảng viên Lê Thị B',
            'email' => 'teacher1@tlu.edu.vn',
            'password' => Hash::make('password'),
            'role' => 'REQUESTER',
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Tạo Hồ sơ Kỹ thuật viên (staff_profiles & staff_specialties)
        $profile1Id = DB::table('staff_profiles')->insertGetId([
            'user_id' => $staff1Id,
            'phone' => '0912345678',
            'shift' => 'Ca Sáng (07:30 - 11:30)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $profile2Id = DB::table('staff_profiles')->insertGetId([
            'user_id' => $staff2Id,
            'phone' => '0987654321',
            'shift' => 'Ca Chiều (13:00 - 17:00)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Gán Chuyên môn staff
        DB::table('staff_specialties')->insert([
            ['staff_profile_id' => $profile1Id, 'category_id' => $projectorCatId],
            ['staff_profile_id' => $profile2Id, 'category_id' => $wifiCatId],
            ['staff_profile_id' => $profile2Id, 'category_id' => $dkmhCatId],
        ]);

        // 5. Tạo các Ticket mẫu & Tiến độ (tickets)
        // Ticket 1: Đang xử lý
        $ticket1Id = DB::table('tickets')->insertGetId([
            'title' => 'Máy chiếu phòng 302 C5 không lên hình',
            'description' => 'Bật công tắc máy chiếu nhưng đèn báo đỏ chớp nháy, màn hình máy tính không xuất tín hiệu sang máy chiếu.',
            'location' => 'Phòng 302 - Nhà C5',
            'category_id' => $projectorCatId,
            'requester_id' => $teacherId,
            'current_assignee_id' => $staff1Id,
            'status' => 'IN_PROGRESS',
            'priority' => 'HIGH',
            'sla_deadline' => now()->addHours(12),
            'created_at' => now()->subHours(2),
            'updated_at' => now()->subHour(),
        ]);

        // Ticket 2: Mới tạo
        $ticket2Id = DB::table('tickets')->insertGetId([
            'title' => 'Mất sóng Wi-Fi tại tầng 3 nhà A1',
            'description' => 'Sóng Wi-Fi TLU_Student tại khu vực hành lang tầng 3 nhà A1 bị mất hoàn toàn từ đầu giờ sáng.',
            'location' => 'Hành lang Tầng 3 - Nhà A1',
            'category_id' => $wifiCatId,
            'requester_id' => $studentId,
            'current_assignee_id' => null,
            'status' => 'OPEN',
            'priority' => 'MEDIUM',
            'sla_deadline' => now()->addHours(4),
            'created_at' => now()->subMinutes(30),
            'updated_at' => now()->subMinutes(30),
        ]);

        // Ticket 3: Đã giải quyết & Đã làm khảo sát
        $ticket3Id = DB::table('tickets')->insertGetId([
            'title' => 'Không thể bấm đăng ký môn Cơ sở dữ liệu',
            'description' => 'Tài khoản báo lỗi trùng ca học mặc dù thời khóa biểu không bị vướng lịch.',
            'location' => 'Hệ thống ĐKMH Online',
            'category_id' => $dkmhCatId,
            'requester_id' => $studentId,
            'current_assignee_id' => $staff2Id,
            'status' => 'RESOLVED',
            'priority' => 'HIGH',
            'sla_deadline' => now()->subHours(5),
            'resolved_at' => now()->subHour(),
            'closed_at' => null,
            'created_at' => now()->subHours(10),
            'updated_at' => now()->subHour(),
        ]);

        // 6. Lịch sử Phân công mẫu (ticket_assignments)
        DB::table('ticket_assignments')->insert([
            [
                'ticket_id' => $ticket1Id,
                'assigned_to_staff_id' => $staff1Id,
                'assigned_by_user_id' => $managerId,
                'note' => 'Kiểm tra gấp bóng đèn máy chiếu phòng 302 C5 để chuẩn bị giờ học tiếp theo.',
                'assigned_at' => now()->subHour(),
            ],
            [
                'ticket_id' => $ticket3Id,
                'assigned_to_staff_id' => $staff2Id,
                'assigned_by_user_id' => $managerId,
                'note' => 'Kiểm tra lại dữ liệu ràng buộc trên cổng ĐKMH cho sinh viên.',
                'assigned_at' => now()->subHours(8),
            ],
        ]);

        // 7. Bình luận trao đổi mẫu (ticket_comments)
        $comment1Id = DB::table('ticket_comments')->insertGetId([
            'ticket_id' => $ticket1Id,
            'user_id' => $staff1Id,
            'content' => 'Chào cô, em đang mang bóng đèn dự phòng lên kiểm tra phòng 302 C5 nhé!',
            'created_at' => now()->subMinutes(45),
        ]);

        DB::table('ticket_comments')->insert([
            'ticket_id' => $ticket1Id,
            'user_id' => $teacherId,
            'content' => 'Cảm ơn em, cô đang đứng chờ ở phòng 302 nhé.',
            'created_at' => now()->subMinutes(30),
        ]);

        // 8. Nhật ký đổi trạng thái mẫu (ticket_status_logs)
        DB::table('ticket_status_logs')->insert([
            [
                'ticket_id' => $ticket1Id,
                'changed_by_user_id' => $staff1Id,
                'old_status' => 'OPEN',
                'new_status' => 'IN_PROGRESS',
                'created_at' => now()->subHour(),
            ],
            [
                'ticket_id' => $ticket3Id,
                'changed_by_user_id' => $staff2Id,
                'old_status' => 'IN_PROGRESS',
                'new_status' => 'RESOLVED',
                'created_at' => now()->subHour(),
            ],
        ]);

        // 9. Đánh giá hài lòng mẫu (satisfaction_surveys)
        DB::table('satisfaction_surveys')->insert([
            'ticket_id' => $ticket3Id,
            'rating_stars' => 5,
            'comment' => 'Kỹ thuật viên xử lý hỗ trợ sửa lỗi đăng ký môn học rất nhanh và nhiệt tình. Cảm ơn nhà trường!',
            'created_at' => now()->subMinutes(20),
        ]);
    }
}
