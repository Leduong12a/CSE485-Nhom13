<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. SEED PHÒNG BAN / KHOA (departments)
        $deptCnttId = DB::table('departments')->insertGetId([
            'code' => 'CNTT',
            'name' => 'Khoa Công nghệ thông tin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $deptCtsvId = DB::table('departments')->insertGetId([
            'code' => 'P_CTSV',
            'name' => 'Phòng Công tác sinh viên',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. SEED TÀI KHOẢN NGƯỜI DÙNG (users)
        // Password mặc định: password
        $adminId = DB::table('users')->insertGetId([
            'department_id' => $deptCnttId,
            'name' => 'Quản trị viên TLU',
            'email' => 'admin@tlu.edu.vn',
            'password' => Hash::make('password'),
            'role' => 'MANAGER',
            'is_active' => true,
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $staff1Id = DB::table('users')->insertGetId([
            'department_id' => $deptCnttId,
            'name' => 'Nguyễn Văn A (Mạng & Phần cứng)',
            'email' => 'staff@tlu.edu.vn',
            'password' => Hash::make('password'),
            'role' => 'STAFF',
            'is_active' => true,
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $staff2Id = DB::table('users')->insertGetId([
            'department_id' => $deptCnttId,
            'name' => 'Trần Thị B (Phần mềm & ĐKMH)',
            'email' => 'staff2@tlu.edu.vn',
            'password' => Hash::make('password'),
            'role' => 'STAFF',
            'is_active' => true,
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $studentId = DB::table('users')->insertGetId([
            'department_id' => $deptCnttId,
            'name' => 'Lê Quý Dương (Sinh viên)',
            'email' => 'student@st.tlu.edu.vn',
            'password' => Hash::make('password'),
            'role' => 'REQUESTER',
            'is_active' => true,
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. SEED HỒ SƠ KĨ THUẬT VIÊN (staff_profiles)
        $profile1Id = DB::table('staff_profiles')->insertGetId([
            'user_id' => $staff1Id,
            'phone' => '0987654321',
            'shift' => 'Ca Sáng (7:00 - 11:30)',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $profile2Id = DB::table('staff_profiles')->insertGetId([
            'user_id' => $staff2Id,
            'phone' => '0912345678',
            'shift' => 'Ca Chiều (13:00 - 17:30)',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 4. SEED DANH MỤC SỰ CỐ & CẤU HÌNH SLA (ticket_categories)
        $catWifiId = DB::table('ticket_categories')->insertGetId([
            'name' => 'Sự cố Mạng Wi-Fi TLU',
            'description' => 'Không kết nối được Wi-Fi, sóng yếu, mất mạng phòng học',
            'sla_hours' => 4,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $catProjectorId = DB::table('ticket_categories')->insertGetId([
            'name' => 'Máy chiếu & Thiết bị Phòng học',
            'description' => 'Máy chiếu không lên hình, hỏng cáp HDMI, Tivi không có tiếng',
            'sla_hours' => 12,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $catPortalId = DB::table('ticket_categories')->insertGetId([
            'name' => 'Tài khoản Đăng ký môn học / Portal',
            'description' => 'Quên mật khẩu, lỗi tài khoản bị khóa khi ĐKMH',
            'sla_hours' => 24,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $catLabId = DB::table('ticket_categories')->insertGetId([
            'name' => 'Hỏng Điều hòa & Máy tính Phòng Lab',
            'description' => 'Điều hòa chảy nước, máy tính lab không lên nguồn',
            'sla_hours' => 48,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 5. SEED CHUYÊN MÔN KĨ THUẬT VIÊN (staff_specialties - Bảng N-N)
        // 5. SEED CHUYÊN MÔN KĨ THUẬT VIÊN (staff_specialties - Bảng N-N)
        DB::table('staff_specialties')->insert([
            [
                'staff_profile_id' => $profile1Id,
                'category_id' => $catWifiId,
            ],
            [
                'staff_profile_id' => $profile1Id,
                'category_id' => $catProjectorId,
            ],
            [
                'staff_profile_id' => $profile2Id,
                'category_id' => $catPortalId,
            ],
            [
                'staff_profile_id' => $profile2Id,
                'category_id' => $catLabId,
            ],
        ]);

        // 6. SEED TICKET MẪU (tickets)
        $ticket1Id = DB::table('tickets')->insertGetId([
            'title' => 'Máy chiếu P301 Tòa A2 không nhận tín hiệu HDMI',
            'description' => 'Giảng viên cắm dây HDMI vào laptop nhưng máy chiếu báo No Signal. Nhờ kỹ thuật viên hỗ trợ gấp cho ca học sáng.',
            'location' => 'P.301 - Tòa A2',
            'category_id' => $catProjectorId,
            'requester_id' => $studentId,
            'current_assignee_id' => $staff1Id,
            'status' => 'IN_PROGRESS',
            'priority' => 'HIGH',
            'sla_deadline' => Carbon::now()->addHours(12),
            'resolved_at' => null,
            'closed_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $ticket2Id = DB::table('tickets')->insertGetId([
            'title' => 'Wi-Fi TLU_Student bị chập chờn ở sảnh Tòa B1',
            'description' => 'Em không thể kết nối mạng Wi-Fi sinh viên để làm bài kiểm tra trắc nghiệm online.',
            'location' => 'Sảnh Tòa B1',
            'category_id' => $catWifiId,
            'requester_id' => $studentId,
            'current_assignee_id' => null,
            'status' => 'OPEN',
            'priority' => 'MEDIUM',
            'sla_deadline' => Carbon::now()->addHours(4),
            'resolved_at' => null,
            'closed_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 7. SEED PHÂN CÔNG KĨ THUẬT VIÊN (ticket_assignments)
        DB::table('ticket_assignments')->insert([
            'ticket_id' => $ticket1Id,
            'assigned_to_staff_id' => $staff1Id,
            'assigned_by_user_id' => $adminId,
            'note' => 'Đã giao anh A qua kiểm tra dây cáp HDMI phòng 301 A2 gấp.',
            'assigned_at' => $now,
        ]);

        // 8. SEED BÌNH LUẬN TRAO ĐỔI (ticket_comments)
        $comment1Id = DB::table('ticket_comments')->insertGetId([
            'ticket_id' => $ticket1Id,
            'user_id' => $staff1Id,
            'content' => 'Chào bạn, mình đang mang cáp HDMI mới lên phòng 301 A2 để thay nhé!',
            'created_at' => $now->copy()->addMinutes(10),
            'updated_at' => $now->copy()->addMinutes(10),
        ]);

        // 9. SEED TỆP ĐÍNH KÈM (ticket_attachments)
        DB::table('ticket_attachments')->insert([
            [
                'ticket_id' => $ticket1Id,
                'comment_id' => null, // Ảnh minh chứng gốc lúc tạo Ticket
                'file_path' => 'attachments/maychieu_p301_error.jpg',
                'file_type' => 'image/jpeg',
                'created_at' => $now,
            ],
            [
                'ticket_id' => $ticket1Id,
                'comment_id' => $comment1Id, // Ảnh gửi kèm trong tin nhắn Chat
                'file_path' => 'attachments/cap_hdmi_replaced.jpg',
                'file_type' => 'image/jpeg',
                'created_at' => $now->copy()->addMinutes(10),
            ],
        ]);

        // 10. SEED LOG TRẠNG THÁI BẤT BIẾN (ticket_status_logs)
        DB::table('ticket_status_logs')->insert([
            [
                'ticket_id' => $ticket1Id,
                'changed_by_user_id' => $studentId,
                'old_status' => null,
                'new_status' => 'OPEN',
                'created_at' => $now,
            ],
            [
                'ticket_id' => $ticket1Id,
                'changed_by_user_id' => $staff1Id,
                'old_status' => 'OPEN',
                'new_status' => 'IN_PROGRESS',
                'created_at' => $now->copy()->addMinutes(5),
            ],
        ]);
    }
}