<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            Log::error('Microsoft SSO Callback Error: ' . $e->getMessage());
            return redirect()->route('login')
                ->withErrors(['email' => 'Đăng nhập Microsoft thất bại: ' . $e->getMessage()]);
        }

        // Lấy Email từ Microsoft Azure claims (hỗ trợ getEmail, mail, userPrincipalName)
        $email = $msUser->getEmail()
            ?? ($msUser->user['mail'] ?? null)
            ?? ($msUser->user['userPrincipalName'] ?? null);

        if (! $email) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Không tìm thấy địa chỉ Email từ tài khoản Microsoft của bạn.']);
        }

        $email = strtolower(trim($email));
        $name  = $msUser->getName() ?? explode('@', $email)[0];

        // Kiểm tra domain email trường TLU
        if (! str_ends_with($email, '@e.tlu.edu.vn')
            && ! str_ends_with($email, '@tlu.edu.vn')
            && ! str_ends_with($email, '@st.tlu.edu.vn')) {
            return redirect()->route('login')
                ->withErrors(['email' => "Chỉ chấp nhận tài khoản Email của Trường Đại học Thủy Lợi (@e.tlu.edu.vn hoặc @tlu.edu.vn). Email của bạn: {$email}"]);
        }

        // Xác định role dựa trên domain email
        $role = str_ends_with($email, '@e.tlu.edu.vn') || str_ends_with($email, '@st.tlu.edu.vn')
            ? 'REQUESTER'  // Sinh viên
            : 'REQUESTER'; // Giảng viên

        // Tự động tìm Khoa dựa trên Mã Sinh Viên trong email
        $departmentId = $this->detectDepartment($email);
        $username = explode('@', $email)[0];

        // Tìm hoặc tạo mới User trong CSDL
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'              => $name,
                'password'          => bcrypt(\Illuminate\Support\Str::random(32)), // Mật khẩu ngẫu nhiên ban đầu
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
        $majorMap = [
            '51' => 'CNTT',   // Công nghệ thông tin
            '52' => 'CNTT',   // Kỹ thuật phần mềm
            '45' => 'KT',     // Kinh tế và Quản lý
            '42' => 'CK',     // Cơ khí
            '44' => 'DT',     // Điện - Điện tử
            '43' => 'XD',     // Xây dựng
        ];

        $username = explode('@', $email)[0];

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
