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
    }
}