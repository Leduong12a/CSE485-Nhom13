<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffProfile;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load(['staffProfile.specialties', 'department']);

        $profile = $user->staffProfile;
        if (! $profile) {
            $profile = StaffProfile::create([
                'user_id' => $user->id,
                'phone'   => '0987654321',
                'shift'   => 'Ca Sáng (07:00 - 11:30)',
            ]);
        }

        $totalAssigned = Ticket::where('current_assignee_id', $user->id)->count();
        $totalResolved = Ticket::where('current_assignee_id', $user->id)->whereIn('status', ['RESOLVED', 'CLOSED'])->count();
        $inProgress    = Ticket::where('current_assignee_id', $user->id)->where('status', 'IN_PROGRESS')->count();

        return view('staff.profile.index', compact(
            'user',
            'profile',
            'totalAssigned',
            'totalResolved',
            'inProgress'
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'shift' => ['required', 'string', 'max:100'],
        ], [
            'phone.required' => 'Vui lòng nhập số điện thoại trực ca.',
            'shift.required' => 'Vui lòng chọn ca trực cố định.',
        ]);

        $profile = StaffProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['phone' => $validated['phone'], 'shift' => $validated['shift']]
        );

        $profile->update($validated);

        return redirect()->route('staff.profile.index')
            ->with('success', 'Đã cập nhật thông tin hồ sơ ca trực KTV thành công!');
    }
}
