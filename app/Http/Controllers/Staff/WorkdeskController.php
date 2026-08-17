<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkdeskController extends Controller
{
    /**
     * UC06: Workdesk Dạng Bảng 2 Tab (Assigned to Me & Group Queue)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $user->load('staffProfile');

        $activeTab = $request->get('tab', 'assigned'); // 'assigned' hoặc 'queue'

        // 1. Tab 1: Ticket được giao cho tôi
        $assignedTickets = Ticket::with(['category', 'requester.department', 'currentAssignee'])
            ->where('current_assignee_id', $user->id)
            ->whereNotIn('status', ['CLOSED'])
            ->orderByRaw("FIELD(status, 'REOPENED', 'IN_PROGRESS', 'OPEN', 'RESOLVED')")
            ->orderBy('sla_deadline', 'asc')
            ->paginate(10, ['*'], 'assigned_page');

        // 2. Tab 2: Hàng chờ Sự cố theo Nhóm Chuyên môn (Ticket chưa có ai nhận - OPEN/REOPENED)
        $groupQueueTickets = Ticket::with(['category', 'requester.department'])
            ->whereNull('current_assignee_id')
            ->whereIn('status', ['OPEN', 'REOPENED'])
            ->orderBy('sla_deadline', 'asc')
            ->paginate(10, ['*'], 'queue_page');

        // Đếm số lượng ticket để hiện badge ở tab header
        $assignedCount = Ticket::where('current_assignee_id', $user->id)
            ->whereNotIn('status', ['CLOSED', 'RESOLVED'])
            ->count();

        $queueCount = Ticket::whereNull('current_assignee_id')
            ->whereIn('status', ['OPEN', 'REOPENED'])
            ->count();

        // Danh sách các KTV cùng nhóm để phục vụ Phân công Nội bộ Nhóm KTV
        $staffMembers = \App\Models\User::where('role', 'STAFF')
            ->where('is_active', true)
            ->with(['staffProfile', 'assignedTickets' => function ($q) {
                $q->whereIn('status', ['OPEN', 'IN_PROGRESS', 'REOPENED']);
            }])
            ->get();

        $otherStaffs = $staffMembers;

        return view('staff.workdesk.index', compact(
            'assignedTickets',
            'groupQueueTickets',
            'activeTab',
            'assignedCount',
            'queueCount',
            'staffMembers',
            'otherStaffs'
        ));
    }

    /**
     * UC09: Workdesk Dạng Thẻ Kanban 3 cột (Cần xử lý, Đang xử lý, Đã xong)
     */
    public function kanban()
    {
        $user = Auth::user();

        // 1. Cột Cần xử lý (OPEN / REOPENED) - Gồm ticket của tôi hoặc ticket chưa nhận
        $todoTickets = Ticket::with(['category', 'requester.department'])
            ->where(function ($q) use ($user) {
                $q->where('current_assignee_id', $user->id)
                  ->orWhereNull('current_assignee_id');
            })
            ->whereIn('status', ['OPEN', 'REOPENED'])
            ->orderBy('sla_deadline', 'asc')
            ->get();

        // 2. Cột Đang xử lý (IN_PROGRESS)
        $inProgressTickets = Ticket::with(['category', 'requester.department'])
            ->where('current_assignee_id', $user->id)
            ->where('status', 'IN_PROGRESS')
            ->orderBy('sla_deadline', 'asc')
            ->get();

        // 3. Cột Đã hoàn thành (RESOLVED / CLOSED)
        $doneTickets = Ticket::with(['category', 'requester.department'])
            ->where('current_assignee_id', $user->id)
            ->whereIn('status', ['RESOLVED', 'CLOSED'])
            ->latest('resolved_at')
            ->take(15)
            ->get();

        return view('staff.workdesk.kanban', compact(
            'todoTickets',
            'inProgressTickets',
            'doneTickets'
        ));
    }
}
