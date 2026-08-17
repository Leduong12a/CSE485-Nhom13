<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang Hồ sơ Quản trị viên (Manager Profile)
     */
    public function index()
    {
        $user = Auth::user();
        $user->load('department');

        // Thống kê dành cho Quản trị viên
        $totalSystemTickets = Ticket::count();

        $resolvedCount = Ticket::whereIn('status', ['RESOLVED', 'CLOSED'])->count();

        // Tỷ lệ đúng SLA
        $onTimeCount = Ticket::whereIn('status', ['RESOLVED', 'CLOSED'])
            ->whereNotNull('resolved_at')
            ->whereNotNull('sla_deadline')
            ->whereRaw('resolved_at <= sla_deadline')
            ->count();

        $slaRate = $resolvedCount > 0 ? round(($onTimeCount / $resolvedCount) * 100, 1) : 100;

        $totalStaffCount = User::where('role', 'STAFF')->where('is_active', true)->count();

        return view('manager.profile.index', compact(
            'user',
            'totalSystemTickets',
            'resolvedCount',
            'slaRate',
            'totalStaffCount'
        ));
    }

    /**
     * Cập nhật thông tin Quản trị viên & Đổi mật khẩu
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'current_password'      => ['nullable', 'required_with:new_password'],
            'new_password'          => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required'             => 'Vui lòng nhập họ và tên.',
            'current_password.required_with' => 'Vui lòng nhập mật khẩu hiện tại để xác nhận đổi mật khẩu.',
            'new_password.min'          => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.confirmed'    => 'Xác nhận mật khẩu mới không trùng khớp.',
        ]);

        // Nếu có đổi mật khẩu
        if ($request->filled('new_password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->name = $validated['name'];
        $user->save();

        return redirect()->back()
            ->with('success', 'Đã cập nhật thông tin hồ sơ Quản trị viên thành công!');
    }
}
