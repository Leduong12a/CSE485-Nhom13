<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentAttachment;
use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\TicketStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Chi tiết Ticket từ góc nhìn Kỹ thuật viên (UC07, UC08)
     */
    public function show(Ticket $ticket)
    {
        $ticket->load([
            'category',
            'requester.department',
            'currentAssignee.staffProfile',
            'attachments',
            'comments.user',
            'comments.attachments',
            'assignments.assignedByUser',
            'statusLogs.changedBy',
            'satisfactionSurvey',
        ]);

        // Lấy ghi chú phân công mới nhất từ Manager (nếu có)
        $latestAssignment = $ticket->assignments()->latest()->first();

        return view('staff.tickets.show', compact('ticket', 'latestAssignment'));
    }

    /**
     * 1-Click Tự nhận xử lý Ticket từ Hàng chờ Nhóm (Claim)
     */
    public function claim(Ticket $ticket)
    {
        $user = Auth::user();

        $oldStatus = $ticket->status;
        $ticket->current_assignee_id = $user->id;
        $ticket->status = 'IN_PROGRESS';
        $ticket->save();

        // Ghi lại Lịch sử Phân công
        TicketAssignment::create([
            'ticket_id'            => $ticket->id,
            'assigned_to_staff_id' => $user->id,
            'assigned_by_user_id'  => $user->id,
            'note'                 => 'Kỹ thuật viên chủ động nhận xử lý từ Hàng chờ Nhóm.',
            'assigned_at'          => now(),
        ]);

        // Ghi log chuyển trạng thái
        TicketStatusLog::create([
            'ticket_id'          => $ticket->id,
            'changed_by_user_id' => $user->id,
            'old_status'         => $oldStatus,
            'new_status'         => 'IN_PROGRESS',
        ]);

        return redirect()->route('staff.tickets.show', $ticket->id)
            ->with('success', 'Bạn đã tự nhận xử lý ticket thành công!');
    }

    /**
     * UC07, UC08: Cập nhật Trạng thái Ticket 1-Click ([▶ Bắt đầu], [✔ Đã khắc phục], [🔒 Đóng])
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:IN_PROGRESS,RESOLVED,CLOSED'],
        ]);

        $user = Auth::user();
        $oldStatus = $ticket->status;
        $newStatus = $validated['status'];

        if ($oldStatus === $newStatus) {
            return redirect()->back();
        }

        $ticket->status = $newStatus;

        // Cập nhật mốc thời gian hoàn thành
        if ($newStatus === 'RESOLVED' && ! $ticket->resolved_at) {
            $ticket->resolved_at = now();
        }

        if ($newStatus === 'CLOSED' && ! $ticket->closed_at) {
            $ticket->closed_at = now();
        }

        $ticket->save();

        // Ghi log nhật ký trạng thái
        TicketStatusLog::create([
            'ticket_id'          => $ticket->id,
            'changed_by_user_id' => $user->id,
            'old_status'         => $oldStatus,
            'new_status'         => $newStatus,
        ]);

        $statusLabels = [
            'IN_PROGRESS' => 'Đang xử lý',
            'RESOLVED'    => 'Đã khắc phục',
            'CLOSED'      => 'Đã đóng',
        ];

        return redirect()->back()
            ->with('success', "Đã chuyển trạng thái ticket sang '{$statusLabels[$newStatus]}'.");
    }

    /**
     * Trao đổi tin nhắn chat 2 chiều với Sinh viên / Giảng viên
     */
    public function addComment(Request $request, Ticket $ticket)
    {
        $request->validate([
            'content'       => ['required', 'string', 'max:1000'],
            'attachments.*' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf'],
        ], [
            'content.required' => 'Vui lòng nhập nội dung tin nhắn.',
        ]);

        $user = Auth::user();

        // Tạo Comment
        $comment = Comment::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $user->id,
            'content'     => $request->content,
            'is_internal' => false,
        ]);

        // Upload tệp đính kèm trong chat
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('comment_attachments', 'public');
                CommentAttachment::create([
                    'comment_id' => $comment->id,
                    'file_path'  => $path,
                    'file_type'  => $file->getClientMimeType(),
                ]);
            }
        }

        return redirect()->back()
            ->with('success', 'Đã gửi tin nhắn trao đổi.');
    }

    /**
     * Phân công / Chuyển giao ticket cho KTV khác trong Nhóm (Giao việc nội bộ)
     */
    public function reassign(Request $request, Ticket $ticket)
    {
        $request->validate([
            'staff_id' => ['required', 'exists:users,id'],
            'note'     => ['nullable', 'string', 'max:500'],
        ], [
            'staff_id.required' => 'Vui lòng chọn Kỹ thuật viên tiếp nhận.',
            'staff_id.exists'   => 'Kỹ thuật viên không hợp lệ.',
        ]);

        $targetStaff = \App\Models\User::where('id', $request->staff_id)->where('role', 'STAFF')->firstOrFail();
        $currentUser = Auth::user();

        $oldStatus = $ticket->status;
        $ticket->current_assignee_id = $targetStaff->id;

        if (in_array($ticket->status, ['OPEN', 'REOPENED'])) {
            $ticket->status = 'IN_PROGRESS';
        }

        $ticket->save();

        // Ghi lại Lịch sử Phân công
        TicketAssignment::create([
            'ticket_id'            => $ticket->id,
            'assigned_to_staff_id' => $targetStaff->id,
            'assigned_by_user_id'  => $currentUser->id,
            'note'                 => $request->note ?: "Phân công / Chuyển giao công việc nội bộ nhóm KTV.",
            'assigned_at'          => now(),
        ]);

        // Ghi log chuyển trạng thái nếu thay đổi
        if ($oldStatus !== $ticket->status) {
            TicketStatusLog::create([
                'ticket_id'          => $ticket->id,
                'changed_by_user_id' => $currentUser->id,
                'old_status'         => $oldStatus,
                'new_status'         => $ticket->status,
            ]);
        }

        return redirect()->back()
            ->with('success', "Đã phân công / chuyển giao ticket thành công cho KTV {$targetStaff->name}.");
    }
}
