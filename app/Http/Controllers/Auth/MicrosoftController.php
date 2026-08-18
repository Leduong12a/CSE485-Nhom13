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
    public function redirect()
    {
        return Socialite::driver('azure')->redirect();
    }

    public function callback()
    {
        try {
            $msUser = Socialite::driver('azure')->user();
        } catch (\Exception $e) {
            Log::error('Microsoft SSO Callback Error: ' . $e->getMessage());
            return redirect()->route('login')
                ->withErrors(['email' => 'Đăng nhập Microsoft thất bại: ' . $e->getMessage()]);
        }

        $email = $msUser->getEmail()
            ?? ($msUser->user['mail'] ?? null)
            ?? ($msUser->user['userPrincipalName'] ?? null);

        if (! $email) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Không tìm thấy địa chỉ Email từ tài khoản Microsoft của bạn.']);
        }

        $email = strtolower(trim($email));
        $name  = $msUser->getName() ?? explode('@', $email)[0];

        if (! str_ends_with($email, '@e.tlu.edu.vn')
            && ! str_ends_with($email, '@tlu.edu.vn')
            && ! str_ends_with($email, '@st.tlu.edu.vn')) {
            return redirect()->route('login')
                ->withErrors(['email' => "Chỉ chấp nhận tài khoản Email của Trường Đại học Thủy Lợi (@e.tlu.edu.vn hoặc @tlu.edu.vn). Email của bạn: {$email}"]);
        }

        $role = str_ends_with($email, '@e.tlu.edu.vn') || str_ends_with($email, '@st.tlu.edu.vn')
            ? 'REQUESTER'
            : 'REQUESTER';

        $departmentId = $this->detectDepartment($email);
        $username = explode('@', $email)[0];

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'              => $name,
                'password'          => bcrypt(\Illuminate\Support\Str::random(32)),
                'role'              => $role,
                'is_active'         => true,
                'department_id'     => $departmentId,
                'email_verified_at' => now(),
            ]
        );

        if (! $user->is_active) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.']);
        }

        if (! $user->department_id && $departmentId) {
            $user->update(['department_id' => $departmentId]);
        }

        Auth::login($user, remember: true);

        return redirect()->route('student.tickets.index');
    }

    private function detectDepartment(string $email): ?int
    {
        $majorMap = [
            '51' => 'CNTT',
            '52' => 'CNTT',
            '45' => 'KT',
            '42' => 'CK',
            '44' => 'DT',
            '43' => 'XD',
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
