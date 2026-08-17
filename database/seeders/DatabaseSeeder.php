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

        // 2. SEED DANH MỤC SỰ CỐ & CẤU HÌNH SLA (ticket_categories)
        $catProjectorId = DB::table('ticket_categories')->insertGetId([
            'name' => 'Máy chiếu & Thiết bị Phòng học',
            'description' => 'Máy chiếu không lên hình, hỏng cáp HDMI, Tivi không có tiếng (Cần sửa gấp trong tiết học)',
            'sla_hours' => 1, // 1 giờ (khắc phục khẩn cấp trong phạm vi ca học 50-100 phút)
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $catWifiId = DB::table('ticket_categories')->insertGetId([
            'name' => 'Sự cố Mạng Wi-Fi TLU',
            'description' => 'Không kết nối được Wi-Fi, sóng yếu, mất mạng phòng học',
            'sla_hours' => 2, // 2 giờ
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

        // 3. SEED TÀI KHOẢN NGƯỜI DÙNG (users)
        // Admin Manager
        $adminId = DB::table('users')->insertGetId([
            'department_id' => $deptCnttId,
            'name' => 'Quản trị viên TLU',
            'email' => 'admin@tlu.edu.vn',
            'password' => Hash::make('123456'),
            'role' => 'MANAGER',
            'is_active' => true,
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Sinh viên mẫu
        $studentId = DB::table('users')->insertGetId([
            'department_id' => $deptCnttId,
            'name' => 'Lê Quý Dương (Sinh viên)',
            'email' => 'student@st.tlu.edu.vn',
            'password' => Hash::make('123456'),
            'role' => 'REQUESTER',
            'is_active' => true,
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ── NHÓM 1: 10 KTV Hardware & CSVC Giảng đường (On-site Hardware) ──
        // Email chuẩn định dạng TLU: [tên].[tên_đệm_chữ_cái_đầu]@tlu.edu.vn
        $hardwareStaffs = [
            ['name' => 'Nguyễn Văn An (Trưởng nhóm On-site)',    'email' => 'an.nv@tlu.edu.vn'],
            ['name' => 'Trần Văn Bình (KTV Giảng đường A1)',   'email' => 'binh.tv@tlu.edu.vn'],
            ['name' => 'Cấn Văn Cường (KTV Giảng đường A2)',   'email' => 'cuong.cv@tlu.edu.vn'],
            ['name' => 'Đỗ Văn Dũng (KTV Tòa B1)',             'email' => 'dung.dv@tlu.edu.vn'],
            ['name' => 'Hoàng Văn Giang (KTV Tòa C5)',          'email' => 'giang.hv@tlu.edu.vn'],
            ['name' => 'Vũ Văn Hùng (KTV Phòng Lab 1)',         'email' => 'hung.vv@tlu.edu.vn'],
            ['name' => 'Đặng Văn Khanh (KTV Phòng Lab 2)',      'email' => 'khanh.dv@tlu.edu.vn'],
            ['name' => 'Bùi Văn Long (KTV Wi-Fi Tòa A)',       'email' => 'long.bv@tlu.edu.vn'],
            ['name' => 'Phạm Văn Minh (KTV Wi-Fi Tòa B)',       'email' => 'minh.pv@tlu.edu.vn'],
            ['name' => 'Lê Văn Nam (KTV Máy chiếu A2)',        'email' => 'nam.lv@tlu.edu.vn'],
        ];

        $shifts = ['Ca Sáng (07:00 - 11:30)', 'Ca Chiều (13:00 - 17:30)', 'Ca Tối / Hành chính'];
        $firstStaff1Id = null;

        foreach ($hardwareStaffs as $idx => $stf) {
            $staffId = DB::table('users')->insertGetId([
                'department_id' => $deptCnttId,
                'name' => $stf['name'],
                'email' => $stf['email'],
                'password' => Hash::make('123456'),
                'role' => 'STAFF',
                'is_active' => true,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($idx === 0) $firstStaff1Id = $staffId;

            $profileId = DB::table('staff_profiles')->insertGetId([
                'user_id' => $staffId,
                'phone' => '0987' . str_pad($idx + 1, 6, '0', STR_PAD_LEFT),
                'shift' => $shifts[$idx % 3],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Gán chuyên môn Hardware: Máy chiếu, Wi-Fi, Phòng Lab
            DB::table('staff_specialties')->insert([
                ['staff_profile_id' => $profileId, 'category_id' => $catProjectorId],
                ['staff_profile_id' => $profileId, 'category_id' => $catWifiId],
                ['staff_profile_id' => $profileId, 'category_id' => $catLabId],
            ]);
        }

        // ── NHÓM 2: 10 KTV Phần mềm & Hệ thống (Software / System) ──
        $softwareStaffs = [
            ['name' => 'Trần Thị Bình (Trưởng nhóm Software)',  'email' => 'binh.tt@tlu.edu.vn'],
            ['name' => 'Nguyễn Thị Hoa (KTV Portal ĐKMH)',      'email' => 'hoa.nt@tlu.edu.vn'],
            ['name' => 'Lê Thị Hương (KTV Mật khẩu Teams)',    'email' => 'huong.lt@tlu.edu.vn'],
            ['name' => 'Phạm Thị Mai (KTV Mật khẩu Outlook)',   'email' => 'mai.pt@tlu.edu.vn'],
            ['name' => 'Đỗ Thị Nga (KTV Hệ thống Điểm)',        'email' => 'nga.dt@tlu.edu.vn'],
            ['name' => 'Hoàng Thị Oanh (KTV Học online)',      'email' => 'oanh.ht@tlu.edu.vn'],
            ['name' => 'Vũ Thị Phương (KTV Cấp lại TK)',       'email' => 'phuong.vt@tlu.edu.vn'],
            ['name' => 'Đặng Thị Quỳnh (KTV Hỗ trợ ĐKMH)',      'email' => 'quynh.dt@tlu.edu.vn'],
            ['name' => 'Bùi Thị Thu (KTV Admin Portal)',        'email' => 'thu.bt@tlu.edu.vn'],
            ['name' => 'Bùi Thị Vân (KTV Hỗ trợ Giảng viên)',   'email' => 'van.bt@tlu.edu.vn'],
        ];

        foreach ($softwareStaffs as $idx => $stf) {
            $staffId = DB::table('users')->insertGetId([
                'department_id' => $deptCnttId,
                'name' => $stf['name'],
                'email' => $stf['email'],
                'password' => Hash::make('123456'),
                'role' => 'STAFF',
                'is_active' => true,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $profileId = DB::table('staff_profiles')->insertGetId([
                'user_id' => $staffId,
                'phone' => '0912' . str_pad($idx + 1, 6, '0', STR_PAD_LEFT),
                'shift' => $shifts[$idx % 3],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Gán chuyên môn Software: Portal ĐKMH
            DB::table('staff_specialties')->insert([
                ['staff_profile_id' => $profileId, 'category_id' => $catPortalId],
            ]);
        }

        // 4. SEED TICKET MẪU (tickets)
        $ticket1Id = DB::table('tickets')->insertGetId([
            'title' => 'Máy chiếu P301 Tòa A2 không nhận tín hiệu HDMI',
            'description' => 'Giảng viên cắm dây HDMI vào laptop nhưng máy chiếu báo No Signal. Nhờ kỹ thuật viên hỗ trợ gấp cho ca học sáng.',
            'location' => 'P.301 - Tòa A2',
            'category_id' => $catProjectorId,
            'requester_id' => $studentId,
            'current_assignee_id' => $firstStaff1Id,
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

        // 5. SEED PHÂN CÔNG KĨ THUẬT VIÊN (ticket_assignments)
        DB::table('ticket_assignments')->insert([
            'ticket_id' => $ticket1Id,
            'assigned_to_staff_id' => $firstStaff1Id,
            'assigned_by_user_id' => $adminId,
            'note' => 'Đã giao anh An qua kiểm tra dây cáp HDMI phòng 301 A2 gấp.',
            'assigned_at' => $now,
        ]);

        // 6. SEED BÌNH LUẬN TRAO ĐỔI (ticket_comments)
        $comment1Id = DB::table('ticket_comments')->insertGetId([
            'ticket_id' => $ticket1Id,
            'user_id' => $firstStaff1Id,
            'content' => 'Chào bạn, mình đang mang cáp HDMI mới lên phòng 301 A2 để thay nhé!',
            'created_at' => $now->copy()->addMinutes(10),
            'updated_at' => $now->copy()->addMinutes(10),
        ]);

        // 7. SEED TỆP ĐÍNH KÈM (ticket_attachments)
        DB::table('ticket_attachments')->insert([
            [
                'ticket_id' => $ticket1Id,
                'comment_id' => null,
                'file_path' => 'attachments/maychieu_p301_error.jpg',
                'file_type' => 'image/jpeg',
                'created_at' => $now,
            ],
            [
                'ticket_id' => $ticket1Id,
                'comment_id' => $comment1Id,
                'file_path' => 'attachments/cap_hdmi_replaced.jpg',
                'file_type' => 'image/jpeg',
                'created_at' => $now->copy()->addMinutes(10),
            ],
        ]);

        // 8. SEED LOG TRẠNG THÁI BẤT BIẾN (ticket_status_logs)
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
                'changed_by_user_id' => $firstStaff1Id,
                'old_status' => 'OPEN',
                'new_status' => 'IN_PROGRESS',
                'created_at' => $now->copy()->addMinutes(5),
            ],
        ]);
    }
}