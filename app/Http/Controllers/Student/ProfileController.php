<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load('department');

        $departments = Department::orderBy('name')->get();

        $username = explode('@', $user->email)[0];
        $studentCode = preg_match('/^\d+$/', $username) ? $username : '—';

        $totalSubmitted = $user->requestedTickets()->count();
        $totalResolved  = $user->requestedTickets()->whereIn('status', ['RESOLVED', 'CLOSED'])->count();
        $totalSurveys   = $user->requestedTickets()->has('satisfactionSurvey')->count();

        return view('student.profile.index', compact(
            'user',
            'departments',
            'studentCode',
            'totalSubmitted',
            'totalResolved',
            'totalSurveys'
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ], [
            'name.required'        => 'Vui lòng nhập họ và tên.',
            'department_id.exists' => 'Khoa/Đơn vị chọn không hợp lệ.',
        ]);

        $user->update($validated);

        return redirect()->route('student.profile.index')
            ->with('success', 'Đã cập nhật thông tin hồ sơ cá nhân thành công!');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'password' => ['required', 'confirmed', Password::defaults()],
        ];

        if ($request->filled('current_password')) {
            $rules['current_password'] = ['current_password'];
        }

        $request->validate($rules, [
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'password.required'                 => 'Vui lòng nhập mật khẩu mới.',
            'password.confirmed'                => 'Xác nhận mật khẩu mới không trùng khớp.',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('student.profile.index')
            ->with('success', 'Mật khẩu của bạn đã được khởi tạo/thay đổi thành công! Giờ bạn có thể đăng nhập bằng ô Email + Mật khẩu này.');
    }
}
