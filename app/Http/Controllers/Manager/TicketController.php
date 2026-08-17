<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\StaffProfile;
use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\TicketCategory;
use App\Models\TicketStatusLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * UC10: Quản lý danh sách Ticket toàn trường
     */
    public function index(Request $request)
    {
        $query = Ticket::with(['category', 'requester.department', 'currentAssignee'])
            ->latest();

        // Lọc theo trạng thái
        if ($request->filled('status') && $request->status !== 'ALL') {
            $query->where('status', $request->status);
        }

        // Lọc theo mức ưu tiên
        if ($request->filled('priority') && $request->priority !== 'ALL') {
            $query->where('priority', $request->priority);
        }

        // Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $tickets = $query->paginate(15)->withQueryString();
        $categories = TicketCategory::orderBy('name')->get();
        $staffMembers = User::where('role', 'STAFF')->where('is_active', true)->get();

        return view('manager.tickets.index', compact('tickets', 'categories', 'staffMembers'));
    }

    /**
     * Chi tiết Ticket từ góc nhìn Quản lý
     */
    public function show(Ticket $ticket)
    {
        $ticket->load([
            'category',
            'requester.department',
            'currentAssignee.staffProfile',
            'attachments',
            'comments.user',
            'assignments.assignedToStaff',
            'assignments.assignedByUser',
            'statusLogs.changedBy',
            'satisfactionSurvey',
        ]);

        // Danh sách KTV khả dụng kèm thông tin ca trực và số ticket đang phụ trách
        $staffMembers = User::where('role', 'STAFF')
            ->where('is_active', true)
            ->with(['staffProfile', 'assignedTickets' => function ($q) {
                $q->whereIn('status', ['OPEN', 'IN_PROGRESS', 'REOPENED']);
            }])
            ->get();

        return view('manager.tickets.show', compact('ticket', 'staffMembers'));
    }

    /**
     * UC10, UC12: Phân công / Đổi Kỹ thuật viên phụ trách
     */
    public function assign(Request $request, Ticket $ticket)
    {
        $request->validate([
            'staff_id' => ['required', 'exists:users,id'],
            'note'     => ['nullable', 'string', 'max:500'],
        ], [
            'staff_id.required' => 'Vui lòng chọn Kỹ thuật viên phụ trách.',
            'staff_id.exists'   => 'Kỹ thuật viên không hợp lệ.',
        ]);

        $staff = User::where('id', $request->staff_id)->where('role', 'STAFF')->firstOrFail();
        $oldAssigneeId = $ticket->current_assignee_id;

        // Cập nhật current_assignee_id
        $ticket->current_assignee_id = $staff->id;

        // Nếu ticket đang OPEN, tự động chuyển sang IN_PROGRESS khi phân công
        $oldStatus = $ticket->status;
        if ($ticket->status === 'OPEN') {
            $ticket->status = 'IN_PROGRESS';
        }

        $ticket->save();

        // Ghi lại lịch sử phân công (ticket_assignments)
        TicketAssignment::create([
            'ticket_id'            => $ticket->id,
            'assigned_to_staff_id' => $staff->id,
            'assigned_by_user_id'  => Auth::id(),
            'note'                 => $request->note,
            'assigned_at'          => now(),
        ]);

        // Ghi log nếu trạng thái thay đổi
        if ($oldStatus !== $ticket->status) {
            TicketStatusLog::create([
                'ticket_id'          => $ticket->id,
                'changed_by_user_id' => Auth::id(),
                'old_status'         => $oldStatus,
                'new_status'         => $ticket->status,
            ]);
        }

        return redirect()->back()
            ->with('success', "Đã phân công thành công cho Kỹ thuật viên {$staff->name}.");
    }
}
