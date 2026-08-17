<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftController extends Controller
{
    /**
     * Redirect người dùng đến trang đăng nhập Microsoft TLU
     */
    public function redirect()
    {
        return Socialite::driver('azure')->redirect();
    }

    /**
     * Xử lý callback sau khi Microsoft xác thực xong
     */
    public function callback()
    {
        try {
            $msUser = Socialite::driver('azure')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Đăng nhập Microsoft thất bại. Vui lòng thử lại.']);
        }

        $email = $msUser->getEmail();
        $name  = $msUser->getName();

        // Chỉ chấp nhận email TLU
        if (! str_ends_with($email, '@e.tlu.edu.vn')
            && ! str_ends_with($email, '@tlu.edu.vn')
            && ! str_ends_with($email, '@st.tlu.edu.vn')) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Chỉ chấp nhận tài khoản Email của Trường Đại học Thủy Lợi (@e.tlu.edu.vn hoặc @tlu.edu.vn).']);
        }

        // Xác định role dựa trên domain email
        $role = str_ends_with($email, '@e.tlu.edu.vn') || str_ends_with($email, '@st.tlu.edu.vn')
            ? 'REQUESTER'  // Sinh viên
            : 'REQUESTER'; // Giảng viên/Staff cũng mặc định REQUESTER (có thể nâng cấp sau)

        // Tự động tìm Khoa dựa trên Mã Sinh Viên trong email
        $departmentId = $this->detectDepartment($email);

        // Tìm hoặc tạo User trong CSDL
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'              => $name ?? explode('@', $email)[0],
                'password'          => bcrypt(\Illuminate\Support\Str::random(32)), // password ngẫu nhiên (không dùng)
                'role'              => $role,
                'is_active'         => true,
                'department_id'     => $departmentId,
                'email_verified_at' => now(),
            ]
        );

        // Nếu tài khoản bị khóa
        if (! $user->is_active) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.']);
        }

        // Cập nhật department_id nếu chưa có
        if (! $user->department_id && $departmentId) {
            $user->update(['department_id' => $departmentId]);
        }

        Auth::login($user, remember: true);

        return redirect()->route('student.tickets.index');
    }

    /**
     * Tự động nhận diện Khoa từ Mã Sinh Viên trong Email
     * VD: 2351xxxx@e.tlu.edu.vn → mã ngành 51 → Khoa CNTT
     */
    private function detectDepartment(string $email): ?int
    {
        // Bảng map mã ngành → tên Khoa (có thể mở rộng thêm)
        $majorMap = [
            '51' => 'CNTT',   // Công nghệ thông tin (215106xxxx)
            '52' => 'CNTT',   // Kỹ thuật phần mềm
            '45' => 'KT',     // Kinh tế và Quản lý
            '42' => 'CK',     // Cơ khí
            '44' => 'DT',     // Điện - Điện tử
            '43' => 'XD',     // Xây dựng
        ];

        // Lấy phần username trước @
        $username = explode('@', $email)[0];

        // MSV sinh viên TLU thường dạng: 2351XXXXXX (10 chữ số)
        // 2 ký tự ở vị trí 3-4 là mã ngành
        if (preg_match('/^\d{10}$/', $username)) {
            $majorCode = substr($username, 2, 2);
            $deptCode  = $majorMap[$majorCode] ?? null;

            if ($deptCode) {
                $dept = Department::where('code', $deptCode)->first();
                return $dept?->id;
            }
        }

        return null;
    }
}
